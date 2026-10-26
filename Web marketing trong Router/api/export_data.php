<?php
// File: api/export_data.php
session_start();
require_once '../config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin.php");
    exit;
}

$db = Database::getInstance();

// Lấy tất cả dữ liệu
$customers = $db->fetchAll("SELECT * FROM customers ORDER BY survey_time DESC");

// Tạo file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=mall_customers_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// UTF-8 BOM để Excel đọc tiếng Việt
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header
fputcsv($output, ['ID', 'Thời gian', 'Họ tên', 'Email', 'SĐT', 'Danh mục', 'Chi tiêu', 'Xu hướng', 'Góp ý', 'IP']);

// Dữ liệu
foreach ($customers as $row) {
    fputcsv($output, [
        $row['id'],
        $row['survey_time'],
        $row['fullname'],
        $row['email'],
        $row['phone'],
        $row['product_search'],
        $row['spending'],
        $row['trend'],
        $row['feedback'],
        $row['ip_address']
    ]);
}

fclose($output);
?>