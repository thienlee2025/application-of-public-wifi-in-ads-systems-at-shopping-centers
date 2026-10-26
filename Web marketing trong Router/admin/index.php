<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - THM MALL</title>
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

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* ===== THÊM CSS CHO PCCC DASHBOARD ===== */
        .pccc-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .pccc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .pccc-header h3 {
            color: #e31b23;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #e74c3c;
            margin-bottom: 15px;
            animation: blinkRed 1s infinite;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #27ae60;
        }

        @keyframes blinkRed {
            0% {
                background: #f8d7da;
            }

            50% {
                background: #ffe0e3;
            }

            100% {
                background: #f8d7da;
            }
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-info {
            background: #3498db;
            color: white;
        }

        .btn-info:hover {
            background: #2980b9;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>THM ADMIN</h2>
        <ul>
            <li><a href="index.php" class="active">📊 Dashboard</a></li>
            <li><a href="surveys.php">📝 Khảo sát</a></li>
            <li><a href="members.php">👥 Hội viên</a></li>
            <li><a href="vouchers.php">🎁 Voucher</a></li>
            <li><a href="products.php">📦 Sản phẩm</a></li>
            <li><a href="connections.php">📶 Kết nối</a></li>
            <li><a href="reports.php">📈 Báo cáo</a></li>
            <li><a href="settings.php">⚙️ Cấu hình</a></li>
            <li><a href="logout.php">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>Dashboard</h1>
            <div>Xin chào,
                <?php echo $admin_name; ?> |
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            // Lấy thống kê
            $today = $conn->query("SELECT COUNT(*) FROM customers WHERE DATE(survey_time) = CURDATE()")->fetch_row()[0];
            $total = $conn->query("SELECT COUNT(*) FROM customers")->fetch_row()[0];
            $members = $conn->query("SELECT COUNT(*) FROM members")->fetch_row()[0];
            $vouchers = $conn->query("SELECT COUNT(*) FROM vouchers WHERE status='active'")->fetch_row()[0] ?? 0;
            ?>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $today; ?>
                </div>
                <div class="stat-label">Khảo sát hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $total; ?>
                </div>
                <div class="stat-label">Tổng khảo sát</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $members; ?>
                </div>
                <div class="stat-label">Hội viên</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $vouchers; ?>
                </div>
                <div class="stat-label">Voucher còn hiệu lực</div>
            </div>
        </div>

        <!-- ===== THÊM DASHBOARD PCCC ===== -->
        <div class="pccc-card" id="pcccCard">
            <div class="pccc-header">
                <h3>🔥 HỆ THỐNG PCCC</h3>
                <button class="btn btn-info" id="refreshPCCC">🔄 Làm mới</button>
            </div>
            <div id="pcccStatus">
                <p>Đang tải trạng thái...</p>
            </div>
            <div class="btn-group">
                <button id="buzzerOn" class="btn btn-danger">🔊 BẬT CÒI</button>
                <button id="buzzerOff" class="btn btn-secondary">🔇 TẮT CÒI</button>
                <button id="resetEmergency" class="btn btn-success">✅ TẮT CHẾ ĐỘ KHẨN CẤP</button>
            </div>
        </div>

        <div class="chart-container">
            <h3>📊 Biểu đồ khảo sát 7 ngày gần nhất</h3>
            <canvas id="surveyChart" style="width:100%; height:300px;"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ===== VẼ BIỂU ĐỒ KHẢO SÁT =====
        fetch('get_chart_data.php')
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('surveyChart'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Số lượt khảo sát',
                            data: data.values,
                            borderColor: '#e31b23',
                            backgroundColor: 'rgba(227,27,35,0.1)',
                            tension: 0.4
                        }]
                    }
                });
            });

        // ===== HÀM CẬP NHẬT TRẠNG THÁI PCCC =====
        function updatePCCCStatus() {
            fetch('/shopping-mall/api/pccc_status.php')
                .then(res => res.json())
                .then(data => {
                    let html = '';

                    if (data.emergency_mode) {
                        html += '<div class="alert-danger">';
                        html += '<strong>⚠️⚠️⚠️ ĐANG TRONG CHẾ ĐỘ KHẨN CẤP! ⚠️⚠️⚠️</strong><br>';
                        html += 'Hệ thống đã phát hiện sự cố cháy nổ. Vui lòng kiểm tra ngay!';
                        html += '</div>';

                        if (data.latest_alert) {
                            html += '<div style="margin-top:15px; padding:10px; background:#f9f9f9; border-radius:5px;">';
                            html += '<strong>📊 Thông tin cảnh báo gần nhất:</strong><br>';
                            html += '📍 Khu vực: Trung tâm<br>';
                            html += '🔥 Giá trị khói: <strong>' + data.latest_alert.gas_value + '</strong><br>';
                            html += '⏰ Thời gian: ' + data.latest_alert.time;
                            html += '</div>';
                        }
                    } else {
                        html += '<div class="alert-success">';
                        html += '<strong>✅ HỆ THỐNG AN TOÀN</strong><br>';
                        html += 'Không phát hiện bất thường về cháy nổ.';
                        html += '</div>';
                    }

                    // Trạng thái còi
                    let buzzerText = '🤖 Tự động';
                    if (data.buzzer_override === 1) buzzerText = '🔊 ĐANG BẬT (ép buộc)';
                    else if (data.buzzer_override === 0) buzzerText = '🔇 ĐANG TẮT (ép buộc)';
                    html += '<div style="margin-top:10px;">🔔 <strong>Trạng thái còi báo:</strong> ' + buzzerText + '</div>';

                    document.getElementById('pcccStatus').innerHTML = html;
                })
                .catch(err => {
                    console.log('Lỗi:', err);
                    document.getElementById('pcccStatus').innerHTML = '<div class="alert-danger">❌ Không thể kết nối đến máy chủ</div>';
                });
        }

        // ===== ĐIỀU KHIỂN PCCC =====
        function controlPCCC(action, value = null) {
            let url = '/shopping-mall/api/pccc_control.php?action=' + action;
            if (value) url += '&value=' + value;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        updatePCCCStatus();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không xác định'));
                    }
                })
                .catch(err => alert('Không thể kết nối đến máy chủ'));
        }

        // ===== GÁN SỰ KIỆN CHO NÚT =====
        document.getElementById('buzzerOn')?.addEventListener('click', () => controlPCCC('buzzer', 'on'));
        document.getElementById('buzzerOff')?.addEventListener('click', () => controlPCCC('buzzer', 'off'));
        document.getElementById('resetEmergency')?.addEventListener('click', () => {
            if (confirm('Bạn có chắc muốn tắt chế độ khẩn cấp? Hệ thống sẽ trở lại bình thường.')) {
                controlPCCC('reset_emergency');
            }
        });
        document.getElementById('refreshPCCC')?.addEventListener('click', () => updatePCCCStatus());

        // ===== TỰ ĐỘNG CẬP NHẬT MỖI 10 GIÂY =====
        updatePCCCStatus();
        setInterval(updatePCCCStatus, 10000);
    </script>
</body>

</html>