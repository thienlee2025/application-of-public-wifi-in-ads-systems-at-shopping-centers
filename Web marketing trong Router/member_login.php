<?php
session_start();
if (isset($_SESSION['member_id'])) {
    header("Location: member_dashboard.php");
    exit;
}

// Lấy thông báo lỗi từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
$register_success = isset($_GET['register']) ? $_GET['register'] : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hội viên - THM MALL</title>
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

        .login-container {
            max-width: 450px;
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

        .login-form {
            padding: 40px;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
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

        .btn-login {
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
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(227, 27, 35, 0.3);
        }

        .links {
            text-align: center;
            margin-top: 25px;
        }

        .links a {
            color: var(--primary-red);
            text-decoration: none;
            margin: 0 10px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .benefits {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
            text-align: center;
        }

        .benefit-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
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

        @media (max-width: 768px) {
            .login-form {
                padding: 20px;
            }

            .benefits {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="header">
            <h2>👥 HỘI VIÊN THM MALL</h2>
            <p>Đăng nhập để nhận nhiều ưu đãi đặc biệt</p>
        </div>

        <div class="login-form">
            <?php if ($register_success == 'success'): ?>
                <div class="alert alert-success">
                    ✅ Đăng ký thành công! Vui lòng đăng nhập.
                </div>
            <?php endif; ?>

            <?php if ($error == 'wrong_password'): ?>
                <div class="alert alert-danger">
                    ❌ Sai mật khẩu. Vui lòng thử lại.
                </div>
            <?php elseif ($error == 'not_found'): ?>
                <div class="alert alert-danger">
                    ❌ Không tìm thấy số điện thoại. Vui lòng đăng ký.
                </div>
            <?php endif; ?>

            <div class="benefits">
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <div class="benefit-text">Tích điểm đổi quà</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🎁</div>
                    <div class="benefit-text">Voucher độc quyền</div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">⭐</div>
                    <div class="benefit-text">Ưu đãi sinh nhật</div>
                </div>
            </div>

            <form method="POST" action="member_login_process.php">
                <div class="form-group">
                    <label class="form-label">📱 Số điện thoại</label>
                    <input type="tel" class="form-control" name="phone" required placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">🔒 Mật khẩu</label>
                    <input type="password" class="form-control" name="password" required placeholder="Nhập mật khẩu">
                </div>

                <button type="submit" class="btn-login">ĐĂNG NHẬP</button>

                <div class="links">
                    <a href="member_register.php">Đăng ký hội viên mới</a>
                    <a href="#">Quên mật khẩu?</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>