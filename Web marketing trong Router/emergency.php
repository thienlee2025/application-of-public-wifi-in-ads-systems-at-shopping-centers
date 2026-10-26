<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ CẢNH BÁO KHẨN CẤP - THM MALL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            font-family: 'Segoe UI', Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            animation: blinkBg 1s infinite;
        }

        @keyframes blinkBg {
            0% {
                background: linear-gradient(135deg, #e74c3c, #c0392b);
            }

            50% {
                background: linear-gradient(135deg, #ff6b6b, #e74c3c);
            }

            100% {
                background: linear-gradient(135deg, #e74c3c, #c0392b);
            }
        }

        .emergency-box {
            background: rgba(0, 0, 0, 0.85);
            padding: 50px;
            border-radius: 30px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .instruction {
            font-size: 20px;
            font-weight: bold;
            color: #f1c40f;
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .btn-refresh {
            margin-top: 25px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
            background: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-refresh:hover {
            transform: scale(1.05);
            background: #f1c40f;
            color: #c0392b;
        }

        .countdown {
            margin-top: 20px;
            font-size: 14px;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .emergency-box {
                padding: 30px;
            }

            h1 {
                font-size: 32px;
            }

            .icon {
                font-size: 60px;
            }

            p {
                font-size: 16px;
            }
        }
    </style>
    <meta http-equiv="refresh" content="15; url=index.php">
</head>

<body>
    <div class="emergency-box">
        <div class="icon">🚨🔥⚠️</div>
        <h1>CẢNH BÁO KHẨN CẤP</h1>
        <p><strong>THM MALL đang xử lý sự cố an toàn.</strong></p>
        <p>Quý khách vui lòng di chuyển <strong>bình tĩnh</strong> đến lối thoát hiểm gần nhất theo hướng dẫn của nhân
            viên.</p>
        <p>Không sử dụng thang máy. Hãy tuân thủ hướng dẫn của đội PCCC.</p>
        <div class="instruction">📢 HỆ THỐNG ĐANG TRONG CHẾ ĐỘ KHẨN CẤP 📢</div>
        <button class="btn-refresh" onclick="location.reload()">🔄 Làm mới</button>
        <div class="countdown">Trang sẽ tự động kiểm tra lại sau 15 giây...</div>
    </div>
    <script>
        setTimeout(() => { window.location.reload(); }, 15000);
    </script>
</body>

</html>