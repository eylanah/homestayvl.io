<?php
// =====================================================
// ajax/forgot_handler.php
// =====================================================

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../inc/db.php';
require_once '../inc/mail_config.php';

// PHPMailer đặt ngoài cùng thư mục project
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// PHP 8.0 compatible - bỏ ": void"
function respond($status, $msg) {
    echo json_encode(['status' => $status, 'msg' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['send_pin'])) {
    respond('error', 'Yêu cầu không hợp lệ!');
}

$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond('error', 'Vui lòng nhập email hợp lệ!');
}

// Kiểm tra email tồn tại
$stmt = $con->prepare("SELECT `id`, `name` FROM `user_cred` WHERE `email` = ? AND `status` = 1 LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    respond('error', 'Email không tồn tại trong hệ thống!');
}

$user = $result->fetch_assoc();
$stmt->close();

// Tạo PIN 6 số
$pin        = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$pin_expire = date('Y-m-d H:i:s', strtotime('+' . PIN_EXPIRE_MINUTES . ' minutes'));

// Lưu PIN vào DB
$stmt = $con->prepare("UPDATE `user_cred` SET `token` = ?, `pin_expire` = ? WHERE `id` = ?");
$stmt->bind_param('ssi', $pin, $pin_expire, $user['id']);
if (!$stmt->execute()) {
    respond('error', 'Lỗi hệ thống, vui lòng thử lại!');
}
$stmt->close();

// Gửi email qua PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
    $mail->addAddress($email, $user['name']);

    $mail->isHTML(true);
    $mail->Subject = 'Mã PIN đặt lại mật khẩu - ' . MAIL_FROM_NAME;
    $mail->Body    = emailTemplate($user['name'], $pin, PIN_EXPIRE_MINUTES);
    $mail->AltBody = "Xin chào {$user['name']},\r\nMã PIN: {$pin}\r\nHiệu lực: " . PIN_EXPIRE_MINUTES . " phút.";

    $mail->send();

    $_SESSION['fp_email']   = $email;
    $_SESSION['fp_user_id'] = $user['id'];
    $_SESSION['fp_step']    = 'verify_pin';

    respond('success', 'Mã PIN đã được gửi đến email của bạn!');

} catch (Exception $e) {
    $con->query("UPDATE `user_cred` SET `token` = NULL, `pin_expire` = NULL WHERE `id` = {$user['id']}");
    respond('error', 'Không thể gửi email: ' . $mail->ErrorInfo);
}

// Template HTML email
function emailTemplate($name, $pin, $minutes) {
    return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" style="padding:40px 20px;">
        <table width="500" cellpadding="0" cellspacing="0"
               style="background:#fff;border-radius:12px;overflow:hidden;
                      box-shadow:0 4px 15px rgba(0,0,0,.1);max-width:100%;">
          <tr>
            <td style="background:#1a1a1a;padding:30px;text-align:center;">
              <h1 style="margin:0;color:#fff;font-size:22px;">🏨 Homestay Vĩnh Long</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:40px 35px;">
              <p style="font-size:16px;color:#333;margin:0 0 10px;">
                Xin chào <strong>$name</strong>,
              </p>
              <p style="font-size:14px;color:#555;margin:0 0 30px;">
                Mã PIN đặt lại mật khẩu của bạn là:
              </p>
              <div style="text-align:center;margin:0 0 30px;">
                <span style="display:inline-block;background:#f8f8f8;
                             border:2px dashed #1a1a1a;border-radius:12px;
                             padding:20px 50px;font-size:42px;font-weight:bold;
                             letter-spacing:12px;color:#1a1a1a;">
                  $pin
                </span>
              </div>
              <table width="100%" cellpadding="12" cellspacing="0"
                     style="background:#fff8e1;border-left:4px solid #ffc107;
                            border-radius:4px;margin-bottom:25px;">
                <tr>
                  <td style="font-size:13px;color:#7a6000;">
                    ⏱ Mã có hiệu lực trong <strong>$minutes phút</strong>.
                    Vui lòng không chia sẻ mã này với ai.
                  </td>
                </tr>
              </table>
              <p style="font-size:13px;color:#999;margin:0;">
                Nếu bạn không yêu cầu, hãy bỏ qua email này.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background:#f9f9f9;padding:20px;text-align:center;border-top:1px solid #eee;">
              <p style="margin:0;font-size:12px;color:#aaa;">
                © 2025 Homestay Vĩnh Long. All rights reserved.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}