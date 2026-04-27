<?php
require('admin/inc/db_config.php');

// lấy dữ liệu MoMo gửi về (POST JSON)
$data = json_decode(file_get_contents("php://input"), true);

// log để debug
file_put_contents("momo_log.txt", print_r($data, true));

// kiểm tra dữ liệu
if (!$data || !isset($data['orderId']) || !isset($data['resultCode'])) {
    http_response_code(400);
    exit("No data from MoMo");
}

// lấy data
$orderId = $data['orderId'];
$resultCode = $data['resultCode'];

// nếu thanh toán thành công
if ($resultCode == 0) {

    update(
        "UPDATE booking_order SET booking_status=?, payment_status=? WHERE order_id=?",
        ['Đã Thanh Toán', 'Đã Thanh Toán', $orderId],
        'sss'
    );

} else {

    update(
        "UPDATE booking_order SET booking_status=?, payment_status=? WHERE order_id=?",
        ['Thanh toán thất bại', 'Thanh toán thất bại', $orderId],
        'sss'
    );
}

// trả về cho MoMo
echo json_encode(["status" => "ok"]);