<?php
require_once 'config.php';

// Lấy thống kê tổng quan
$total_surveys = 0;
$total_members = 0;
$total_vouchers = 0;
$today_surveys = 0;

$result = $conn->query("SELECT COUNT(*) FROM customers");
if ($result) {
    $total_surveys = $result->fetch_row()[0];
}

$result = $conn->query("SELECT COUNT(*) FROM members");
if ($result) {
    $total_members = $result->fetch_row()[0];
}

$result = $conn->query("SELECT SUM(used) FROM vouchers");
if ($result) {
    $total_vouchers = $result->fetch_row()[0] ?? 0;
}

$result = $conn->query("SELECT COUNT(*) FROM customers WHERE DATE(survey_time) = CURDATE()");
if ($result) {
    $today_surveys = $result->fetch_row()[0];
}

// Dữ liệu cho biểu đồ 7 ngày
$chart_labels = [];
$chart_values = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = 0;
    $result = $conn->query("SELECT COUNT(*) FROM customers WHERE DATE(survey_time) = '$date'");
    if ($result) {
        $count = $result->fetch_row()[0];
    }
    $chart_labels[] = date('d/m', strtotime($date));
    $chart_values[] = $count;
}

// Dữ liệu danh mục
$categories = ['dientu', 'thoitrang', 'thucpham', 'giatri', 'suckhoe', 'dungcu'];
$cat_names = [
    'dientu' => '📱 Điện tử',
    'thoitrang' => '👕 Thời trang',
    'thucpham' => '🥘 Thực phẩm',
    'giatri' => '🎬 Giải trí',
    'suckhoe' => '💊 Sức khỏe',
    'dungcu' => '🛠️ Đồ gia dụng'
];
$cat_values = [];
foreach ($categories as $cat) {
    $result = $conn->query("SELECT COUNT(*) FROM customers WHERE product_search = '$cat'");
    $cat_values[] = $result ? $result->fetch_row()[0] : 0;
}

// Dữ liệu xu hướng giờ
$hours_labels = ['8-10h', '10-12h', '12-14h', '14-16h', '16-18h', '18-20h', '20-22h'];
$hours_values = [0, 0, 0, 0, 0, 0, 0];
for ($h = 8; $h <= 21; $h++) {
    $hour_start = $h;
    $hour_end = $h + 1;
    $idx = floor(($h - 8) / 2);
    if ($idx >= 0 && $idx < 7) {
        $result = $conn->query("SELECT COUNT(*) FROM customers WHERE HOUR(survey_time) >= $hour_start AND HOUR(survey_time) < $hour_end");
        if ($result) {
            $hours_values[$idx] += $result->fetch_row()[0];
        }
    }
}

// Dữ liệu chi tiêu
$spending_labels = ['<500k', '500k-1tr', '1-3tr', '3-5tr', '>5tr'];
$spending_values = [];
foreach ($spending_labels as $spend) {
    $result = $conn->query("SELECT COUNT(*) FROM customers WHERE spending = '$spend'");
    $spending_values[] = $result ? $result->fetch_row()[0] : 0;
}

