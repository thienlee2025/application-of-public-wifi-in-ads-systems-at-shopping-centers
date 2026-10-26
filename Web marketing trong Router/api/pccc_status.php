<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'shopping_mall');
if ($conn->connect_error)
    die(json_encode(['status' => 'error']));

// Lấy trạng thái khẩn cấp
$result = $conn->query("SELECT emergency_mode, buzzer_override FROM pccc_settings LIMIT 1");
$settings = $result->fetch_assoc();

// Lấy cảnh báo mới nhất
$alert = $conn->query("SELECT gas_value, alert_time FROM pccc_log WHERE status = 'active' ORDER BY id DESC LIMIT 1");
$latest = $alert->fetch_assoc();

echo json_encode([
    'emergency_mode' => (bool) $settings['emergency_mode'],
    'buzzer_override' => $settings['buzzer_override'],
    'latest_alert' => $latest ? [
        'gas_value' => $latest['gas_value'],
        'time' => $latest['alert_time']
    ] : null
]);
$conn->close();
?>