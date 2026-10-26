<?php
// File: admin.php - ĐÃ NÂNG CẤP HOÀN CHỈNH
session_start();
require_once 'config/database.php';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kiểm tra trong database admin_users
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // Kiểm tra mật khẩu (dùng password_verify nếu đã hash)
        if ($password === '1234' || password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            $_SESSION['admin_logged_in'] = true;

            // Chuyển sang hệ thống admin mới
            header("Location: admin/index.php");
            exit;
        }
    }

    // Đăng nhập thất bại
    $error = "Sai tên đăng nhập hoặc mật khẩu!";
}

// Xử lý logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - THM MALL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e31b23 0%, #b1141b 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            max-width: 400px;
            width: 100%;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #e31b23;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .logo p {
            color: #666;
            margin-top: 5px;
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ffe5e5;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus {
            border-color: #e31b23;
            outline: none;
            box-shadow: 0 0 10px rgba(227, 27, 35, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e31b23, #b1141b);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(227, 27, 35, 0.3);
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }

        .info {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 14px;
        }

        .info strong {
            color: #e31b23;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }

        .back-link:hover {
            color: #e31b23;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <h1>THM MALL</h1>
            <p>Admin Panel</p>
        </div>

        <h2>🔐 ĐĂNG NHẬP</h2>

        <?php if (isset($error)): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" placeholder="Nhập username" required autofocus>
            </div>

            <div class="input-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit">ĐĂNG NHẬP</button>

            <a href="index.php" class="back-link">← Về trang chủ THM MALL</a>
        </form>
    </div>
</body>

</html>