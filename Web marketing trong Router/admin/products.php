<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - THM MALL</title>
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

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(227, 27, 35, 0.2);
        }

        .product-image {
            height: 150px;
            background: linear-gradient(45deg, #ffe5e5, #fff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            border-bottom: 3px solid #e31b23;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .product-price {
            color: #e31b23;
            font-weight: 700;
        }

        .product-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 13px;
            color: #666;
        }

        .interest-count {
            background: #f0f0f0;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .add-product {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
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

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
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
            <li><a href="products.php" class="active">📦 Sản phẩm</a></li>
            <li><a href="connections.php">📶 Kết nối</a></li>
            <li><a href="reports.php">📈 Báo cáo</a></li>
            <li><a href="settings.php">⚙️ Cấu hình</a></li>
            <li><a href="logout.php">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>📦 Quản lý sản phẩm</h1>
            <div>Xin chào,
                <?php echo $admin_name; ?> |
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>

        <?php
        // Lấy thống kê sản phẩm từ bảng customers
        $total = $conn->query("SELECT COUNT(DISTINCT product_search) FROM customers")->fetch_row()[0];
        $top = $conn->query("SELECT product_search, COUNT(*) as count FROM customers GROUP BY product_search ORDER BY count DESC LIMIT 1")->fetch_assoc();
        $today = $conn->query("SELECT COUNT(*) FROM customers WHERE DATE(survey_time) = CURDATE()")->fetch_row()[0];
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $total; ?>
                </div>
                <div class="stat-label">Danh mục</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $top['count'] ?? 0; ?>
                </div>
                <div class="stat-label">Top:
                    <?php echo $top['product_search'] ?? 'Chưa có'; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $today; ?>
                </div>
                <div class="stat-label">Lượt hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">💡</div>
                <div class="stat-label">Gợi ý: Thêm sản phẩm</div>
            </div>
        </div>

        <div class="add-product">
            <h3>➕ Thêm sản phẩm mới</h3>
            <form class="voucher-form" style="grid-template-columns: repeat(5,1fr);">
                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" placeholder="VD: iPhone 15">
                </div>
                <div class="form-group">
                    <label>Danh mục</label>
                    <select>
                        <option>Điện tử</option>
                        <option>Thời trang</option>
                        <option>Thực phẩm</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá</label>
                    <input type="number" placeholder="₫">
                </div>
                <div class="form-group">
                    <label>Giá cũ</label>
                    <input type="number" placeholder="₫">
                </div>
                <div class="form-group">
                    <label>Hình ảnh</label>
                    <input type="text" placeholder="📱">
                </div>
                <button class="create-btn" style="grid-column: span 5;">Thêm sản phẩm</button>
            </form>
        </div>

        <div class="filter-section">
            <select class="filter-input">
                <option>Tất cả danh mục</option>
                <option>Điện tử</option>
                <option>Thời trang</option>
                <option>Thực phẩm</option>
            </select>
            <input type="text" class="filter-input" placeholder="🔍 Tìm sản phẩm...">
            <button class="filter-btn">Lọc</button>
        </div>

        <div class="product-grid">
            <?php
            $products = [
                ['name' => 'iPhone 15 Pro Max', 'price' => '33.990.000', 'old' => '38.990.000', 'cat' => 'dientu', 'icon' => '📱', 'interest' => 1234],
                ['name' => 'Đầm dạ hội', 'price' => '2.590.000', 'old' => '3.990.000', 'cat' => 'thoitrang', 'icon' => '👗', 'interest' => 987],
                ['name' => 'Đồng hồ Rolex', 'price' => '285.000.000', 'old' => '320.000.000', 'cat' => 'dientu', 'icon' => '⌚', 'interest' => 456],
                ['name' => 'Giày Nike Air Max', 'price' => '2.890.000', 'old' => '3.590.000', 'cat' => 'thoitrang', 'icon' => '👟', 'interest' => 756],
                ['name' => 'MacBook Pro', 'price' => '45.990.000', 'old' => '49.990.000', 'cat' => 'dientu', 'icon' => '💻', 'interest' => 890],
                ['name' => 'Túi xách Louis', 'price' => '12.500.000', 'old' => '15.000.000', 'cat' => 'thoitrang', 'icon' => '👛', 'interest' => 234],
                ['name' => 'Rượu vang', 'price' => '890.000', 'old' => '1.200.000', 'cat' => 'thucpham', 'icon' => '🍷', 'interest' => 567],
                ['name' => 'Bánh Trung Thu', 'price' => '450.000', 'old' => '650.000', 'cat' => 'thucpham', 'icon' => '🥮', 'interest' => 123],
            ];

            foreach ($products as $p):
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php echo $p['icon']; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-name">
                            <?php echo $p['name']; ?>
                        </div>
                        <div class="product-price">
                            <?php echo $p['price']; ?>₫
                        </div>
                        <div style="font-size: 12px; color: #999;"><del>
                                <?php echo $p['old']; ?>₫
                            </del></div>
                        <div class="product-stats">
                            <span class="interest-count">🔥
                                <?php echo $p['interest']; ?> quan tâm
                            </span>
                            <span>✏️ 🗑️</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>