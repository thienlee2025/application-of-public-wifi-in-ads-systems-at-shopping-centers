<?php
require_once 'config.php';

$data = [
    'labels' => [],
    'values' => []
];

// Lấy dữ liệu 7 ngày gần nhất
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = $conn->query("SELECT COUNT(*) FROM customers WHERE DATE(survey_time) = '$date'")->fetch_row()[0];

    $data['labels'][] = date('d/m', strtotime($date));
    $data['values'][] = $count;
}

header('Content-Type: application/json');
echo json_encode($data);
?>