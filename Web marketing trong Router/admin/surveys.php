<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khảo sát - THM MALL</title>
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

        .export-btn {
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
        }

        .badge-online {
            background: #27ae60;
        }

        .badge-offline {
            background: #e74c3c;
        }

        .badge-both {
            background: #f39c12;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }

        .pagination a.active {
            background: #e31b23;
            color: white;
            border-color: #e31b23;
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
            <li><a href="surveys.php" class="active">📝 Khảo sát</a></li>
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
            <h1>📝 Quản lý khảo sát</h1>
            <div>Xin chào,
                <?php echo $admin_name; ?> |
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>

        <div class="filter-section">
            <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Tìm kiếm (tên, email, SĐT)">

            <select class="filter-input" id="categoryFilter">
                <option value="">📋 Tất cả danh mục</option>
                <option value="dientu">📱 Điện tử</option>
                <option value="thoitrang">👕 Thời trang</option>
                <option value="thucpham">🥘 Thực phẩm</option>
                <option value="giatri">🎬 Giải trí</option>
                <option value="suckhoe">💊 Sức khỏe</option>
                <option value="dungcu">🛠️ Đồ gia dụng</option>
            </select>

            <select class="filter-input" id="trendFilter">
                <option value="">📊 Tất cả xu hướng</option>
                <option value="online">📱 Online</option>
                <option value="offline">🏬 Offline</option>
                <option value="both">🔄 Cả hai</option>
            </select>

            <input type="date" class="filter-input" id="dateFrom" placeholder="Từ ngày">
            <input type="date" class="filter-input" id="dateTo" placeholder="Đến ngày">

            <button class="filter-btn" onclick="applyFilter()">Lọc dữ liệu</button>
            <a href="export_surveys.php" class="export-btn">📥 Xuất Excel</a>
        </div>

        <div class="table-container">
            <table id="surveyTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thời gian</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Danh mục</th>
                        <th>Chi tiêu</th>
                        <th>Xu hướng</th>
                        <th>IP</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    // Lấy dữ liệu khảo sát
                    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    $limit = 20;
                    $offset = ($page - 1) * $limit;

                    $sql = "SELECT * FROM customers ORDER BY survey_time DESC LIMIT ? OFFSET ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $limit, $offset);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $categoryNames = [
                        'dientu' => '📱 Điện tử',
                        'thoitrang' => '👕 Thời trang',
                        'thucpham' => '🥘 Thực phẩm',
                        'giatri' => '🎬 Giải trí',
                        'suckhoe' => '💊 Sức khỏe',
                        'dungcu' => '🛠️ Đồ gia dụng'
                    ];

                    $spendingNames = [
                        '<500k' => 'Dưới 500k',
                        '500k-1m' => '500k - 1tr',
                        '1m-3m' => '1tr - 3tr',
                        '3m-5m' => '3tr - 5tr',
                        '>5m' => 'Trên 5tr'
                    ];

                    while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#
                                <?php echo $row['id']; ?>
                            </td>
                            <td>
                                <?php echo date('H:i d/m', strtotime($row['survey_time'])); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['fullname']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['email']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['phone']); ?>
                            </td>
                            <td>
                                <?php echo $categoryNames[$row['product_search']] ?? $row['product_search']; ?>
                            </td>
                            <td>
                                <?php echo $spendingNames[$row['spending']] ?? $row['spending']; ?>
                            </td>
                            <td>
                                <?php if ($row['trend'] == 'online'): ?>
                                    <span class="badge badge-online">Online</span>
                                <?php elseif ($row['trend'] == 'offline'): ?>
                                    <span class="badge badge-offline">Offline</span>
                                <?php else: ?>
                                    <span class="badge badge-both">Cả hai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $row['ip_address']; ?>
                            </td>
                            <td>
                                <a href="#" onclick="viewDetail(<?php echo $row['id']; ?>)" style="color: #e31b23;">👁️
                                    Xem</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php
            $total = $conn->query("SELECT COUNT(*) FROM customers")->fetch_row()[0];
            $totalPages = ceil($total / $limit);

            for ($i = 1; $i <= $totalPages; $i++):
                ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        function applyFilter() {
            // Xử lý lọc dữ liệu (có thể gửi AJAX)
            alert('Tính năng đang phát triển');
        }

        function viewDetail(id) {
            alert('Xem chi tiết khảo sát ID: ' + id);
            // Có thể mở modal hoặc chuyển trang
        }

        // Tìm kiếm realtime
        document.getElementById('searchInput').addEventListener('keyup', function () {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll('#tableBody tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    </script>
</body>

</html>