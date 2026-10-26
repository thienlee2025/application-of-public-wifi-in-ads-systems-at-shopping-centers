<?php
session_start();
if (isset($_SESSION['member_id'])) {
    header("Location: member_dashboard.php");
    exit;
}

// Lấy lỗi từ session nếu có
$errors = isset($_SESSION['register_errors']) ? $_SESSION['register_errors'] : [];
unset($_SESSION['register_errors']);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký hội viên - THM MALL</title>
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
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fff5f5, #ffe5e5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(227, 27, 35, 0.2);
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
        }

        .register-form {
            padding: 40px;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .form-label span {
            color: var(--primary-red);
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #ffe5e5;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-red);
            outline: none;
            box-shadow: 0 0 15px rgba(227, 27, 35, 0.2);
        }

        .radio-group {
            display: flex;
            gap: 20px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(227, 27, 35, 0.3);
        }

        .benefits {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }

        .benefit-item {
            padding: 10px;
        }

        .benefit-icon {
            font-size: 24px;
            color: var(--gold);
            margin-bottom: 5px;
        }

        .benefit-text {
            font-size: 12px;
            color: #666;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-form {
                padding: 20px;
            }

            .benefits {
                grid-template-columns: 1fr;
            }

            .radio-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="header">
            <h2>🎉 ĐĂNG KÝ HỘI VIÊN THM MALL</h2>
            <p>Nhận ngay ưu đãi chào mừng 100 điểm + voucher 50k</p>
        </div>

        <div class="register-form">
            <div class="benefits">
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <div class="benefit-text">Tích điểm đổi quà</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🎫</div>
                    <div class="benefit-text">Voucher mỗi tháng</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🎂</div>
                    <div class="benefit-text">Quà sinh nhật</div>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?php echo $error; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="member_register_process.php">
                <div class="form-group">
                    <label class="form-label">📱 Số điện thoại <span>*</span></label>
                    <input type="tel" class="form-control" name="phone" required placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">🔒 Mật khẩu <span>*</span></label>
                    <input type="password" class="form-control" name="password" required
                        placeholder="Tối thiểu 6 ký tự">
                </div>

                <div class="form-group">
                    <label class="form-label">🔒 Nhập lại mật khẩu <span>*</span></label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">👤 Họ và tên <span>*</span></label>
                    <input type="text" class="form-control" name="fullname" required placeholder="Nhập họ tên">
                </div>

                <div class="form-group">
                    <label class="form-label">📧 Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Nhập email">
                </div>

                <div class="form-group">
                    <label class="form-label">🎂 Ngày sinh</label>
                    <input type="date" class="form-control" name="birthday">
                </div>

                <div class="form-group">
                    <label class="form-label">👤 Giới tính</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="gender" value="male" id="male">
                            <label for="male">Nam</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="gender" value="female" id="female">
                            <label for="female">Nữ</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="gender" value="other" id="other" checked>
                            <label for="other">Khác</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="agree" required>
                        Tôi đồng ý với <a href="#">điều khoản</a> của THM MALL
                    </label>
                </div>

                <button type="submit" class="btn-register">ĐĂNG KÝ NGAY - NHẬN 100 ĐIỂM</button>

                <div class="login-link">
                    Đã có tài khoản? <a href="member_login.php">Đăng nhập ngay</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>