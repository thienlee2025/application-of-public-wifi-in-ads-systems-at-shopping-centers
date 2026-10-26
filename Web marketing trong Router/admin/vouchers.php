<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Voucher - THM MALL</title>
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

        .create-voucher {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .voucher-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .create-btn {
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
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

        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
        }

        .status-active {
            background: #27ae60;
        }

        .status-used {
            background: #7f8c8d;
        }

        .status-expired {
            background: #e74c3c;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 2px;
            color: white;
        }

        .action-btn.send {
            background: #3498db;
        }

        .action-btn.delete {
            background: #e74c3c;
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
            <li><a href="members.php">👥 Hội viên</a></li>
            <li><a href="vouchers.php" class="active">🎁 Voucher</a></li>
            <li><a href="products.php">📦 Sản phẩm</a></li>
            <li><a href="connections.php">📶 Kết nối</a></li>
            <li><a href="reports.php">📈 Báo cáo</a></li>
            <li><a href="settings.php">⚙️ Cấu hình</a></li>
            <li><a href="logout.php">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>🎁 Quản lý Voucher</h1>
            <div>Xin chào,
                <?php echo $admin_name; ?> |
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>

        <?php
        // Kiểm tra và tạo bảng vouchers nếu chưa có
        $conn->query("CREATE TABLE IF NOT EXISTS vouchers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) UNIQUE NOT NULL,
            value INT NOT NULL COMMENT 'Phần trăm giảm',
            points_required INT DEFAULT 0,
            quantity INT DEFAULT 100,
            used INT DEFAULT 0,
            expiry_date DATE,
            status ENUM('active', 'expired', 'disabled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Thêm voucher mẫu nếu chưa có
        $check = $conn->query("SELECT COUNT(*) FROM vouchers")->fetch_row()[0];
        if ($check == 0) {
            $conn->query("INSERT INTO vouchers (code, value, points_required, expiry_date) VALUES 
                ('DHMT20', 20, 0, DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
                ('DHMT50K', 50000, 200, DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
                ('DHMT100K', 100000, 500, DATE_ADD(CURDATE(), INTERVAL 30 DAY))");
        }

        // Lấy thống kê
        $total = $conn->query("SELECT COUNT(*) FROM vouchers")->fetch_row()[0];
        $active = $conn->query("SELECT COUNT(*) FROM vouchers WHERE status='active'")->fetch_row()[0];
        $used = $conn->query("SELECT SUM(used) FROM vouchers")->fetch_row()[0] ?? 0;
        $expired = $conn->query("SELECT COUNT(*) FROM vouchers WHERE expiry_date < CURDATE()")->fetch_row()[0];
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $total; ?>
                </div>
                <div class="stat-label">Tổng voucher</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $active; ?>
                </div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $used; ?>
                </div>
                <div class="stat-label">Đã sử dụng</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $expired; ?>
                </div>
                <div class="stat-label">Hết hạn</div>
            </div>
        </div>

        <div class="create-voucher">
            <h3>➕ Tạo voucher mới</h3>
            <form method="POST" action="create_voucher.php" class="voucher-form">
                <div class="form-group">
                    <label>Mã voucher</label>
                    <input type="text" name="code" value="DHMT<?php echo date('dmy') . rand(10, 99); ?>" required>
                </div>
                <div class="form-group">
                    <label>Giá trị</label>
                    <select name="value_type">
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Số tiền/Phần trăm</label>
                    <input type="number" name="value" required>
                </div>
                <div class="form-group">
                    <label>Điểm yêu cầu</label>
                    <input type="number" name="points_required" value="0">
                </div>
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" value="100">
                </div>
                <div class="form-group">
                    <label>Ngày hết hạn</label>
                    <input type="date" name="expiry_date" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                </div>
                <button type="submit" class="create-btn">📌 Tạo voucher</button>
            </form>
        </div>

        <div class="filter-section">
            <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Tìm mã voucher">

            <select class="filter-input" id="statusFilter">
                <option value="">📋 Tất cả trạng thái</option>
                <option value="active">✅ Đang hoạt động</option>
                <option value="expired">⏰ Hết hạn</option>
                <option value="used">✔️ Đã sử dụng</option>
            </select>

            <button class="filter-btn" onclick="applyFilter()">Lọc</button>
            <button class="filter-btn" onclick="exportVouchers()">📥 Xuất Excel</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã voucher</th>
                        <th>Giá trị</th>
                        <th>Điểm yêu cầu</th>
                        <th>Số lượng</th>
                        <th>Đã dùng</th>
                        <th>Còn lại</th>
                        <th>Hạn dùng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $vouchers = $conn->query("SELECT * FROM vouchers ORDER BY created_at DESC");
                    while ($v = $vouchers->fetch_assoc()):
                        $remaining = $v['quantity'] - $v['used'];
                        $expiry = strtotime($v['expiry_date']);
                        $today = time();

                        if ($v['status'] == 'active' && $expiry < $today) {
                            $status = 'expired';
                            $status_text = 'Hết hạn';
                            $status_class = 'status-expired';
                        } else {
                            $status = $v['status'];
                            $status_text = $status == 'active' ? 'Đang hoạt động' : ($status == 'used' ? 'Đã dùng' : 'Hết hạn');
                            $status_class = $status == 'active' ? 'status-active' : ($status == 'used' ? 'status-used' : 'status-expired');
                        }
                        ?>
                        <tr>
                            <td>#
                                <?php echo $v['id']; ?>
                            </td>
                            <td><strong>
                                    <?php echo $v['code']; ?>
                                </strong></td>
                            <td>
                                <?php echo $v['value']; ?>%
                            </td>
                            <td>
                                <?php echo number_format($v['points_required']); ?>
                            </td>
                            <td>
                                <?php echo $v['quantity']; ?>
                            </td>
                            <td>
                                <?php echo $v['used']; ?>
                            </td>
                            <td>
                                <?php echo $remaining; ?>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($v['expiry_date'])); ?>
                            </td>
                            <td><span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span></td>
                            <td>
                                <button class="action-btn send" onclick="sendVoucher(<?php echo $v['id']; ?>)">📤
                                    Gửi</button>
                                <button class="action-btn delete"
                                    onclick="deleteVoucher(<?php echo $v['id']; ?>)">🗑️</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function applyFilter() {
            let value = document.getElementById('searchInput').value.toLowerCase();
            let status = document.getElementById('statusFilter').value;
            let rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let code = row.cells[1].textContent.toLowerCase();
                let statusCell = row.cells[8].textContent.toLowerCase();

                let matchCode = value === '' || code.includes(value);
                let matchStatus = status === '' || statusCell.includes(status);

                row.style.display = matchCode && matchStatus ? '' : 'none';
            });
        }

        function sendVoucher(id) {
            alert('Tính năng gửi voucher đang phát triển');
        }

        function deleteVoucher(id) {
            if (confirm('Bạn có chắc muốn xóa voucher này?')) {
                alert('Đã xóa voucher ID: ' + id);
            }
        }

        function exportVouchers() {
            alert('Đang xuất file Excel...');
            window.location.href = 'export_vouchers.php';
        }
    </script>
</body>

</html>