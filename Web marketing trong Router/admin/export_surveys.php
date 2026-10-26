<?php
require_once 'config.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=surveys_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

// Header
fputcsv($output, ['ID', 'Thời gian', 'Họ tên', 'Email', 'SĐT', 'Danh mục', 'Chi tiêu', 'Xu hướng', 'IP']);

$categoryNames = [
    'dientu' => 'Điện tử',
    'thoitrang' => 'Thời trang',
    'thucpham' => 'Thực phẩm',
    'giatri' => 'Giải trí',
    'suckhoe' => 'Sức khỏe',
    'dungcu' => 'Đồ gia dụng'
];

$spendingNames = [
    '<500k' => 'Dưới 500k',
    '500k-1m' => '500k - 1tr',
    '1m-3m' => '1tr - 3tr',
    '3m-5m' => '3tr - 5tr',
    '>5m' => 'Trên 5tr'
];

$result = $conn->query("SELECT * FROM customers ORDER BY survey_time DESC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['survey_time'],
        $row['fullname'],
        $row['email'],
        $row['phone'],
        $categoryNames[$row['product_search']] ?? $row['product_search'],
        $spendingNames[$row['spending']] ?? $row['spending'],
        $row['trend'],
        $row['ip_address']
    ]);
}

fclose($output);
?>