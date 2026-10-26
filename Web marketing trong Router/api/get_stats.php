<?php
// File: api/get_stats.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';

try {
    $db = Database::getInstance();

    // Lấy tham số
    $filter = $_GET['filter'] ?? 'today';
    $response = ['success' => true];

    switch ($filter) {
        case 'realtime':
            // Thống kê thời gian thực (30 phút gần nhất)
            $stats = $db->fetchAll("SELECT 
                DATE_FORMAT(survey_time, '%H:%i') as time,
                COUNT(*) as count
                FROM customers 
                WHERE survey_time >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
                GROUP BY DATE_FORMAT(survey_time, '%H:%i')
                ORDER BY time");

            $response['data'] = [
                'labels' => array_column($stats, 'time'),
                'values' => array_column($stats, 'count')
            ];
            break;

        case 'today':
            // Thống kê hôm nay
            $stats = $db->fetchOne("SELECT 
                COUNT(*) as total_today,
                COUNT(DISTINCT email) as unique_customers,
                SUM(CASE WHEN trend = 'online' THEN 1 ELSE 0 END) as online_count,
                SUM(CASE WHEN trend = 'offline' THEN 1 ELSE 0 END) as offline_count
                FROM customers 
                WHERE DATE(survey_time) = CURDATE()");

            $response['data'] = $stats;
            break;

        case 'week':
            // Thống kê 7 ngày
            $stats = $db->fetchAll("SELECT 
                DATE(survey_time) as date,
                COUNT(*) as total,
                SUM(CASE WHEN trend = 'online' THEN 1 ELSE 0 END) as online,
                SUM(CASE WHEN trend = 'offline' THEN 1 ELSE 0 END) as offline
                FROM customers 
                WHERE survey_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(survey_time)
                ORDER BY date");

            $response['data'] = [
                'labels' => array_column($stats, 'date'),
                'total' => array_column($stats, 'total'),
                'online' => array_column($stats, 'online'),
                'offline' => array_column($stats, 'offline')
            ];
            break;

        case 'categories':
            // Thống kê theo danh mục
            $stats = $db->fetchAll("SELECT 
                product_search,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM customers), 1) as percentage
                FROM customers 
                GROUP BY product_search 
                ORDER BY count DESC");

            $categoryNames = [
                'dientu' => 'Điện tử',
                'thoitrang' => 'Thời trang',
                'thucpham' => 'Thực phẩm',
                'giatri' => 'Giải trí',
                'suckhoe' => 'Sức khỏe',
                'dungcu' => 'Đồ gia dụng'
            ];

            $formatted = [];
            foreach ($stats as $row) {
                $formatted[] = [
                    'name' => $categoryNames[$row['product_search']] ?? $row['product_search'],
                    'count' => $row['count'],
                    'percentage' => $row['percentage']
                ];
            }

            $response['data'] = $formatted;
            break;

        case 'trends':
            // Phân tích xu hướng
            $stats = $db->fetchAll("SELECT 
                trend,
                COUNT(*) as count,
                AVG(CASE 
                    WHEN spending = '<500k' THEN 0.3
                    WHEN spending = '500k-1m' THEN 0.75
                    WHEN spending = '1m-3m' THEN 2
                    WHEN spending = '3m-5m' THEN 4
                    WHEN spending = '>5m' THEN 6
                    ELSE 0 END) as avg_spending
                FROM customers 
                GROUP BY trend");

            $response['data'] = $stats;
            break;

        case 'spending':
            // Phân bố chi tiêu
            $stats = $db->fetchAll("SELECT 
                spending,
                COUNT(*) as count,
                ROUND(AVG(CAST(
                    CASE 
                        WHEN spending = '<500k' THEN 300000
                        WHEN spending = '500k-1m' THEN 750000
                        WHEN spending = '1m-3m' THEN 2000000
                        WHEN spending = '3m-5m' THEN 4000000
                        WHEN spending = '>5m' THEN 6000000
                    END AS DECIMAL(10,2)
                ))) as avg_amount
                FROM customers 
                GROUP BY spending
                ORDER BY FIELD(spending, '<500k', '500k-1m', '1m-3m', '3m-5m', '>5m')");

            $spendingNames = [
                '<500k' => 'Dưới 500k',
                '500k-1m' => '500k - 1 triệu',
                '1m-3m' => '1 - 3 triệu',
                '3m-5m' => '3 - 5 triệu',
                '>5m' => 'Trên 5 triệu'
            ];

            $formatted = [];
            foreach ($stats as $row) {
                $formatted[] = [
                    'range' => $spendingNames[$row['spending']] ?? $row['spending'],
                    'count' => $row['count'],
                    'avg_amount' => $row['avg_amount']
                ];
            }

            $response['data'] = $formatted;
            break;

        case 'summary':
            // Tổng hợp tất cả
            $response['data'] = [
                'total' => $db->fetchOne("SELECT COUNT(*) as total FROM customers")['total'],
                'today' => $db->fetchOne("SELECT COUNT(*) as today FROM customers WHERE DATE(survey_time) = CURDATE()")['today'],
                'unique_emails' => $db->fetchOne("SELECT COUNT(DISTINCT email) as unique_emails FROM customers")['unique_emails'],
                'avg_per_day' => round($db->fetchOne("SELECT AVG(daily) as avg FROM (SELECT COUNT(*) as daily FROM customers GROUP BY DATE(survey_time)) as t")['avg'] ?? 0, 1),
                'top_category' => $db->fetchOne("SELECT 
                    product_search,
                    COUNT(*) as count 
                    FROM customers 
                    GROUP BY product_search 
                    ORDER BY count DESC 
                    LIMIT 1")['product_search'] ?? 'N/A',
                'online_rate' => round($db->fetchOne("SELECT 
                    (SUM(CASE WHEN trend = 'online' OR trend = 'both' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as rate 
                    FROM customers")['rate'] ?? 0, 1)
            ];
            break;

        default:
            throw new Exception('Invalid filter parameter');
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>