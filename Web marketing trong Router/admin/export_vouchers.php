<?php
require_once 'config.php';

// Xuất file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=vouchers_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

// Header
fputcsv($output, ['ID', 'Mã voucher', 'Giá trị', 'Điểm yêu cầu', 'Số lượng', 'Đã dùng', 'Hạn dùng', 'Trạng thái']);

// Dữ liệu
$result = $conn->query("SELECT * FROM vouchers ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['code'],
        $row['value'] . '%',
        $row['points_required'],
        $row['quantity'],
        $row['used'],
        $row['expiry_date'],
        $row['status']
    ]);
}

fclose($output);
?>