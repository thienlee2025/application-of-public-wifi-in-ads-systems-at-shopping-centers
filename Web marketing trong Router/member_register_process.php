<?php
session_start();
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Lấy dữ liệu
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$birthday = $_POST['birthday'] ?? null;
$gender = $_POST['gender'] ?? 'other';
$agree = isset($_POST['agree']);

// Validate
$errors = [];

if (empty($phone)) {
    $errors[] = "Số điện thoại không được để trống";
} elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    $errors[] = "Số điện thoại không hợp lệ";
}

if (empty($password)) {
    $errors[] = "Mật khẩu không được để trống";
} elseif (strlen($password) < 6) {
    $errors[] = "Mật khẩu tối thiểu 6 ký tự";
}

if ($password != $confirm) {
    $errors[] = "Mật khẩu nhập lại không khớp";
}

if (empty($fullname)) {
    $errors[] = "Họ tên không được để trống";
}

if (!$agree) {
    $errors[] = "Bạn phải đồng ý với điều khoản";
}

// Kiểm tra số điện thoại đã tồn tại chưa
if (empty($errors)) {
    $check = $conn->prepare("SELECT id FROM members WHERE phone = ?");
    $check->bind_param("s", $phone);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Số điện thoại đã được đăng ký";
    }
}

// Nếu có lỗi, quay lại trang đăng ký
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header("Location: member_register.php");
    exit;
}

// Hash mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Thêm vào database
$sql = "INSERT INTO members (phone, password, fullname, email, birthday, gender, points, tier) 
        VALUES (?, ?, ?, ?, ?, ?, 100, 'Đồng')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $phone, $hashed_password, $fullname, $email, $birthday, $gender);

if ($stmt->execute()) {
    // Đăng ký thành công
    header("Location: member_login.php?register=success");
} else {
    echo "Lỗi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>