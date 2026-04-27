<?php
session_start();
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

$resultCode = $_GET['resultCode'] ?? null;
$orderId = $_GET['orderId'] ?? '';

// Log để debug
$log = date('Y-m-d H:i:s') . "\nRETURN PARAMS:\n" . print_r($_GET, true) . "\n";
file_put_contents("momo_return_log.txt", $log, FILE_APPEND);

// Nếu thanh toán thành công
if ($resultCode == 0) {
    // Cập nhật trạng thái đơn hàng
    update(
        "UPDATE booking_order SET booking_status=?, payment_status=? WHERE order_id=?",
        ['Đã Thanh Toán', 'Đã Thanh Toán', $orderId],
        'sss'
    );
    
    $_SESSION['booking_success'] = "Thanh toán MoMo thành công! Mã đơn: " . $orderId;
    header("Location: bookings.php?pay_status=success");
    exit;
} else {
    // Thanh toán thất bại
    $message = $_GET['message'] ?? 'Thanh toán thất bại';
    $_SESSION['booking_error'] = $message . " (Mã lỗi: " . $resultCode . ")";
    header("Location: bookings.php?pay_status=failed");
    exit;
}
?>
