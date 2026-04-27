<?php 
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (isset($_POST['check_availability'])) {
    $frm_data = filteration($_POST);
    $status = "";
    $result = "";

    // Kiểm tra ngày nhận và trả phòng
    $today_date = new DateTime(date("Y-m-d"));
    $checkin_date = new DateTime($frm_data['check_in']);
    $checkout_date = new DateTime($frm_data['check_out']);

    if ($checkin_date == $checkout_date) {
        $status = 'check_in_out_equal';
    } elseif ($checkout_date < $checkin_date) {
        $status = 'check_out_earlier';
    } elseif ($checkin_date < $today_date) {
        $status = 'check_in_earlier';
    } else {
        // Thêm kiểm tra số ngày đặt phòng không vượt quá 1 tháng
        $interval = $checkin_date->diff($checkout_date);
        $days_diff = $interval->days;

        if ($days_diff > 30) {
            $status = 'checkout_exceeds_one_month';
        }
    }

    if ($status != '') {
        $result = json_encode(["status" => $status]);
        echo $result;
        exit;
    } else {
        session_start();

        // Kiểm tra số lượng phòng đã đặt
        $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
            WHERE booking_status IN ('Đã Đặt', 'Đã Xác Nhận Đặt Phòng')
            AND room_id=?
            AND NOT (check_out < ? OR check_in > ?)";

        $values = [$_SESSION['room']['id'], $frm_data['check_in'], $frm_data['check_out']];
        $tb_result = select($tb_query, $values, 'iss');
        $tb_fetch = mysqli_fetch_assoc($tb_result);

        // Kiểm tra số lượng phòng còn trống
        $rq_query = "SELECT `quantity` FROM `rooms` WHERE `id`=?";
        $rq_result = select($rq_query, [$_SESSION['room']['id']], 'i');
        $rq_fetch = mysqli_fetch_assoc($rq_result);

        if ($rq_fetch) {
            $count_rooms = $rq_fetch['quantity'] - $tb_fetch['total_bookings'];
        } else {
            $count_rooms = 0;
        }

        $num_rooms = $frm_data['num_rooms'];

        if ($num_rooms > $count_rooms) {
            $status = 'not_enough_rooms';
            $result = json_encode(['status' => $status]);
            echo $result;
            exit;
        }

        if ($count_rooms == 0) {
            $status = 'unavailable';
            $result = json_encode(['status' => $status]);
            echo $result;
            exit;
        }

        // Tính toán tổng số tiền
        $count_days = $checkout_date->diff($checkin_date)->days;
        $payment = $_SESSION['room']['price'] * $num_rooms * $count_days;

        $_SESSION['room']['payment'] = $payment;
        $_SESSION['room']['available'] = true;

        $result = json_encode(["status" => 'available', "c_rooms" => $count_rooms, "days" => $count_days, "payment" => $payment]);
        echo $result;
    }
}
?>
