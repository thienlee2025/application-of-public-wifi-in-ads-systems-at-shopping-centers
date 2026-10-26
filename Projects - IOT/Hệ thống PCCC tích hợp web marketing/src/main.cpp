// ==========================================
// HỆ THỐNG PCCC - TÍCH HỢP TRUNG TÂM
// ESP32-C3 + MQ-2 + Buzzer + WiFi + API
// ==========================================

#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// ===== CẤU HÌNH WIFI =====
const char *ssid = "Free_WiFi_Offer";
const char *password = NULL;

// ===== CẤU HÌNH SERVER =====
const char *alertApi = "http://192.168.4.3/shopping-mall/api/pccc_alert.php";
const char *statusApi = "http://192.168.4.3/shopping-mall/api/pccc_status.php";

// ===== CẤU HÌNH CẢM BIẾN =====
#define MQ_PIN 0
#define BUZZER_PIN 2
#define THRESHOLD 1000 // mức ngưỡng cảnh báo cháy (có thể điều chỉnh theo môi trường thực tế)
#define ALARM_DURATION 10000

// ===== BIẾN TOÀN CỤC =====
int gasValue = 0;
bool fireDetected = false;
bool alarmActive = false;
unsigned long alarmStartTime = 0;
unsigned long lastStatusCheck = 0;
bool buzzerOverride = false; // true: ép buộc, false: tự động
bool buzzerState = false;    // Trạng thái còi khi ép buộc

// ===== HÀM KẾT NỐI WIFI =====
void connectWiFi()
{
  Serial.print("📡 Đang kết nối WiFi");
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20)
  {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED)
  {
    Serial.println("\n✅ WiFi đã kết nối!");
    Serial.print("🌐 IP: ");
    Serial.println(WiFi.localIP());
  }
  else
  {
    Serial.println("\n⚠️ Không thể kết nối WiFi! Chạy ở chế độ độc lập.");
  }
}

// ===== HÀM GỬI CẢNH BÁO LÊN SERVER =====
void sendAlert()
{
  if (WiFi.status() != WL_CONNECTED)
  {
    Serial.println("⚠️ Không có WiFi, không thể gửi cảnh báo!");
    return;
  }

  HTTPClient http;
  http.begin(alertApi);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postData = "gas_value=" + String(gasValue) + "&location=Zone_A";
  int httpCode = http.POST(postData);

  if (httpCode == 200)
  {
    Serial.println("✅ Đã gửi cảnh báo lên server!");
  }
  else
  {
    Serial.printf("❌ Gửi cảnh báo thất bại, HTTP Code: %d\n", httpCode);
  }
  http.end();
}

// ===== HÀM LẤY TRẠNG THÁI TỪ SERVER (HỖ TRỢ AUTO/MANUAL) =====
void checkServerCommand()
{
  if (WiFi.status() != WL_CONNECTED)
    return;

  HTTPClient http;
  http.begin(statusApi);
  int httpCode = http.GET();

  if (httpCode == 200)
  {
    String payload = http.getString();

    DynamicJsonDocument doc(256);
    deserializeJson(doc, payload);

    int buzzerOverrideVal = doc["buzzer_override"];
    String mode = doc["mode"] | "auto";

    if (mode == "manual")
    {
      // Chế độ MANUAL: Admin điều khiển hoàn toàn
      if (buzzerOverrideVal == 1)
      {
        buzzerOverride = true;
        buzzerState = true;
        Serial.println("📡 [MANUAL] BẬT CÒI theo lệnh admin");
      }
      else if (buzzerOverrideVal == 0)
      {
        buzzerOverride = true;
        buzzerState = false;
        Serial.println("📡 [MANUAL] TẮT CÒI theo lệnh admin");
      }
    }
    else
    {
      // Chế độ AUTO: Bỏ qua lệnh admin, còi tự động
      buzzerOverride = false;
      Serial.println("📡 [AUTO] Chế độ tự động");
    }
  }
  http.end();
}

// ===== HÀM ĐIỀU KHIỂN CÒI =====
void controlBuzzer()
{
  // Nếu đang ở chế độ MANUAL, ưu tiên lệnh từ admin
  if (buzzerOverride)
  {
    digitalWrite(BUZZER_PIN, buzzerState ? HIGH : LOW);
    return;
  }

  // Chế độ AUTO
  if (alarmActive)
  {
    static unsigned long lastToggle = 0;
    static bool buzzerOn = false;

    if (millis() - lastToggle > 200)
    {
      buzzerOn = !buzzerOn;
      digitalWrite(BUZZER_PIN, buzzerOn ? HIGH : LOW);
      lastToggle = millis();
    }

    if (millis() - alarmStartTime >= ALARM_DURATION)
    {
      alarmActive = false;
      digitalWrite(BUZZER_PIN, LOW);
      Serial.println("⏰ HẾT 10 GIÂY - TỰ ĐỘNG TẮT CÒI");
    }
  }
  else
  {
    digitalWrite(BUZZER_PIN, LOW);
  }
}

// ===== SETUP =====
void setup()
{
  Serial.begin(115200);
  delay(1000);

  Serial.println("\n======================================");
  Serial.println("🔥 HỆ THỐNG PCCC - TRUNG TÂM THƯƠNG MẠI");
  Serial.println("======================================");

  pinMode(MQ_PIN, INPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);

  connectWiFi();

  Serial.println("⏳ Đang khởi động cảm biến MQ-2...");
  for (int i = 20; i > 0; i--)
  {
    delay(1000);
    if (i % 5 == 0)
      Serial.print(".");
  }
  Serial.println("\n✅ Hệ thống sẵn sàng!");
  Serial.print("📊 Ngưỡng cảnh báo: ");
  Serial.println(THRESHOLD);
  Serial.println("======================================\n");
}

// ===== LOOP =====
void loop()
{
  static unsigned long lastRead = 0;
  if (millis() - lastRead > 300)
  {
    int sum = 0;
    for (int i = 0; i < 5; i++)
    {
      sum += analogRead(MQ_PIN);
      delay(10);
    }
    gasValue = sum / 5;

    Serial.print("📊 Nồng độ khói: ");
    Serial.print(gasValue);

    if (gasValue > THRESHOLD && !fireDetected)
    {
      fireDetected = true;
      alarmActive = true;
      alarmStartTime = millis();
      Serial.println(" ⚠️⚠️⚠️ PHÁT HIỆN CHÁY! ⚠️⚠️⚠️");
      sendAlert();
    }
    else if (gasValue <= THRESHOLD && fireDetected)
    {
      fireDetected = false;
      Serial.println(" ✅ HẾT KHÓI - AN TOÀN");
    }
    else if (gasValue > THRESHOLD)
    {
      Serial.println(" 🔴 ĐANG CHÁY - CÒI KÊU!");
    }
    else
    {
      Serial.println(" ✅ BÌNH THƯỜNG");
    }

    lastRead = millis();
  }

  if (millis() - lastStatusCheck > 5000)
  {
    checkServerCommand();
    lastStatusCheck = millis();
  }

  controlBuzzer();
  delay(50);
}