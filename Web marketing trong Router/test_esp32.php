<?php
// File: test_esp32.php
// Dùng để kiểm tra kết nối đến ESP32

$esp32IP = "192.168.4.1";

echo "<h2>🔌 Kiểm tra kết nối ESP32</h2>";

// Test ping
echo "<h3>1. Ping ESP32:</h3>";
exec("ping -n 1 " . $esp32IP, $output, $result);
if ($result === 0) {
    echo "<p style='color:green'>✅ Ping thành công!</p>";
} else {
    echo "<p style='color:red'>❌ Không thể ping ESP32</p>";
}

// Test gọi API authorize
echo "<h3>2. Test API authorize:</h3>";
$testIP = "192.168.4.100";
$url = "http://$esp32IP/authorize?client=" . urlencode($testIP);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "<p style='color:green'>✅ Gọi ESP32 thành công! Response: $response</p>";
} else {
    echo "<p style='color:red'>❌ Gọi ESP32 thất bại. HTTP Code: $httpCode</p>";
}

// Hướng dẫn
echo "<h3>📋 Hướng dẫn:</h3>";
echo "<ul>";
echo "<li>Đảm bảo PC đang kết nối WiFi <strong>Free_WiFi_Offer</strong></li>";
echo "<li>IP ESP32: <strong>$esp32IP</strong></li>";
echo "<li>IP PC: <strong>" . $_SERVER['SERVER_ADDR'] . "</strong></li>";
echo "</ul>";
?>