// Dữ liệu báo cáo ngày
$daily_report = [];
for ($i = 7; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $row = [
        'date' => $date,
        'total' => 0,
        'online' => 0,
        'offline' => 0,
        'both' => 0,
        'dientu' => 0,
        'thoitrang' => 0,
        'thucpham' => 0
    ];

    $result = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN trend='online' THEN 1 ELSE 0 END) as online,
        SUM(CASE WHEN trend='offline' THEN 1 ELSE 0 END) as offline,
        SUM(CASE WHEN trend='both' THEN 1 ELSE 0 END) as both,
        SUM(CASE WHEN product_search='dientu' THEN 1 ELSE 0 END) as dientu,
        SUM(CASE WHEN product_search='thoitrang' THEN 1 ELSE 0 END) as thoitrang,
        SUM(CASE WHEN product_search='thucpham' THEN 1 ELSE 0 END) as thucpham
        FROM customers WHERE DATE(survey_time) = '$date'");

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $row['total'] = $data['total'] ?? 0;
        $row['online'] = $data['online'] ?? 0;
        $row['offline'] = $data['offline'] ?? 0;
        $row['both'] = $data['both'] ?? 0;
        $row['dientu'] = $data['dientu'] ?? 0;
        $row['thoitrang'] = $data['thoitrang'] ?? 0;
        $row['thucpham'] = $data['thucpham'] ?? 0;
    }
    $daily_report[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo thống kê - THM MALL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #e31b23, #b1141b);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #e31b23;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }

        .chart-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chart-card h3 {
            margin-bottom: 15px;
            color: #e31b23;
        }

        .summary-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #e31b23;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        canvas {
            max-height: 300px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }

            .chart-row {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="sidebar">
        <h2>THM ADMIN</h2>
        <ul>
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="surveys.php">📝 Khảo sát</a></li>
            <li><a href="members.php">👥 Hội viên</a></li>
            <li><a href="vouchers.php">🎁 Voucher</a></li>
            <li><a href="products.php">📦 Sản phẩm</a></li>
            <li><a href="connections.php">📶 Kết nối</a></li>
            <li><a href="reports.php" class="active">📈 Báo cáo</a></li>
            <li><a href="settings.php">⚙️ Cấu hình</a></li>
            <li><a href="logout.php">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>📈 Báo cáo thống kê</h1>
            <div>Xin chào, <?php echo $admin_name; ?> | <?php echo date('d/m/Y H:i'); ?></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_surveys; ?></div>
                <div class="stat-label">Tổng khảo sát</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_members; ?></div>
                <div class="stat-label">Hội viên</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_vouchers; ?></div>
                <div class="stat-label">Voucher đã dùng</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $today_surveys; ?></div>
                <div class="stat-label">Hôm nay</div>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-card">
                <h3>📊 Thống kê khảo sát 7 ngày</h3>
                <canvas id="weeklyChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>🥧 Phân bố danh mục</h3>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-card">
                <h3>⏰ Xu hướng theo giờ</h3>
                <canvas id="hourlyChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>💰 Phân bố chi tiêu</h3>
                <canvas id="spendingChart"></canvas>
            </div>
        </div>

        <div class="summary-table">
            <h3 style="margin-bottom: 15px;">📋 Báo cáo chi tiết theo ngày</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Số khảo sát</th>
                        <th>Online</th>
                        <th>Offline</th>
                        <th>Cả hai</th>
                        <th>Điện tử</th>
                        <th>Thời trang</th>
                        <th>Thực phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily_report as $row): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                            <td><?php echo $row['total']; ?></td>
                            <td><?php echo $row['online']; ?></td>
                            <td><?php echo $row['offline']; ?></td>
                            <td><?php echo $row['both']; ?></td>
                            <td><?php echo $row['dientu']; ?></td>
                            <td><?php echo $row['thoitrang']; ?></td>
                            <td><?php echo $row['thucpham']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Biểu đồ tuần
        new Chart(document.getElementById('weeklyChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Số khảo sát',
                    data: <?php echo json_encode($chart_values); ?>,
                    borderColor: '#e31b23',
                    backgroundColor: 'rgba(227,27,35,0.1)',
                    tension: 0.4
                }]
            }
        });

        // Biểu đồ danh mục
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_values($cat_names)); ?>,
                datasets: [{
                    data: <?php echo json_encode($cat_values); ?>,
                    backgroundColor: ['#e31b23', '#3498db', '#27ae60', '#f39c12', '#9b59b6', '#1abc9c']
                }]
            }
        });

        // Biểu đồ giờ
        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($hours_labels); ?>,
                datasets: [{
                    label: 'Lượt khảo sát',
                    data: <?php echo json_encode($hours_values); ?>,
                    backgroundColor: '#e31b23'
                }]
            }
        });

        // Biểu đồ chi tiêu
        new Chart(document.getElementById('spendingChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($spending_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($spending_values); ?>,
                    backgroundColor: ['#e31b23', '#3498db', '#27ae60', '#f39c12', '#9b59b6']
                }]
            }
        });
    </script>
</body>

</html>