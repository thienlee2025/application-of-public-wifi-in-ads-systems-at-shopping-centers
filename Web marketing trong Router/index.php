<?php
session_start();

// ===== KIỂM TRA CHẾ ĐỘ KHẨN CẤP (PCCC) =====
$emergency_mode = false;
$conn = new mysqli('localhost', 'root', '', 'shopping_mall');
if (!$conn->connect_error) {
    $result = $conn->query("SELECT emergency_mode FROM pccc_settings LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $emergency_mode = (bool)$row['emergency_mode'];
    }
    $conn->close();
}

// Nếu đang trong chế độ khẩn cấp, chuyển hướng đến trang cảnh báo
if ($emergency_mode) {
    header("Location: emergency.php");
    exit;
}
// ===== KẾT THÚC KIỂM TRA =====
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THM MALL - WiFi & Khảo sát</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-red: #e31b23;
            --dark-red: #b1141b;
            --gold: #ffd700;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(227, 27, 35, 0.3);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            font-size: 48px;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.2));
        }

        .logo-text h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .logo-text p {
            font-size: 14px;
            opacity: 0.9;
        }

        .hotline {
            background: rgba(255,255,255,0.15);
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .hotline i {
            margin-right: 8px;
        }

        /* HERO BANNER */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100"><rect width="100" height="100" fill="%23e31b23"/><circle cx="50" cy="50" r="30" fill="%23ffd700" opacity="0.2"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 20px;
            text-align: center;
        }

        .hero-content {
            max-width: 900px;
            margin: 0 auto;
        }

        .hero h2 {
            font-size: 56px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: slideInDown 1s ease;
        }

        .hero p {
            font-size: 24px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .hero-badges {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .badge {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 60px;
            border: 2px solid var(--gold);
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* WELCOME SECTION */
        .welcome {
            max-width: 1200px;
            margin: -60px auto 40px;
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(227, 27, 35, 0.15);
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .welcome-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-title h3 {
            color: var(--primary-red);
            font-size: 32px;
            font-weight: 700;
        }

        .welcome-title p {
            color: #666;
            font-size: 18px;
            margin-top: 10px;
        }

        /* BEST SELLER */
        .best-seller {
            margin: 50px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h3 {
            color: var(--primary-red);
            font-size: 36px;
            font-weight: 700;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }

        .section-title h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gold);
            border-radius: 2px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border: 1px solid #ffe5e5;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(227, 27, 35, 0.2);
        }

        .product-image {
            height: 200px;
            background: linear-gradient(45deg, #ffe5e5, #fff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            border-bottom: 3px solid var(--gold);
        }

        .product-info {
            padding: 20px;
            text-align: center;
        }

        .product-info h4 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark-gray);
        }

        .product-price {
            color: var(--primary-red);
            font-size: 20px;
            font-weight: 700;
        }

        .product-price del {
            color: #999;
            font-size: 14px;
            margin-left: 8px;
            font-weight: normal;
        }

        /* AWARDS SECTION */
        .awards {
            background: linear-gradient(135deg, #fff5f5, #ffe5e5);
            border-radius: 30px;
            padding: 50px;
            margin: 50px 0;
        }

        .awards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            text-align: center;
        }

        .award-item {
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .award-icon {
            font-size: 48px;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .award-item h4 {
            color: var(--primary-red);
            font-size: 20px;
            margin-bottom: 10px;
        }

        .award-item p {
            color: #666;
        }

        /* CONNECTION OPTIONS */
        .connection-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 60px 0;
        }

        .option-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(227, 27, 35, 0.1);
            transition: all 0.3s;
        }

        .option-card:hover {
            transform: scale(1.02);
        }

        .option-card.fast {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px solid var(--primary-red);
        }

        .option-card.survey {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
        }

        .option-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .option-card h3 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .option-card p {
            font-size: 16px;
            margin-bottom: 25px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .option-btn {
            display: inline-block;
            padding: 15px 40px;
            border-radius: 60px;
            font-size: 18px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .option-card.fast .option-btn {
            background: var(--primary-red);
            color: white;
        }

        .option-card.survey .option-btn {
            background: white;
            color: var(--primary-red);
        }

        .option-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* MEMBER LOGIN SECTION */
        .member-section {
            background: white;
            border-radius: 30px;
            padding: 40px;
            margin: 40px 0;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .member-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .member-header h3 {
            color: var(--primary-red);
            font-size: 28px;
            font-weight: 700;
        }

        .member-points {
            background: linear-gradient(135deg, var(--gold), #ffed4e);
            padding: 15px 30px;
            border-radius: 60px;
            font-weight: 600;
        }

        .member-login {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .member-login input {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            border: 2px solid #ffe5e5;
            border-radius: 10px;
            font-size: 16px;
        }

        .member-login button {
            padding: 15px 30px;
            background: var(--primary-red);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .member-login button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(227, 27, 35, 0.3);
        }

        .rewards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .reward-item {
            background: #fff5f5;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #ffe5e5;
            transition: all 0.3s;
        }

        .reward-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(227, 27, 35, 0.1);
        }

        .reward-points {
            background: var(--gold);
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
        }

        .logout-section {
            background: #f0f9ff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .member-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .member-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }

        .member-btn.primary {
            background: var(--primary-red);
            color: white;
        }

        .member-btn.secondary {
            background: #6c757d;
            color: white;
        }

        .member-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* FOOTER */
        .footer {
            background: linear-gradient(135deg, var(--dark-red), #8b0000);
            color: white;
            padding: 60px 0 20px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            padding: 0 20px;
        }

        .footer-col h4 {
            font-size: 20px;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-col h4:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--gold);
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul li a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-col ul li a:hover {
            color: var(--gold);
            padding-left: 5px;
        }

        .contact-info li {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--gold);
            color: var(--dark-red);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 40px;
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .connection-options {
                grid-template-columns: 1fr;
            }
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
            .rewards-grid {
                grid-template-columns: 1fr;
            }
            .member-login {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <span class="logo-icon">🛍️</span>
                <div class="logo-text">
                    <h1>THM MALL</h1>
                    <p>Trung tâm thương mại hiện đại bậc nhất</p>
                </div>
            </div>
            <div class="hotline">
                <i>📞</i> 1900 6868
            </div>
        </div>
    </header>

    <!-- HERO BANNER -->
    <section class="hero">
        <div class="hero-content">
            <h2>CHÀO MỪNG ĐẾN VỚI THM MALL</h2>
            <p>Không gian mua sắm đẳng cấp - Giải trí tuyệt vời</p>
            <div class="hero-badges">
                <span class="badge">🏆 Top 10 TTTM 2026</span>
                <span class="badge">⭐ 5 sao chất lượng</span>
                <span class="badge">🌐 Hơn 1 triệu khách hàng</span>
            </div>
        </div>
    </section>

    <!-- WELCOME SECTION -->
    <div class="welcome">
        <div class="welcome-title">
            <h3>THM MALL - NƠI MUA SẮM LÝ TƯỞNG</h3>
            <p>Với hơn 500 thương hiệu nổi tiếng trong và ngoài nước</p>
        </div>

        <!-- BEST SELLER -->
        <div class="best-seller">
            <div class="section-title">
                <h3>🔥 BEST SELLER</h3>
            </div>
            <div class="product-grid">
                <div class="product-card">
                    <div class="product-image">📱</div>
                    <div class="product-info">
                        <h4>iPhone 15 Pro Max</h4>
                        <div class="product-price">33.990.000đ <del>38.990.000đ</del></div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">👗</div>
                    <div class="product-info">
                        <h4>Đầm dạ hội cao cấp</h4>
                        <div class="product-price">2.590.000đ <del>3.990.000đ</del></div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">⌚</div>
                    <div class="product-info">
                        <h4>Đồng hồ Rolex</h4>
                        <div class="product-price">285.000.000đ <del>320.000.000đ</del></div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image">👟</div>
                    <div class="product-info">
                        <h4>Giày Nike Air Max</h4>
                        <div class="product-price">2.890.000đ <del>3.590.000đ</del></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AWARDS SECTION -->
        <div class="awards">
            <div class="awards-grid">
                <div class="award-item">
                    <div class="award-icon">🏆</div>
                    <h4>Top 10 TTTM 2026</h4>
                    <p>Do Hiệp hội Bán lẻ Việt Nam bình chọn</p>
                </div>
                <div class="award-item">
                    <div class="award-icon">⭐</div>
                    <h4>Chứng nhận 5 sao</h4>
                    <p>Dịch vụ xuất sắc 5 năm liên tiếp</p>
                </div>
                <div class="award-item">
                    <div class="award-icon">🌐</div>
                    <h4>Thương hiệu uy tín</h4>
                    <p>Được hàng triệu khách hàng tin tưởng</p>
                </div>
            </div>
        </div>

        <!-- CONNECTION OPTIONS -->
        <div class="connection-options">
            <div class="option-card fast">
                <div class="option-icon">⚡</div>
                <h3>Kết nối siêu tốc</h3>
                <p>Dành cho khách hàng bận rộn<br>Kết nối WiFi ngay lập tức</p>
                <a href="fast-connect.php?client=<?php echo $_GET['client'] ?? ''; ?>" class="option-btn">KẾT NỐI NGAY</a>
            </div>
            
            <div class="option-card survey">
                <div class="option-icon">🎁</div>
                <h3>Nhận ưu đãi đặc biệt</h3>
                <p>Tham gia khảo sát 2 phút<br>Nhận ngay voucher giảm 20% + 10 điểm hội viên</p>
                <a href="survey.html?client=<?php echo $_GET['client'] ?? ''; ?>" class="option-btn">NHẬN ƯU ĐÃI NGAY</a>
            </div>
        </div>

        <!-- MEMBER SECTION -->
        <div class="member-section">
            <?php if(isset($_SESSION['member_name'])): ?>
                <!-- ĐÃ ĐĂNG NHẬP -->
                <div class="member-header">
                    <h3>👋 Xin chào, <?php echo $_SESSION['member_name']; ?>!</h3>
                    <div class="member-points">💰 <?php echo number_format($_SESSION['member_points']); ?> điểm</div>
                </div>
                
                <div class="logout-section">
                    <p style="margin-bottom: 15px;">Hạng thành viên: <strong><?php echo $_SESSION['member_tier'] ?? 'Đồng'; ?></strong></p>
                    <div class="member-actions">
                        <a href="member_dashboard.php" class="member-btn primary">👤 Trang cá nhân</a>
                        <a href="member_logout.php" class="member-btn secondary">🚪 Đăng xuất</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- CHƯA ĐĂNG NHẬP -->
                <div class="member-header">
                    <h3>👥 Hội viên THM</h3>
                    <div class="member-points">Đăng nhập để nhận ưu đãi</div>
                </div>
                
                <form method="POST" action="member_login_process.php">
                    <div class="member-login">
                        <input type="text" name="phone" placeholder="Số điện thoại" required>
                        <input type="password" name="password" placeholder="Mật khẩu" required>
                        <button type="submit">ĐĂNG NHẬP</button>
                    </div>
                </form>
                
                <div style="margin: 20px 0; text-align: right;">
                    <a href="member_register.php" style="color: var(--primary-red); font-weight: 600;">➡️ Đăng ký hội viên mới</a>
                </div>
            <?php endif; ?>
            
            <h4 style="margin: 30px 0 15px;">🎁 Đổi quà hấp dẫn:</h4>
            <div class="rewards-grid">
                <div class="reward-item">
                    <span style="font-size: 24px;">☕</span>
                    <div>
                        <strong>Voucher cà phê</strong>
                        <div><span class="reward-points">50 điểm</span></div>
                    </div>
                </div>
                <div class="reward-item">
                    <span style="font-size: 24px;">🎫</span>
                    <div>
                        <strong>Voucher 100k</strong>
                        <div><span class="reward-points">200 điểm</span></div>
                    </div>
                </div>
                <div class="reward-item">
                    <span style="font-size: 24px;">🧸</span>
                    <div>
                        <strong>Gấu bông</strong>
                        <div><span class="reward-points">150 điểm</span></div>
                    </div>
                </div>
                <div class="reward-item">
                    <span style="font-size: 24px;">🎧</span>
                    <div>
                        <strong>Tai nghe</strong>
                        <div><span class="reward-points">500 điểm</span></div>
                    </div>
                </div>
                <div class="reward-item">
                    <span style="font-size: 24px;">⌚</span>
                    <div>
                        <strong>Đồng hồ</strong>
                        <div><span class="reward-points">1000 điểm</span></div>
                    </div>
                </div>
                <div class="reward-item">
                    <span style="font-size: 24px;">🎁</span>
                    <div>
                        <strong>Quà bí mật</strong>
                        <div><span class="reward-points">300 điểm</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col">
                <h4>VỀ THM MALL</h4>
                <ul>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Hệ thống cơ sở</a></li>
                    <li><a href="#">Tin tức & Sự kiện</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>HỖ TRỢ</h4>
                <ul>
                    <li><a href="#">Trung tâm CSKH</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Điều khoản sử dụng</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>HỆ THỐNG CƠ SỞ</h4>
                <ul>
                    <li>🏢 THM Mall Nguyễn Trãi - Q.1</li>
                    <li>🏢 THM Mall Lê Văn Việt - Q.9</li>
                    <li>🏢 THM Mall Phạm Văn Đồng - Thủ Đức</li>
                    <li>🏢 THM Mall Nguyễn Văn Linh - Q.7</li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>LIÊN HỆ</h4>
                <ul class="contact-info">
                    <li><i>📞</i> Hotline: 1900 6868</li>
                    <li><i>📧</i> Email: info@thmmall.vn</li>
                    <li><i>🌐</i> Website: www.thmmall.vn</li>
                </ul>
                <div class="social-links">
                    <a href="#">f</a>
                    <a href="#">𝕏</a>
                    <a href="#">in</a>
                    <a href="#">📷</a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2026 THM MALL - Tập đoàn thương mại THM. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>