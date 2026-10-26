<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu hình hệ thống - THM MALL</title>
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

        .settings-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .settings-title {
            color: #e31b23;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffe5e5;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group input:focus {
            border-color: #e31b23;
            outline: none;
        }

        .save-btn {
            padding: 12px 30px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .save-btn:hover {
            background: #229954;
        }

        .info-text {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }

        .status-active {
            background: #27ae60;
            color: white;
        }

        .status-inactive {
            background: #e74c3c;
            color: white;
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

            .form-grid {
                grid-template-columns: 1fr;
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
            <li><a href="connections.php">📶 Kết nối</a></li>
            <li><a href="reports.php">📈 Báo cáo</a></li>
            <li><a href="settings.php" class="active">⚙️ Cấu hình</a></li>
            <li><a href="logout.php">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>⚙️ Cấu hình hệ thống</h1>
            <div>Xin chào, <?php echo $admin_name; ?> | <?php echo date('d/m/Y H:i'); ?></div>
        </div>

        <form method="POST" action="save_settings.php">
            <!-- CẤU HÌNH CHUNG -->
            <div class="settings-section">
                <div class="settings-title">🏢 Thông tin trung tâm</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tên trung tâm</label>
                        <input type="text" value="THM MALL" placeholder="Tên trung tâm thương mại">
                    </div>
                    <div class="form-group">
                        <label>Hotline</label>
                        <input type="text" value="1900 6868" placeholder="Số điện thoại">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="info@thmmall.vn" placeholder="Email liên hệ">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" value="www.thmmall.vn" placeholder="Địa chỉ website">
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text" value="123 Nguyễn Trãi, Q.1, TP.HCM" placeholder="Địa chỉ trung tâm">
                    </div>
                </div>
            </div>

            <!-- CẤU HÌNH WIFI -->
            <div class="settings-section">
                <div class="settings-title">📶 Cấu hình WiFi</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tên WiFi (SSID)</label>
                        <input type="text" value="Free_WiFi_Offer">
                        <div class="info-text">Tên mạng WiFi phát ra từ ESP32</div>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu WiFi</label>
                        <input type="text" value="" placeholder="Để trống nếu không có mật khẩu">
                    </div>
                    <div class="form-group">
                        <label>Thời gian kết nối tối đa</label>
                        <select>
                            <option>30 phút</option>
                            <option selected>60 phút</option>
                            <option>120 phút</option>
                            <option>Không giới hạn</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Giới hạn băng thông</label>
                        <select>
                            <option>Không giới hạn</option>
                            <option>1 Mbps</option>
                            <option>2 Mbps</option>
                            <option>5 Mbps</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CẤU HÌNH VOUCHER -->
            <div class="settings-section">
                <div class="settings-title">🎁 Cấu hình Voucher</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã voucher mặc định</label>
                        <input type="text" value="DHMT">
                        <div class="info-text">Tiền tố cho mã voucher tự động</div>
                    </div>
                    <div class="form-group">
                        <label>Giá trị giảm (%)</label>
                        <input type="number" value="20">
                    </div>
                    <div class="form-group">
                        <label>Điểm thưởng cho khảo sát</label>
                        <input type="number" value="10">
                    </div>
                    <div class="form-group">
                        <label>Thời hạn voucher (ngày)</label>
                        <input type="number" value="30">
                    </div>
                </div>
            </div>

            <!-- CẤU HÌNH ESP32 -->
            <div class="settings-section">
                <div class="settings-title">📟 Cấu hình ESP32</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Địa chỉ IP ESP32</label>
                        <input type="text" value="192.168.4.1" readonly>
                        <div class="info-text">Không thay đổi nếu không cần thiết</div>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <div>
                            <span class="status-badge status-active">🟢 Đang hoạt động</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Port</label>
                        <input type="text" value="80" readonly>
                    </div>
                    <div class="form-group">
                        <label>Firmware version</label>
                        <input type="text" value="v2.0.1" readonly>
                    </div>
                </div>
            </div>

            <!-- CẤU HÌNH EMAIL -->
            <div class="settings-section">
                <div class="settings-title">📧 Cấu hình Email</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>SMTP Server</label>
                        <input type="text" value="smtp.gmail.com">
                    </div>
                    <div class="form-group">
                        <label>Port</label>
                        <input type="text" value="587">
                    </div>
                    <div class="form-group">
                        <label>Email gửi</label>
                        <input type="email" value="noreply@thmmall.vn">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" value="********">
                    </div>
                </div>
            </div>

            <!-- CẤU HÌNH BẢO MẬT -->
            <div class="settings-section">
                <div class="settings-title">🔐 Cấu hình bảo mật</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Session timeout</label>
                        <select>
                            <option>30 phút</option>
                            <option selected>60 phút</option>
                            <option>120 phút</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số lần đăng nhập sai tối đa</label>
                        <input type="number" value="5">
                    </div>
                    <div class="form-group">
                        <label>Khóa tài khoản sau (phút)</label>
                        <input type="number" value="30">
                    </div>
                </div>
            </div>

            <button type="submit" class="save-btn">💾 Lưu cấu hình</button>
        </form>
    </div>
</body>

</html>