<?php
session_start();
require_once '../config/database.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin.php");
    exit;
}

// Lấy thông tin admin
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Kết nối database
$db = Database::getInstance();
$conn = $db->getConnection();
?>