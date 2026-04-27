<?php

require_once('email_config.php');
require_once('vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_booking_email($user_email, $user_name, $booking_data, $email_type) {
    // Tạo nội dung email dựa trên loại
    switch($email_type) {
        case 'booking_created':
            $subject = "Xác nhận đặt phòng - " . $booking_data['order_id'];
            $content = "
                <h2>Đặt phòng thành công!</h2>
                <p>Xin chào <strong>{$user_name}</strong>,</p>
                <p>Cảm ơn bạn đã đặt phòng tại khách sạn của chúng tôi.</p>
                
                <h3>Thông tin đặt phòng:</h3>
                <ul>
                    <li><strong>Mã đơn hàng:</strong> {$booking_data['order_id']}</li>
                    <li><strong>Phòng:</strong> {$booking_data['room_name']}</li>
                    <li><strong>Ngày nhận phòng:</strong> {$booking_data['checkin']}</li>
                    <li><strong>Ngày trả phòng:</strong> {$booking_data['checkout']}</li>
                    <li><strong>Tổng tiền:</strong> " . number_format($booking_data['total_amount'], 0, ',', '.') . " vnđ</li>
                </ul>
                
                <h3>Thanh toán:</h3>
                <p><strong>Cần thanh toán trước 50%:</strong> " . number_format($booking_data['deposit_amount'], 0, ',', '.') . " vnđ</p>
                <p><strong>Phương thức:</strong> {$booking_data['payment_method']}</p>
                <p>Vui lòng chuyển khoản trong vòng 24h để xác nhận đặt phòng.</p>
                
                <p>Trân trọng,<br>Homestay Vĩnh Long</p>
            ";
            break;

        case 'payment_50_confirmed':
            $subject = "Đã xác nhận thanh toán 50% - " . $booking_data['order_id'];
            $content = "
                <h2>Đã xác nhận thanh toán!</h2>
                <p>Xin chào <strong>{$user_name}</strong>,</p>
                <p>Chúng tôi đã nhận được thanh toán 50% của bạn.</p>
                
                <h3>Thông tin đặt phòng:</h3>
                <ul>
                    <li><strong>Mã đơn hàng:</strong> {$booking_data['order_id']}</li>
                    <li><strong>Phòng:</strong> {$booking_data['room_name']}</li>
                    <li><strong>Ngày nhận phòng:</strong> {$booking_data['checkin']}</li>
                    <li><strong>Ngày trả phòng:</strong> {$booking_data['checkout']}</li>
                    <li><strong>Đã thanh toán:</strong> " . number_format($booking_data['paid_amount'], 0, ',', '.') . " vnđ (50%)</li>
                    <li><strong>Còn lại:</strong> " . number_format($booking_data['remaining_amount'], 0, ',', '.') . " vnđ (50%)</li>
                </ul>
                
                <p><strong>Lưu ý:</strong> Vui lòng thanh toán 50% còn lại tại quầy lễ tân khi nhận phòng.</p>
                
                <p>Trân trọng,<br>Homestay Vĩnh Long</p>
            ";
            break;

        case 'payment_full_confirmed':
            $subject = "Đã thanh toán đủ - " . $booking_data['order_id'];
            $content = "
                <h2>Thanh toán hoàn tất!</h2>
                <p>Xin chào <strong>{$user_name}</strong>,</p>
                <p>Cảm ơn bạn đã thanh toán đầy đủ. Đặt phòng của bạn đã được xác nhận hoàn tất.</p>
                
                <h3>Thông tin đặt phòng:</h3>
                <ul>
                    <li><strong>Mã đơn hàng:</strong> {$booking_data['order_id']}</li>
                    <li><strong>Phòng:</strong> {$booking_data['room_name']}</li>
                    <li><strong>Ngày nhận phòng:</strong> {$booking_data['checkin']}</li>
                    <li><strong>Ngày trả phòng:</strong> {$booking_data['checkout']}</li>
                    <li><strong>Tổng đã thanh toán:</strong> " . number_format($booking_data['total_amount'], 0, ',', '.') . " vnđ</li>
                </ul>
                
                <p>Chúng tôi rất mong được đón tiếp bạn!</p>
                
                <p>Trân trọng,<br>Homestay Vĩnh Long</p>
            ";
            break;

        case 'booking_cancelled':
            $subject = "Phòng đặt đã bị hủy - " . $booking_data['order_id'];
            $content = "
                <h2 style='color:#dc3545;'>Phòng đặt đã bị hủy!</h2>
                <p>Xin chào <strong>{$user_name}</strong>,</p>
                <p>Chúng tôi rất tiếc phải thông báo rằng đặt phòng của bạn đã bị hủy.</p>
                
                <p style='background:#f8d7da; border:1px solid #f5c6cb; padding:10px; border-radius:5px; color:#721c24;'>
                    <strong>Lý do:</strong> " . ($booking_data['cancel_reason'] ?? 'Một số vấn đề khác') . "
                </p>
                
                <h3>Thông tin đặt phòng đã hủy:</h3>
                <ul>
                    <li><strong>Mã đơn hàng:</strong> {$booking_data['order_id']}</li>
                    <li><strong>Phòng:</strong> {$booking_data['room_name']}</li>
                    <li><strong>Ngày nhận phòng:</strong> {$booking_data['checkin']}</li>
                    <li><strong>Ngày trả phòng:</strong> {$booking_data['checkout']}</li>
                    <li><strong>Tổng tiền:</strong> " . number_format($booking_data['total_amount'], 0, ',', '.') . " vnđ</li>
                </ul>
                
                <h3>Thông tin liên hệ:</h3>
                <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi:</p>
                <ul>
                    <li><strong>Điện thoại:</strong> 0869034523</li>
                    <li><strong>Email:</strong> " . ($booking_data['contact_email'] ?? '23004122@gmail.com') . "</li>
                </ul>
                
                <p style='margin-top:20px;'>
                    <a href='https://www.facebook.com/share/1BXPDGyhHt/' style='color:#fff; background:#1877f2; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>
                        Liên hệ qua Facebook
                    </a>
                </p>
                
                <p>Trân trọng,<br>Homestay Vĩnh Long</p>
            ";
            break;

        default:
            return false;
    }

    // Kiểm tra xem đã cấu hình Gmail chưa
    if(SMTP_USERNAME == 'your-email@gmail.com') {
        error_log("⚠️  Gmail SMTP chưa được cấu hình. Vui lòng cập nhật inc/email_config.php");
        return true; // Return true để không block quy trình
    }

    // Tạo PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Người gửi
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Người nhận
        $mail->addAddress($user_email, $user_name);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        // Gửi email
        $mail->send();
        error_log("✓ Email sent successfully to: $user_email");
        return true;
        
    } catch (Exception $e) {
        error_log("✗ Email failed: {$mail->ErrorInfo}");
        return false;
    }
}

?>
