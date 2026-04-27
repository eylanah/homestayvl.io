<?php 

  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/mpdf/vendor/autoload.php');

  adminLogin();

  if(isset($_GET['gen_pdf']) && isset($_GET['id']))
  {
    $frm_data = filteration($_GET);
    $query = "SELECT bo.*, bd.*,uc.email FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      INNER JOIN `user_cred` uc ON bo.user_id = uc.id
      WHERE ((bo.booking_status='Đã Thanh Toán') 
      OR (bo.booking_status='Đã Thanh Toán 50%')
      OR (bo.booking_status='Đã Huỷ')
      OR (bo.booking_status='Đã Xác Nhận Đặt Phòng')) 
      AND bo.booking_id = '$frm_data[id]'";

    $res = mysqli_query($con,$query);
    $total_rows = mysqli_num_rows($res);

    if($total_rows==0){
      header('location: dashboard.php');
      exit;
    }

    $data = mysqli_fetch_assoc($res);

    $date = date("H:i | d-m-Y",strtotime($data['datentime']));
    $checkin = date("d-m-Y",strtotime($data['check_in']));
    $checkout = date("d-m-Y",strtotime($data['check_out']));
    
    // Format prices
    $price = number_format($data['price'], 0, ',', '.');
    $trans_amt = number_format($data['trans_amt'], 0, ',', '.');
    
    // Calculate payment details based on status
    $total_amount = $data['price'];
    $paid_amount = $data['trans_amt'];
    $remaining_amount = 0;
    
    if($data['booking_status'] == 'Đã Thanh Toán 50%') {
      $remaining_amount = $total_amount - $paid_amount;
      $remaining_formatted = number_format($remaining_amount, 0, ',', '.');
      $payment_info = "Đã thanh toán 50%: $trans_amt vnđ<br>Còn lại (thanh toán tại lễ tân): $remaining_formatted vnđ";
    } else if($data['booking_status'] == 'Đã Thanh Toán') {
      $payment_info = "Đã thanh toán đầy đủ: $trans_amt vnđ";
    } else {
      $payment_info = "Số tiền: $trans_amt vnđ";
    }

    $table_data = "

    <h2 style='text-align: center'>KHÁCH SẠN VINH HOTEL</h2>
    <h3 style='text-align: center'>HOÁ ĐƠN</h3>
    <table border='1' style='margin: auto; border-collapse: collapse; width: 90%;'>
      <tr>
        <td style='padding: 8px;'><b>ID Đơn:</b> $data[order_id]</td>
        <td style='padding: 8px;'><b>Ngày Đặt:</b> $date</td>
      </tr>
      <tr>
        <td colspan='2' style='padding: 8px;'><b>Trạng Thái:</b> $data[booking_status]</td>
      </tr>
      <tr>
        <td style='padding: 8px;'><b>Tên:</b> $data[user_name]</td>
        <td style='padding: 8px;'><b>Email:</b> $data[email]</td>
      </tr>
      <tr>
        <td style='padding: 8px;'><b>Số Điện Thoại:</b> $data[phonenum]</td>
        <td style='padding: 8px;'><b>Địa Chỉ:</b> $data[address]</td>
      </tr>
      <tr>
        <td style='padding: 8px;'><b>Tên Phòng:</b> $data[room_name]</td>
        <td style='padding: 8px;'><b>Giá Phòng:</b> $price vnđ</td>
      </tr>
      <tr>
        <td style='padding: 8px;'><b>Ngày Vào:</b> $checkin</td>
        <td style='padding: 8px;'><b>Ngày Ra:</b> $checkout</td>
      </tr>
      <tr>
        <td colspan='2' style='padding: 8px;'><b>Thông Tin Thanh Toán:</b><br>$payment_info</td>
      </tr>
    ";

    // if($data['booking_status']=='Đã Huỷ')
    // {
    //   $refund = ($data['refund']) ? "Amount Refunded" : "Not Yet Refunded";

    //   $table_data.="<tr>
    //     <td>Amount Paid: ₹$data[trans_amt]</td>
    //     <td>Refund: $refund</td>
    //   </tr>";
    // }
    // else if($data['booking_status']=='payment failed')
    // {
    //   $table_data.="<tr>
    //     <td>Transaction Amount: ₹$data[trans_amt]</td>
    //     <td>Failure Response: $data[trans_resp_msg]</td>
    //   </tr>";
    // }
    // else
    // {
    //   $table_data.="<tr>
    //     <td>Room Number: $data[room_no]</td>
    //     <td>Amount Paid: ₹$data[trans_amt]</td>
    //   </tr>";
    // }

    $table_data.="</table>";

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($table_data);
    $mpdf->Output($data['order_id'].'.pdf','D');

  }
  else{
    header('location: dashboard.php');
  }
  
?>