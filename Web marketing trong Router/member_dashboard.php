<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: member_login.php");
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();

$member_id = $_SESSION['member_id'];

// Lấy thông tin member
$sql = "SELECT * FROM members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

// Lấy lịch sử điểm
$history_sql = "SELECT * FROM point_history WHERE member_id = ? ORDER BY created_at DESC LIMIT 10";
$stmt_history = $conn->prepare($history_sql);
$stmt_history->bind_param("i", $member_id);
$stmt_history->execute();
$history = $stmt_history->get_result();

// Lấy danh sách quà có thể đổi
$rewards_sql = "SELECT * FROM rewards WHERE status = 'active' ORDER BY points_required";
$rewards = $conn->query($rewards_sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hội viên THM MALL</title>
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
            padding: 20px;
        }

        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 30px;
            border-radius: 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .member-info {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px 30px;
            border-radius: 20px;
        }

        .member-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .member-tier {
            background: var(--gold);
            color: #333;
            padding: 5px 15px;
            border-radius: 30px;
            display: inline-block;
            font-weight: 600;
        }

        .btn-logout {
            background: white;
            color: var(--primary-red);
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .points-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .points-value {
            font-size: 48px;
            font-weight: 800;
            color: var(--primary-red);
        }

        .points-label {
            color: #666;
            font-size: 16px;
        }

        .btn-redeem {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 15px 30px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            color: var(--primary-red);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffe5e5;
        }

        .info-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .info-label {
            color: #666;
            font-size: 13px;
        }

        .info-value {
            font-weight: 600;
            margin-top: 5px;
        }

        .history-item {
            padding: 10px;
            border-bottom: 1px solid #ffe5e5;
            display: flex;
            justify-content: space-between;
        }

        .history-points {
            font-weight: 700;
        }

        .history-points.positive {
            color: #27ae60;
        }

        .rewards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .reward-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid #ffe5e5;
        }

        .reward-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .reward-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .reward-points {
            color: var(--primary-red);
            font-weight: 700;
            margin: 10px 0;
        }

        .btn-small {
            background: var(--primary-red);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
        }

        .btn-small:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .rewards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <div>
                <h1>👋 Xin chào,
                    <?php echo htmlspecialchars($member['fullname']); ?>
                </h1>
                <p>Chào mừng bạn quay trở lại THM MALL</p>
            </div>
            <div class="member-info">
                <div class="member-name">Hạng: <span class="member-tier">
                        <?php echo $member['tier']; ?>
                    </span></div>
                <div style="margin-top: 10px;">📱
                    <?php echo $member['phone']; ?>
                </div>
            </div>
            <a href="member_logout.php" class="btn-logout">Đăng xuất</a>
        </div>

        <div class="points-card">
            <div>
                <div class="points-label">Điểm tích lũy hiện tại</div>
                <div class="points-value">
                    <?php echo number_format($member['points']); ?>
                </div>
            </div>
            <a href="#rewards" class="btn-redeem">ĐỔI QUÀ NGAY</a>
        </div>

        <div class="grid-2">
            <div class="card">
                <h3>📋 Thông tin cá nhân</h3>
                <div class="info-item">
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value">
                        <?php echo $member['phone']; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        <?php echo $member['email'] ?: 'Chưa cập nhật'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ngày sinh</div>
                    <div class="info-value">
                        <?php echo $member['birthday'] ? date('d/m/Y', strtotime($member['birthday'])) : 'Chưa cập nhật'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ngày tham gia</div>
                    <div class="info-value">
                        <?php echo date('d/m/Y', strtotime($member['created_at'])); ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>📊 Lịch sử tích điểm</h3>
                <?php if ($history->num_rows > 0): ?>
                    <?php while ($row = $history->fetch_assoc()): ?>
                        <div class="history-item">
                            <span>
                                <?php echo $row['reason']; ?>
                            </span>
                            <span class="history-points <?php echo $row['points_change'] > 0 ? 'positive' : ''; ?>">
                                <?php echo $row['points_change'] > 0 ? '+' : ''; ?>
                                <?php echo $row['points_change']; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Chưa có lịch sử tích điểm</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card" id="rewards">
            <h3>🎁 Đổi quà từ điểm thưởng</h3>
            <div class="rewards-grid">
                <?php while ($reward = $rewards->fetch_assoc()): ?>
                    <div class="reward-card">
                        <div class="reward-icon">
                            <?php echo explode(' ', $reward['name'])[0]; ?>
                        </div>
                        <div class="reward-name">
                            <?php echo $reward['name']; ?>
                        </div>
                        <div class="reward-points">
                            <?php echo number_format($reward['points_required']); ?> điểm
                        </div>
                        <button class="btn-small" <?php echo $member['points'] < $reward['points_required'] ? 'disabled' : ''; ?>
                            onclick="redeem(
                        <?php echo $reward['id']; ?>)">
                            Đổi ngay
                        </button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        function redeem(rewardId) {
            if (confirm('Bạn có chắc muốn đổi quà này?')) {
                window.location.href = 'member_redeem.php?reward=' + rewardId;
            }
        }
    </script>
</body>

</html>