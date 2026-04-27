<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
require('../../inc/email_functions.php');
adminLogin();

if (isset($_POST['get_bookings'])) {
  $frm_data = filteration($_POST);

  $query = "SELECT bo.*, bd.* FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?) 
      AND (
        (bo.payment_status IN ('Chờ Thanh Toán', 'Đã Thanh Toán 50%'))
        OR (bo.payment_status IS NULL AND bo.booking_status IN ('Chờ Thanh Toán', 'Đã Thanh Toán 50%'))
        OR (bo.payment_status = '' AND bo.booking_status IN ('Chờ Thanh Toán', 'Đã Thanh Toán 50%'))
      ) AND bo.arrival = ? 
      ORDER BY bo.booking_id DESC";

  $res = select($query, ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", 0], 'sssi');

  $i = 1;
  $table_data = "";

  if (mysqli_num_rows($res) == 0) {
    echo "<b>Không tìm thấy dữ liệu nào!</b>";
    exit;
  }

  while ($data = mysqli_fetch_assoc($res)) {
    $date_now = date("d-m-Y", strtotime(date("d-m-Y")));
    $date = date("d-m-Y", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));
    $count_days = date_diff(new DateTime($checkin), new DateTime($checkout))->days;
    $time_out = date_diff(new DateTime($date_now), new DateTime($checkout))->days;
    
    $price = number_format($data['price'], 0, ',', '.');
    $total_pay = number_format($data['total_pay'], 0, ',', '.');
    if(new DateTime($date_now) >= new DateTime($checkout)){
      $han_phong = "<span class='badge bg-warning'>Đã Hết Hạn</span>";
     
    }
    else{
      $han_phong = $time_out .' '."ngày";
    }

    // Xác định nút hiển thị dựa trên payment_status
    $action_buttons = "";
    if($data['payment_status'] == 'Chờ Thanh Toán') {
      $action_buttons = "
        <button type='button' onclick='confirm_payment($data[booking_id])' class='mb-2 btn btn-success btn-sm fw-bold shadow-none'>
          <i class='bi bi-check2-square'></i> Xác Nhận Đã Nhận 50%
        </button>
        <br>
        <button type='button' onclick='cancel_booking($data[booking_id])' class='mt-2 btn btn-outline-danger btn-sm fw-bold shadow-none'>
          <i class='bi bi-trash'></i> Huỷ Đặt Phòng
        </button>
      ";
    } else if($data['payment_status'] == 'Đã Thanh Toán 50%') {
      $action_buttons = "
        <span class='badge bg-success mb-2'>Đã nhận 50%</span>
        <br>
        <button type='button' onclick='confirm_full_payment($data[booking_id])' class='mb-2 btn btn-primary btn-sm fw-bold shadow-none'>
          <i class='bi bi-cash-stack'></i> Xác Nhận Đã Nhận Đủ
        </button>
        <br>
        <button type='button' onclick='cancel_booking($data[booking_id])' class='mt-2 btn btn-outline-danger btn-sm fw-bold shadow-none'>
          <i class='bi bi-trash'></i> Huỷ Đặt Phòng
        </button>
      ";
    }

    $table_data .= "
        <tr>
          <td>$i</td>
          <td>
            <span class='badge bg-primary'>
              ID Đặt Phòng: $data[order_id]
            </span>
            <br>
            <b>Tên:</b> $data[user_name]
            <br>
            <b>Điện Thoại:</b> $data[phonenum]
          </td>
          <td>
            <b>Phòng:</b> $data[room_name]
            <br>
            <b>Giá:</b> $price vnđ
            <br>
            <b>Tổng:</b> $total_pay vnđ
          </td>
          <td>
            <b>Ngày Vào:</b> $checkin
            <br>
            <b>Ngày Trả:</b> $checkout
            <br>
            <b>Thờng Gian:</b> $count_days ngày
            <br>
            <b>Thờng Gian Còn Lại:</b> $han_phong

          </td>
          <td>
            $action_buttons
          </td>
        </tr>
      ";

    $i++;
  }

  echo $table_data;
}

if (isset($_POST['payment_booking'])) {
  $frm_data = filteration($_POST);

  $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
      ON bo.booking_id = bd.booking_id INNER JOIN `rooms` r
      ON bo.room_id = r.id
      SET bo.arrival = ?, bo.booking_status = ?, bo.trans_amt = ?, bo.trans_status=?
      WHERE bo.booking_id = ?";

  $values = [1, $frm_data['booking_status'], $frm_data['trans_amt'], $frm_data['trans_status'],$frm_data['booking_id']];

  $res = update($query, $values, 'isssi');
  echo $res;
}


if (isset($_POST['assign_room'])) {
  $frm_data = filteration($_POST);

  $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
      ON bo.booking_id = bd.booking_id
      SET bo.arrival = ?, bo.rate_review = ?, bd.room_no = ? 
      WHERE bo.booking_id = ?";

  $values = [1, 0, $frm_data['room_no'], $frm_data['booking_id']];

  $res = update($query, $values, 'iisi'); 

  echo ($res == 2) ? 1 : 0;
}


