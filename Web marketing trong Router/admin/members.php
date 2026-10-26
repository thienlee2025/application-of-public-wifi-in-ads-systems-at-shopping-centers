<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hội viên - THM MALL</title>
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
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #e31b23;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 200px;
        }

        .filter-btn {
            padding: 10px 20px;
            background: #e31b23;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
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

        .tier-badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
        }

        .tier-gold {
            background: #f1c40f;
            color: #000;
        }

        .tier-silver {
            background: #95a5a6;
        }

        .tier-bronze {
            background: #e67e22;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 2px;
        }

        .action-btn.edit {
            background: #3498db;
            color: white;
        }

        .action-btn.points {
            background: #27ae60;
            color: white;
        }

        .action-btn.lock {
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
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>THM ADMIN</h2>
        <ul>
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="surveys.php">📝 Khảo sát</a></li>
            <li><a href="members.php" class="active">👥 Hội viên</a></li>
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
            <h1>👥 Quản lý hội viên</h1>
            <div>Xin chào,
                <?php echo $admin_name; ?> |
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>

        <?php
        // Lấy thống kê hội viên
        $total = $conn->query("SELECT COUNT(*) FROM members")->fetch_row()[0];
        $gold = $conn->query("SELECT COUNT(*) FROM members WHERE tier='Vàng'")->fetch_row()[0];
        $silver = $conn->query("SELECT COUNT(*) FROM members WHERE tier='Bạc'")->fetch_row()[0];
        $bronze = $conn->query("SELECT COUNT(*) FROM members WHERE tier='Đồng'")->fetch_row()[0];
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $total; ?>
                </div>
                <div class="stat-label">Tổng hội viên</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $gold; ?>
                </div>
                <div class="stat-label">Hạng Vàng</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $silver; ?>
                </div>
                <div class="stat-label">Hạng Bạc</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $bronze; ?>
                </div>
                <div class="stat-label">Hạng Đồng</div>
            </div>
        </div>

        <div class="filter-section">
            <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Tìm kiếm (tên, SĐT, email)">

            <select class="filter-input" id="tierFilter">
                <option value="">🏅 Tất cả hạng</option>
                <option value="Vàng">🥇 Vàng</option>
                <option value="Bạc">🥈 Bạc</option>
                <option value="Đồng">🥉 Đồng</option>
            </select>

            <select class="filter-input" id="sortFilter">
                <option value="points_desc">Điểm cao → thấp</option>
                <option value="points_asc">Điểm thấp → cao</option>
                <option value="newest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
            </select>

            <button class="filter-btn" onclick="applyFilter()">Áp dụng</button>
            <button class="filter-btn" onclick="window.location.href='add_member.php'">➕ Thêm hội viên</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>SĐT</th>
                        <th>Email</th>
                        <th>Hạng</th>
                        <th>Điểm</th>
                        <th>Ngày tham gia</th>
                        <th>Lần cuối</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $members = $conn->query("SELECT * FROM members ORDER BY points DESC LIMIT 50");
                    while ($m = $members->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#
                                <?php echo $m['id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($m['fullname']); ?>
                            </td>
                            <td>
                                <?php echo $m['phone']; ?>
                            </td>
                            <td>
                                <?php echo $m['email']; ?>
                            </td>
                            <td>
                                <?php if ($m['tier'] == 'Vàng'): ?>
                                    <span class="tier-badge tier-gold">🥇 Vàng</span>
                                <?php elseif ($m['tier'] == 'Bạc'): ?>
                                    <span class="tier-badge tier-silver">🥈 Bạc</span>
                                <?php else: ?>
                                    <span class="tier-badge tier-bronze">🥉 Đồng</span>
                                <?php endif; ?>
                            </td>
                            <td><strong>
                                    <?php echo number_format($m['points']); ?>
                                </strong></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($m['created_at'])); ?>
                            </td>
                            <td>
                                <?php echo $m['last_login'] ? date('d/m/Y', strtotime($m['last_login'])) : 'Chưa đăng nhập'; ?>
                            </td>
                            <td>
                                <button class="action-btn edit" onclick="editMember(<?php echo $m['id']; ?>)">✏️</button>
                                <button class="action-btn points" onclick="addPoints(<?php echo $m['id']; ?>)">➕</button>
                                <button class="action-btn lock" onclick="toggleLock(<?php echo $m['id']; ?>)">🔒</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function applyFilter() {
            alert('Tính năng đang phát triển');
        }

        function editMember(id) {
            alert('Sửa thông tin hội viên ID: ' + id);
        }

        function addPoints(id) {
            let points = prompt('Nhập số điểm muốn cộng (có thể nhập số âm để trừ):');
            if (points !== null) {
                alert('Đã cộng ' + points + ' điểm cho hội viên ID: ' + id);
            }
        }

        function toggleLock(id) {
            if (confirm('Bạn có chắc muốn khóa/mở khóa tài khoản này?')) {
                alert('Đã thay đổi trạng thái hội viên ID: ' + id);
            }
        }

        // Tìm kiếm realtime
        document.getElementById('searchInput').addEventListener('keyup', function () {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    </script>
</body>

</html>