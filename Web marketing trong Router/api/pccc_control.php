<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'shopping_mall');
if ($conn->connect_error)
    die(json_encode(['status' => 'error']));

$action = $_GET['action'] ?? '';
$value = $_GET['value'] ?? null;

if ($action == 'reset_emergency') {
    $conn->query("UPDATE pccc_settings SET emergency_mode = FALSE, buzzer_override = NULL");
    $conn->query("UPDATE pccc_log SET status = 'resolved' WHERE status = 'active'");
    echo json_encode(['status' => 'success', 'message' => 'Đã tắt chế độ khẩn cấp']);
} elseif ($action == 'buzzer') {
    $buzzer_val = ($value == 'on') ? 1 : ($value == 'off' ? 0 : 'NULL');
    $conn->query("UPDATE pccc_settings SET buzzer_override = $buzzer_val");
    echo json_encode(['status' => 'success', 'buzzer' => $buzzer_val]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Action không hợp lệ']);
}
$conn->close();
?>