if (isset($_POST['confirm_payment'])) {
  $frm_data = filteration($_POST);

  // Lấy total_pay từ booking_details để tính 50% chính xác
  $query_get = "SELECT bd.total_pay FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      WHERE bo.booking_id = ?";
  $result = select($query_get, [$frm_data['booking_id']], 'i');
  $booking = mysqli_fetch_assoc($result);
  $half_amount = $booking['total_pay'] * 0.5;

  $query = "UPDATE `booking_order` 
      SET `booking_status` = 'Đã Thanh Toán 50%', 
          `payment_status` = 'Đã Thanh Toán 50%', 
          `trans_status` = 'Đã nhận 50%',
          `trans_amt` = ?
      WHERE `booking_id` = ?";

  $values = [$half_amount, $frm_data['booking_id']];
  $res = update($query, $values, 'ii');

  if($res) {
    // Lấy thông tin booking và user để gửi email
    $booking_query = "SELECT bo.*, bd.*, uc.email, uc.name as user_name 
                      FROM booking_order bo
                      INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
                      INNER JOIN user_cred uc ON bo.user_id = uc.id
                      WHERE bo.booking_id = ?";
    $booking_result = select($booking_query, [$frm_data['booking_id']], 'i');
    $booking = mysqli_fetch_assoc($booking_result);

    $email_data = [
      'order_id' => $booking['order_id'],
      'room_name' => $booking['room_name'],
      'checkin' => date('d/m/Y', strtotime($booking['check_in'])),
      'checkout' => date('d/m/Y', strtotime($booking['check_out'])),
      'paid_amount' => $half_amount,
      'remaining_amount' => $booking['total_pay'] - $half_amount
    ];

    send_booking_email($booking['email'], $booking['user_name'], $email_data, 'payment_50_confirmed');
  }

  echo $res;
}

if (isset($_POST['confirm_full_payment'])) {
  $frm_data = filteration($_POST);

  // Lấy total_pay từ booking_details để set đúng số tiền đầy đủ
  $query_get = "SELECT bd.total_pay FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      WHERE bo.booking_id = ?";
  $result = select($query_get, [$frm_data['booking_id']], 'i');
  $booking = mysqli_fetch_assoc($result);
  $full_amount = $booking['total_pay'];

  $query = "UPDATE `booking_order` 
    SET `booking_status` = 'Đã Thanh Toán', 
        `payment_status` = 'Đã Thanh Toán', 
        `trans_status` = 'Thành Công', 
        `trans_amt` = ?,
        `arrival` = 1
    WHERE `booking_id` = ?";

  $values = [$full_amount, $frm_data['booking_id']];
  $res = update($query, $values, 'ii');

  if($res) {
    // Lấy thông tin booking và user để gửi email
    $booking_query = "SELECT bo.*, bd.*, uc.email, uc.name as user_name 
                      FROM booking_order bo
                      INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
                      INNER JOIN user_cred uc ON bo.user_id = uc.id
                      WHERE bo.booking_id = ?";
    $booking_result = select($booking_query, [$frm_data['booking_id']], 'i');
    $booking = mysqli_fetch_assoc($booking_result);

    $email_data = [
      'order_id' => $booking['order_id'],
      'room_name' => $booking['room_name'],
      'checkin' => date('d/m/Y', strtotime($booking['check_in'])),
      'checkout' => date('d/m/Y', strtotime($booking['check_out'])),
      'total_amount' => $full_amount
    ];

    send_booking_email($booking['email'], $booking['user_name'], $email_data, 'payment_full_confirmed');
  }

  echo $res;
}


if (isset($_POST['cancel_booking'])) {
  $frm_data = filteration($_POST);

  // Lấy thông tin booking trước khi hủy để xác định lý do và gửi email
  $booking_query = "SELECT bo.*, bd.*, uc.email, uc.name as user_name 
                    FROM booking_order bo
                    INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
                    INNER JOIN user_cred uc ON bo.user_id = uc.id
                    WHERE bo.booking_id = ?";
  $booking_result = select($booking_query, [$frm_data['booking_id']], 'i');
  $booking = mysqli_fetch_assoc($booking_result);

  // Xác định lý do hủy dựa trên payment_status hiện tại
  $cancel_reason = 'Một số vấn đề khác';
  if ($booking['payment_status'] == 'Chờ Thanh Toán') {
    $cancel_reason = 'chưa thanh toán';
  } else if ($booking['payment_status'] == 'Đã Thanh Toán 50%') {
    $cancel_reason = 'chưa thanh toán đủ tiền';
  }

  $query = "UPDATE `booking_order` 
    SET `booking_status` = 'Đã Huỷ', 
        `payment_status` = 'Đã Huỷ', 
        `trans_status` = 'Đã Huỷ',
        `trans_amt` = 0,
        `arrival` = 1
    WHERE `booking_id` = ?";
  $values = [$frm_data['booking_id']];
  $res = update($query, $values, 'i');

  if ($res) {
    $email_data = [
      'order_id' => $booking['order_id'],
      'room_name' => $booking['room_name'],
      'checkin' => date('d/m/Y', strtotime($booking['check_in'])),
      'checkout' => date('d/m/Y', strtotime($booking['check_out'])),
      'total_amount' => $booking['total_pay'],
      'cancel_reason' => $cancel_reason,
      'contact_email' => '23004122@gmail.com'
    ];

    send_booking_email($booking['email'], $booking['user_name'], $email_data, 'booking_cancelled');
  }

  echo $res;

}