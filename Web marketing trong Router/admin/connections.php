<?php
require_once 'config.php';

// ===== HÀM LẤY TRẠNG THÁI ESP32 =====
function getESP32Status()
{
    // Kiểm tra ESP32 có online không bằng ping
    $ping = exec("ping -n 1 192.168.4.1 2>nul", $output, $result);
    $online = ($result == 0);

    return [
        'online' => $online,
        'ssid' => 'Free_WiFi_Offer',
        'ip' => '192.168.4.1'
    ];
}

// ===== LẤY DANH SÁCH IP ĐANG KẾT NỐI THỰC TẾ =====
// Lấy các IP đã khảo sát trong 30 phút gần đây (coi như đang kết nối)
function getConnectedDevices()
{
    global $conn;

    $sql = "SELECT 
                ip_address, 
                COUNT(*) as survey_count,
                MAX(survey_time) as last_survey
            FROM customers 
            WHERE ip_address IS NOT NULL 
              AND ip_address != '' 
              AND ip_address != '::1'
              AND survey_time >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
            GROUP BY ip_address 
            ORDER BY last_survey DESC";

    $result = $conn->query($sql);
    $devices = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $devices[] = [
                'ip' => $row['ip_address'],
                'last_time' => $row['last_survey'],
                'survey_count' => $row['survey_count']
            ];
        }
    }

    return $devices;
}

// ===== LẤY DỮ LIỆU =====
$esp32_status = getESP32Status();
$connected_devices = getConnectedDevices();

// Thống kê
$total_surveys = $conn->query("SELECT COUNT(*) FROM customers")->fetch_row()[0];
$today_surveys = $conn->query("SELECT COUNT(*) FROM customers WHERE DATE(survey_time) = CURDATE()")->fetch_row()[0];
$total_connected = count($connected_devices);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý kết nối - THM MALL</title>
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

        .status-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .status-led {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #27ae60;
            box-shadow: 0 0 10px #27ae60;
            animation: pulse 1.5s infinite;
        }

        .status-led.off {
            background: #e74c3c;
            box-shadow: 0 0 10px #e74c3c;
            animation: none;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .info-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 28px;
            font-weight: 700;
            color: #e31b23;
        }

        .table-container {
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

        .device-count {
            background: #e31b23;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 5px;
        }

        .action-btn {
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background: #e74c3c;
            color: white;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: #c0392b;
        }

        .refresh-btn {
            background: #3498db;
            margin-left: 10px;
        }

        .refresh-btn:hover {
            background: #2980b9;
        }

        code {
            background: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }

        .ip-address {
            font-family: monospace;
            font-weight: 600;
            color: #2c3e50;
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

            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
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
            <li><a href="connections.php" class="active">📶 Kết nối</a></li>
            <li><a href="reports.php">📈 Báo cáo</a></li>
            <li><a href="settings.php">⚙️ Cấu hình</a></li>
            <li><a href="logout.php">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>📶 Quản lý kết nối WiFi</h1>
            <div>
                Xin chào, <?php echo $admin_name; ?> | <?php echo date('d/m/Y H:i:s'); ?>
                <button class="action-btn refresh-btn" onclick="location.reload()">🔄 Làm mới</button>
            </div>
        </div>

        <!-- ESP32 STATUS -->
        <div class="status-card">
            <div class="status-led <?php echo $esp32_status['online'] ? '' : 'off'; ?>"></div>
            <div>
                <h3>ESP32-C3 Super Mini</h3>
                <p>Trạng thái:
                    <strong style="color: <?php echo $esp32_status['online'] ? '#27ae60' : '#e74c3c'; ?>;">
                        <?php echo $esp32_status['online'] ? '🟢 Đang hoạt động' : '🔴 Không kết nối'; ?>
                    </strong>
                </p>
            </div>
            <div>
                <p>📶 SSID: <strong><?php echo $esp32_status['ssid']; ?></strong></p>
                <p>🌐 IP: <strong><?php echo $esp32_status['ip']; ?></strong></p>
            </div>
            <div>
                <button class="action-btn" onclick="restartESP32()">🔄 Khởi động lại</button>
                <button class="action-btn" onclick="testConnection()">📡 Test kết nối</button>
            </div>
        </div>

        <!-- STATISTICS -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">📱 Đang kết nối</div>
                <div class="info-value"><?php echo $total_connected; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">✅ Đã ủy quyền</div>
                <div class="info-value"><?php echo $total_connected; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">📊 Tổng khảo sát</div>
                <div class="info-value"><?php echo $total_surveys; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">📅 Khảo sát hôm nay</div>
                <div class="info-value"><?php echo $today_surveys; ?></div>
            </div>
        </div>

        <!-- CONNECTED DEVICES TABLE -->
        <div class="table-container">
            <h3 style="margin-bottom: 15px;">
                📱 Thiết bị đang kết nối (30 phút gần nhất)
                <span class="device-count"><?php echo $total_connected; ?> thiết bị</span>
            </h3>

            <?php if (empty($connected_devices)): ?>
                <p style="text-align: center; padding: 40px; color: #999;">
                    🔍 Không có thiết bị nào đang kết nối.
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Địa chỉ IP</th>
                            <th>Thời gian kết nối gần nhất</th>
                            <th>Số lần khảo sát</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = 1;
                        foreach ($connected_devices as $device):
                            $ip = $device['ip'];
                            $last_time = $device['last_time'];
                            $survey_count = $device['survey_count'];
                            $time_str = date('H:i:s d/m/Y', strtotime($last_time));
                            ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><code class="ip-address"><?php echo htmlspecialchars($ip); ?></code></td>
                                <td><?php echo $time_str; ?></td>
                                <td><?php echo $survey_count; ?> lượt</td>
                                <td>
                                    <button class="action-btn" onclick="disconnectDevice('<?php echo $ip; ?>')">❌ Ngắt kết
                                        nối</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function restartESP32() {
            if (confirm('Khởi động lại ESP32? Thiết bị sẽ mất kết nối tạm thời.')) {
                fetch('http://192.168.4.1/restart', { method: 'POST', mode: 'no-cors' })
                    .catch(e => console.log('ESP32 restart command sent'));
                alert('Đã gửi lệnh khởi động lại ESP32');
                setTimeout(() => location.reload(), 3000);
            }
        }

        function testConnection() {
            fetch('http://192.168.4.1/', { method: 'GET', mode: 'no-cors' })
                .catch(e => console.log('ESP32 ping sent'));
            alert('Đã gửi lệnh test kết nối đến ESP32');
        }

        function disconnectDevice(ip) {
            if (confirm('Ngắt kết nối thiết bị ' + ip + '?')) {
                fetch('http://192.168.4.1/disconnect?client=' + encodeURIComponent(ip))
                    .catch(e => console.log('Disconnect sent'));
                alert('Đã yêu cầu ngắt kết nối ' + ip);
                setTimeout(() => location.reload(), 2000);
            }
        }
    </script>
</body>

</html>