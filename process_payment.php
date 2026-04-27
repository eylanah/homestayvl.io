<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require('inc/email_functions.php');

date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();

function sendSuccessMail($CUST_ID, $ORDER_ID, $booking_data, $room, $TXN_AMOUNT, $TOTAL_AMOUNT) {

  $user_res = select("SELECT email, name FROM user_cred WHERE id=?", [$CUST_ID], 'i');
  $user = mysqli_fetch_assoc($user_res);

  $email_data = [
    'order_id' => $ORDER_ID,
    'room_name' => $room['name'],
    'checkin' => date('d/m/Y', strtotime($booking_data['checkin'])),
    'checkout' => date('d/m/Y', strtotime($booking_data['checkout'])),
    'paid_amount' => $TXN_AMOUNT,
    'remaining_amount' => $TOTAL_AMOUNT - $TXN_AMOUNT
  ];

  send_booking_email(
    $user['email'],
    $user['name'],
    $email_data,
    'payment_50_confirmed'
  );
}

// ================= LOGIN CHECK =================
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
  exit;
}

// ================= METHOD CHECK =================
if (!isset($_POST['payment_method'])) {
  redirect('payment.php');
  exit;
}

$frm_data = filteration($_POST);
$payment_method = $frm_data['payment_method'];

// ================= DATA =================
$booking_data = $_SESSION['booking_data'];
$CUST_ID = $_SESSION['uId'];
$ROOM_ID = $_SESSION['room']['id'];

$TXN_AMOUNT = $_SESSION['payment_amount'];
$TOTAL_AMOUNT = $_SESSION['total_amount'];

$ROOM = $_SESSION['room'];

// Nếu đã có ORDER_ID từ bank_qr.php thì giữ lại, không tạo mới
if (isset($_SESSION['ORDER_ID'])) {
  $ORDER_ID = $_SESSION['ORDER_ID'];
} else {
  $ORDER_ID = 'ORD_' . $CUST_ID . time();
  $_SESSION['ORDER_ID'] = $ORDER_ID;
}


// =================================================
// MOMO
// =================================================
if ($payment_method == 'ewallet') {

  insert("INSERT INTO booking_order(user_id, room_id, check_in, check_out, order_id, booking_status, payment_status) 
  VALUES (?,?,?,?,?,'pending','Chờ Thanh Toán')", [
    $CUST_ID,
    $ROOM_ID,
    $booking_data['checkin'],
    $booking_data['checkout'],
    $ORDER_ID
  ], 'issss');

  $_SESSION['temp_booking'] = [
    'room' => $ROOM,
    'booking_data' => $booking_data,
    'payment_amount' => $TXN_AMOUNT,
    'total_amount' => $TOTAL_AMOUNT,
    'user_id' => $CUST_ID,
    'order_id' => $ORDER_ID
  ];

  header("Location: process_momo.php");
  exit;
}


// =================================================
// CARD
// =================================================
if ($payment_method == 'card') {

  insert("INSERT INTO booking_order 
  (user_id, room_id, check_in, check_out, order_id, trans_amt, booking_status, payment_status, trans_status) 
  VALUES (?,?,?,?,?,?,'Đã Thanh Toán 50%','Đã Thanh Toán 50%','Đã nhận 50%')", [
    $CUST_ID,
    $ROOM_ID,
    $booking_data['checkin'],
    $booking_data['checkout'],
    $ORDER_ID,
    $TXN_AMOUNT
  ], 'issssi');

  $booking_id = mysqli_insert_id($con);

  insert("INSERT INTO booking_details
  (booking_id, room_name, price, total_pay, user_name, phonenum, address)
  VALUES (?,?,?,?,?,?,?)", [
    $booking_id,
    $ROOM['name'],
    $ROOM['price'],
    $TOTAL_AMOUNT,
    $booking_data['name'],
    $booking_data['phonenum'],
    $booking_data['address']
  ], 'issssss');

  // SEND MAIL
  sendSuccessMail($CUST_ID, $ORDER_ID, $booking_data, $ROOM, $TXN_AMOUNT, $TOTAL_AMOUNT);

  unset($_SESSION['booking_data']);
  unset($_SESSION['payment_amount']);
  unset($_SESSION['total_amount']);

  $_SESSION['booking_success'] = "Thanh toán thẻ thành công! Mã đơn: $ORDER_ID";

  redirect("bookings.php");
  exit;
}


// =================================================
// BANK TRANSFER (Chuyển khoản ngân hàng)
// =================================================
if ($payment_method == 'bank') {

  insert("INSERT INTO booking_order 
  (user_id, room_id, check_in, check_out, order_id, trans_amt, booking_status, payment_status, trans_status) 
  VALUES (?,?,?,?,?,?,'Chờ Thanh Toán','Chờ Thanh Toán','Chờ chuyển khoản')", [
    $CUST_ID,
    $ROOM_ID,
    $booking_data['checkin'],
    $booking_data['checkout'],
    $ORDER_ID,
    $TXN_AMOUNT
  ], 'issssi');

  $booking_id = mysqli_insert_id($con);

  insert("INSERT INTO booking_details
  (booking_id, room_name, price, total_pay, user_name, phonenum, address)
  VALUES (?,?,?,?,?,?,?)", [
    $booking_id,
    $ROOM['name'],
    $ROOM['price'],
    $TOTAL_AMOUNT,
    $booking_data['name'],
    $booking_data['phonenum'],
    $booking_data['address']
  ], 'issssss');

  // SEND MAIL
  sendSuccessMail($CUST_ID, $ORDER_ID, $booking_data, $ROOM, $TXN_AMOUNT, $TOTAL_AMOUNT);

  unset($_SESSION['booking_data']);
  unset($_SESSION['payment_amount']);
  unset($_SESSION['total_amount']);
  unset($_SESSION['ORDER_ID']);

  $_SESSION['booking_success'] = "Đặt phòng thành công! Vui lòng chuyển khoản theo hướng dẫn. Mã đơn: $ORDER_ID";

  redirect("bookings.php");
  exit;
}


// =================================================
// 👉 FALLBACK
// =================================================
echo "Payment method invalid!";
exit;
