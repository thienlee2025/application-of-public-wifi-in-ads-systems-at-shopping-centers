<?php
session_start();
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

// Kiểm tra member theo số điện thoại
$sql = "SELECT * FROM members WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $member = $result->fetch_assoc();

    // Kiểm tra mật khẩu (dùng password_verify vì mật khẩu đã được hash)
    if (password_verify($password, $member['password'])) {
        // Đăng nhập thành công
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['member_name'] = $member['fullname'];
        $_SESSION['member_phone'] = $member['phone'];
        $_SESSION['member_points'] = $member['points'];
        $_SESSION['member_tier'] = $member['tier'];

        // Cập nhật last_login
        $update = "UPDATE members SET last_login = NOW() WHERE id = ?";
        $stmt_update = $conn->prepare($update);
        $stmt_update->bind_param("i", $member['id']);
        $stmt_update->execute();

        header("Location: member_dashboard.php");
        exit;
    } else {
        // Sai mật khẩu
        header("Location: member_login.php?error=wrong_password");
        exit;
    }
} else {
    // Không tìm thấy số điện thoại
    header("Location: member_login.php?error=not_found");
    exit;
}
?>