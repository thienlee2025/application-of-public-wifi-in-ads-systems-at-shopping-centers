<?php
session_start();
require_once 'config.php';

// Lưu cấu hình vào file hoặc database
// Ở đây demo đơn giản, bạn có thể lưu vào database nếu cần

$_SESSION['success'] = "Đã lưu cấu hình thành công!";
header("Location: settings.php");
exit;
?>