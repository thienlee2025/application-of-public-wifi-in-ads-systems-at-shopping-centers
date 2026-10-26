#include <WiFi.h>
#include <DNSServer.h>
#include <WebServer.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

// ===== CẤU HÌNH WiFi =====
const char *ssid = "Free_WiFi_Offer";
const char *password = NULL;
const IPAddress serverIP(192, 168, 4, 3); // IP PC Server

const byte DNS_PORT = 53;
DNSServer dnsServer;
WebServer webServer(80);
#define LED_BUILTIN 8

// ===== CẤU HÌNH MÀN HÌNH OLED =====
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_RESET -1
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

// Biến thời gian
unsigned long startTime = 0;
unsigned long lastDisplayUpdate = 0;

// ===== HÀM XỬ LÝ REDIRECT =====
void handleRedirect()
{
  String clientIP = webServer.client().remoteIP().toString();
  String redirectURL = "http://192.168.4.3/shopping-mall/index.php?client=" + clientIP;
  webServer.sendHeader("Location", redirectURL, true);
  webServer.send(302, "text/plain", "");
  Serial.println("Redirect to: " + redirectURL);
}

// ===== KHỞI TẠO MÀN HÌNH =====
void initOLED()
{
  Wire.begin(20, 21); // SDA=20, SCL=21 theo kết nối của bạn

  if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C))
  {
    Serial.println("OLED not found at 0x3C, trying 0x3D...");
    if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3D))
    {
      Serial.println("OLED init failed!");
      return;
    }
  }

  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.display();

  Serial.println("✅ OLED initialized");
  startTime = millis();
}

// ===== CẬP NHẬT MÀN HÌNH =====
void updateDisplay()
{
  display.clearDisplay();

  // Dòng 1: Tên WiFi
  display.setCursor(0, 0);
  display.print("WiFi: ");
  display.print(ssid);

  // Dòng 2: IP ESP32
  display.setCursor(0, 16);
  display.print("IP: ");
  display.print(WiFi.softAPIP());

  // Dòng 3: Số kết nối
  display.setCursor(0, 32);
  display.print("Devices: ");
  display.print(WiFi.softAPgetStationNum());

  // Dòng 4: Thời gian hoạt động
  display.setCursor(0, 48);
  display.print("Uptime: ");

  unsigned long seconds = (millis() - startTime) / 1000;
  int hours = seconds / 3600;
  int minutes = (seconds % 3600) / 60;
  int secs = seconds % 60;

  if (hours < 10)
    display.print("0");
  display.print(hours);
  display.print(":");
  if (minutes < 10)
    display.print("0");
  display.print(minutes);
  display.print(":");
  if (secs < 10)
    display.print("0");
  display.print(secs);

  display.display();
}

// ===== SETUP =====
void setup()
{
  Serial.begin(115200);
  while (!Serial)
  {
    delay(10);
  }

  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, HIGH);

  Serial.println("\n=== ESP32-C3 CAPTIVE PORTAL + OLED ===");

  // Khởi tạo màn hình
  initOLED();

  // Cấu hình WiFi AP
  WiFi.mode(WIFI_AP);
  WiFi.setTxPower(WIFI_POWER_8_5dBm);

  IPAddress localIP(192, 168, 4, 1);
  WiFi.softAPConfig(localIP, localIP, IPAddress(255, 255, 255, 0));

  if (WiFi.softAP(ssid, password))
  {
    Serial.println("✅ AP Started");
    digitalWrite(LED_BUILTIN, LOW);
  }

  // DNS chuyển hướng về ESP32
  dnsServer.start(DNS_PORT, "*", localIP);

  // Xử lý URL kiểm tra kết nối
  webServer.on("/generate_204", handleRedirect);
  webServer.on("/hotspot-detect.html", handleRedirect);
  webServer.on("/connecttest.txt", handleRedirect);

  // API ủy quyền từ PC
  webServer.on("/authorize", HTTP_GET, []()
               {
    if (webServer.hasArg("client")) {
      String clientIP = webServer.arg("client");
      Serial.println("✅ Authorized: " + clientIP);
      webServer.send(200, "text/plain", "OK");
    } else {
      webServer.send(400, "text/plain", "Missing client");
    } });

  // Xử lý mọi request khác
  webServer.onNotFound(handleRedirect);

  webServer.begin();
  Serial.println("✅ Web Server started");

  // Cập nhật màn hình lần đầu
  updateDisplay();
}

// ===== LOOP =====
void loop()
{
  dnsServer.processNextRequest();
  webServer.handleClient();

  // Cập nhật màn hình mỗi giây
  if (millis() - lastDisplayUpdate > 1000)
  {
    updateDisplay();
    lastDisplayUpdate = millis();
  }
}