<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_bookings'])) {
  $frm_data = filteration($_POST);

  $query = "SELECT bo.*, bd.* FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?) 
      AND (bo.booking_status = ? AND bo.arrival = ?) ORDER BY bo.booking_id ASC";

  $res = select($query, ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", 'Đã Đặt', 0], 'ssssi');

  $i = 1;
  $table_data = "";

  if (mysqli_num_rows($res) == 0) {
    echo "<b>Không tìm thấy dữ liệu nào!</b>";
    exit;
  }

  while ($data = mysqli_fetch_assoc($res)) {
    $date = date("d-m-Y", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));
    $count_days = date_diff(new DateTime($checkin), new DateTime($checkout))->days; 

    // Định dạng giá phòng
    $price = number_format($data['price'], 0, ',', '.'); // Định dạng giá phòng
    $total_pay = number_format($data['total_pay'], 0, ',', '.'); // Định dạng tổng tiền

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
            <b>Thời Gian:</b> $count_days ngày
            <br>
            <b>Ngày Đặt:</b> $date
          </td>
          <td>
            <button type='button' onclick='kh_booking($data[booking_id])' class='mb-2 btn btn-outline-primary btn-sm fw-bold shadow-none'>
              <i class='bi bi-check2-square'></i> Xác Nhận Đặt Phòng
            </button>
            <br>
            <button type='button' onclick='huy_booking($data[booking_id])' class='mt-2 btn btn-outline-danger btn-sm fw-bold shadow-none'>
              <i class='bi bi-trash'></i> Huỷ Đặt Phòng
            </button>
          </td>
        </tr>
      ";

    $i++;
}

  echo $table_data;
}

if (isset($_POST['kh_booking'])) {
  $frm_data = filteration($_POST);

  $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
      ON bo.booking_id = bd.booking_id
      SET bo.booking_status = ?
      WHERE bo.booking_id = ?";

  $values = ['Đã Xác Nhận Đặt Phòng',$frm_data['booking_id']];

  $res = update($query, $values, 'si');
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


if (isset($_POST['huy_booking'])) {
  $frm_data = filteration($_POST);

  $query = "UPDATE `booking_order` bo INNER JOIN `rooms` r
  ON bo.room_id = r.id
  SET `booking_status`=?, `refund`=?
  WHERE `booking_id`=?";
  $values = ['Đã Huỷ', 0, $frm_data['booking_id']];
  $res = update($query, $values, 'sii');

  echo $res;

}


