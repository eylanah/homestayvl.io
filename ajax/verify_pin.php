<?php
// =====================================================
// ajax/verify_pin.php
// Xử lý 2 việc:
//   1. verify_pin  → kiểm tra PIN đúng/hết hạn
//   2. reset_pass  → đổi mật khẩu sau khi PIN đã xác nhận
// =====================================================

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../inc/db.php';

function respond(string $status, string $msg): never {
    echo json_encode(['status' => $status, 'msg' => $msg]);
    exit;
}

// --------------------------------------------------
// Action 1: Xác minh mã PIN
// --------------------------------------------------
if (isset($_POST['verify_pin'])) {

    // Kiểm tra session bước trước
    if (empty($_SESSION['fp_email']) || empty($_SESSION['fp_step'])
        || $_SESSION['fp_step'] !== 'verify_pin') {
        respond('error', 'Phiên làm việc không hợp lệ! Vui lòng thử lại từ đầu.');
    }

    $pin_input = trim($_POST['pin'] ?? '');

    if (!preg_match('/^\d{6}$/', $pin_input)) {
        respond('error', 'Mã PIN phải gồm đúng 6 chữ số!');
    }

    $email = $_SESSION['fp_email'];
    $now   = date('Y-m-d H:i:s');

    // Truy vấn PIN trong DB (so sánh cả thời gian hết hạn)
    $stmt = $con->prepare(
        "SELECT `id` FROM `user_cred`
         WHERE `email` = ? AND `token` = ? AND `pin_expire` > ? AND `status` = 1
         LIMIT 1"
    );
    $stmt->bind_param('sss', $email, $pin_input, $now);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        respond('error', 'Mã PIN không đúng hoặc đã hết hạn!');
    }

    // PIN đúng → cho phép bước đổi mật khẩu
    $_SESSION['fp_step'] = 'reset_pass';
    respond('success', 'Xác minh thành công!');
}

// --------------------------------------------------
// Action 2: Đặt lại mật khẩu
// --------------------------------------------------
if (isset($_POST['reset_pass'])) {

    // Kiểm tra session bước trước
    if (empty($_SESSION['fp_email']) || empty($_SESSION['fp_step'])
        || $_SESSION['fp_step'] !== 'reset_pass') {
        respond('error', 'Phiên làm việc không hợp lệ! Vui lòng thử lại từ đầu.');
    }

    $new_pass  = $_POST['new_pass']   ?? '';
    $conf_pass = $_POST['conf_pass']  ?? '';

    if (strlen($new_pass) < 8) {
        respond('error', 'Mật khẩu phải có ít nhất 8 ký tự!');
    }

    if ($new_pass !== $conf_pass) {
        respond('error', 'Mật khẩu xác nhận không khớp!');
    }

    $email        = $_SESSION['fp_email'];
    $hashed_pass  = password_hash($new_pass, PASSWORD_BCRYPT);

    // Cập nhật mật khẩu, xóa PIN
    $stmt = $con->prepare(
        "UPDATE `user_cred`
         SET `password` = ?, `token` = NULL, `pin_expire` = NULL
         WHERE `email` = ? AND `status` = 1"
    );
    $stmt->bind_param('ss', $hashed_pass, $email);

    if (!$stmt->execute() || $stmt->affected_rows === 0) {
        respond('error', 'Không thể cập nhật mật khẩu, vui lòng thử lại!');
    }
    $stmt->close();

    // Xóa toàn bộ session forgot password
    unset($_SESSION['fp_email'], $_SESSION['fp_user_id'], $_SESSION['fp_step']);

    respond('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập lại.');
}

// Nếu không khớp action nào
respond('error', 'Yêu cầu không hợp lệ!');
