<?php
// Lấy IP client từ URL (do ESP32 chuyển hướng)
$client_ip = $_GET['client'] ?? '';

// Gọi ESP32 để ủy quyền (nếu có)
if (!empty($client_ip)) {
    $esp32IP = "192.168.4.1";
    $url = "http://$esp32IP/authorize?client=" . urlencode($client_ip);

    // Gọi bằng file_get_contents (đơn giản)
    @file_get_contents($url);
}

// Chuyển thẳng đến thanks.html (KHÔNG voucher)
header("Location: thanks.html?voucher=false");
exit;
?>