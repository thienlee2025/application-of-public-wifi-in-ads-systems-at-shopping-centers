<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'shopping_mall');
if ($conn->connect_error)
    die(json_encode(['status' => 'error']));

$gas_value = isset($_POST['gas_value']) ? intval($_POST['gas_value']) : 0;
$location = isset($_POST['location']) ? $_POST['location'] : 'Zone_A';

// Lưu log
$sql = "INSERT INTO pccc_log (gas_value, location) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $gas_value, $location);
$stmt->execute();

// Kích hoạt chế độ khẩn cấp
$conn->query("UPDATE pccc_settings SET emergency_mode = TRUE, buzzer_override = NULL");

echo json_encode(['status' => 'success', 'emergency' => true]);
$conn->close();
?>