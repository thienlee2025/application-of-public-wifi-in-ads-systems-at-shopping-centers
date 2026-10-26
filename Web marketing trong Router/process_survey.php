<?php
// process_survey.php
session_start();

// BẬT DEBUG ĐỂ XEM LỖI (THÊM DÒNG NÀY)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// SỬA: Kết nối database ĐÚNG
$conn = new mysqli('localhost', 'root', '', 'shopping_mall');  // ← SỬA THÀNH shopping_mall

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$product_search = $_POST['product_search'] ?? '';
$spending = $_POST['spending'] ?? '';
$trend = $_POST['trend'] ?? '';
$feedback = $_POST['feedback'] ?? '';
$client_ip = $_POST['client_ip'] ?? $_SERVER['REMOTE_ADDR'];

// DEBUG: In ra để kiểm tra (có thể xóa sau)
error_log("=== NEW SURVEY ===");
error_log("Fullname: $fullname");
error_log("Email: $email");
error_log("Phone: $phone");
error_log("Product: $product_search");
error_log("IP: $client_ip");

// Validate cơ bản
if (empty($fullname) || empty($email) || empty($phone)) {
    die("❌ Vui lòng điền đầy đủ thông tin!");
}

// Kiểm tra bảng có cột points không
$check_col = $conn->query("SHOW COLUMNS FROM customers LIKE 'points'");
$has_points = $check_col && $check_col->num_rows > 0;

if ($has_points) {
    $sql = "INSERT INTO customers (fullname, email, phone, product_search, spending, trend, feedback, ip_address, points) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 10)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $fullname, $email, $phone, $product_search, $spending, $trend, $feedback, $client_ip);
} else {
    $sql = "INSERT INTO customers (fullname, email, phone, product_search, spending, trend, feedback, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $fullname, $email, $phone, $product_search, $spending, $trend, $feedback, $client_ip);
}

if ($stmt->execute()) {
    error_log("✅ Lưu thành công! ID: " . $stmt->insert_id);

    // Gọi ESP32 để ủy quyền
    if (!empty($client_ip) && $client_ip != '::1' && $client_ip != '127.0.0.1') {
        $esp32_url = "http://192.168.4.1/authorize?client=" . urlencode($client_ip);
        @file_get_contents($esp32_url);
        error_log("Called ESP32: $esp32_url");
    }

    // Chuyển đến trang thanks với thông báo có voucher
    header("Location: thanks.html?name=" . urlencode($fullname) . "&voucher=true&code=DHMT17A1CL&points=10");
    exit;
} else {
    error_log("❌ Lỗi SQL: " . $stmt->error);
    echo "❌ Lỗi lưu dữ liệu: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>