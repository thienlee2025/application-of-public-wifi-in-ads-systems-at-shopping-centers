<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'] ?? '';
    $value = $_POST['value'] ?? 0;
    $points_required = $_POST['points_required'] ?? 0;
    $quantity = $_POST['quantity'] ?? 100;
    $expiry_date = $_POST['expiry_date'] ?? date('Y-m-d', strtotime('+30 days'));

    // Kiểm tra mã đã tồn tại chưa
    $check = $conn->prepare("SELECT id FROM vouchers WHERE code = ?");
    $check->bind_param("s", $code);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Mã voucher đã tồn tại!";
        header("Location: vouchers.php");
        exit;
    }

    // Thêm voucher mới
    $sql = "INSERT INTO vouchers (code, value, points_required, quantity, expiry_date) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiis", $code, $value, $points_required, $quantity, $expiry_date);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Tạo voucher thành công!";
    } else {
        $_SESSION['error'] = "Lỗi: " . $stmt->error;
    }

    header("Location: vouchers.php");
    exit;
}
?>