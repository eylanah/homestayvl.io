<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// ================= CHECK LOGIN =================
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
  exit;
}

if (isset($_POST['pay_now'])) {

  $frm_data = filteration($_POST);

  $CUST_ID = $_SESSION['uId'];
  $ROOM = $_SESSION['room'];

  // ================= ORDER ID (QUAN TRỌNG) =================
  $ORDER_ID = 'ORD_' . $CUST_ID . time();
  $_SESSION['ORDER_ID'] = $ORDER_ID;

  // ================= PAYMENT =================
  $TXN_AMOUNT = $ROOM['payment']; // hoặc giá bạn tính
  $_SESSION['payment_amount'] = $TXN_AMOUNT;

  $TOTAL_AMOUNT = $ROOM['price'] * $frm_data['num_rooms'];
  $_SESSION['total_amount'] = $TOTAL_AMOUNT;

  $_SESSION['booking_data'] = [
    'checkin'  => $frm_data['checkin'],
    'checkout' => $frm_data['checkout'],
    'name'     => $frm_data['name'],
    'phonenum' => $frm_data['phonenum'],
    'address'  => $frm_data['address'],
    'num_rooms'=> $frm_data['num_rooms']
  ];

  // ================= INSERT BOOKING (pending) =================
  insert(
    "INSERT INTO booking_order
    (user_id, room_id, check_in, check_out, order_id, booking_status)
    VALUES (?,?,?,?,?,'pending')",
    [
      $CUST_ID,
      $ROOM['id'],
      $frm_data['checkin'],
      $frm_data['checkout'],
      $ORDER_ID
    ],
    'issss'
  );

  $booking_id = mysqli_insert_id($con);

  insert(
    "INSERT INTO booking_details
    (booking_id, room_name, price, total_pay, user_name, phonenum, address)
    VALUES (?,?,?,?,?,?,?)",
    [
      $booking_id,
      $ROOM['name'],
      $ROOM['price'],
      $TOTAL_AMOUNT,
      $frm_data['name'],
      $frm_data['phonenum'],
      $frm_data['address']
    ],
    'issssss'
  );

  // ================= CHUYỂN SANG MOMO =================
  header("Location: process_momo.php");
  exit;
}
