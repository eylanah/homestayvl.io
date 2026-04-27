<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - PHÒNG ĐẶT</title>
  <style>
    .toast-container {
      position: fixed;
      top: 80px;
      right: 20px;
      z-index: 9999;
    }
  </style>
</head>

<body class="bg-light">

  <?php
  require('inc/header.php');

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
  }
  ?>

  <!-- Toast Notification -->
  <div class="toast-container">
    <?php 
    if(isset($_SESSION['booking_success'])) {
      echo '<div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi bi-check-circle-fill me-2"></i>
            '.$_SESSION['booking_success'].'
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>';
      unset($_SESSION['booking_success']);
    }
    
    if(isset($_SESSION['booking_error'])) {
      echo '<div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi bi-x-circle-fill me-2"></i>
            '.$_SESSION['booking_error'].'
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>';
      unset($_SESSION['booking_error']);
    }
    ?>
  </div>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">ĐẶT PHÒNG</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">TRANG CHỦ</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">PHÒNG ĐẶT</a>
        </div>
      </div>

      <?php
$query = "SELECT bo.*, bd.* FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE bo.user_id=?
    ORDER BY bo.booking_id DESC";

$result = select($query, [$_SESSION['uId']], 'i');

  $visible_statuses = ['Chờ Thanh Toán', 'Đã Thanh Toán', 'Đã Thanh Toán 50%', 'Đã Xác Nhận Đặt Phòng', 'Đã Huỷ'];
$has_visible_booking = false;

while ($data = mysqli_fetch_assoc($result)) {
  if (!in_array($data['booking_status'], $visible_statuses, true)) {
    continue;
  }

  $has_visible_booking = true;

  $date = date("d-m-Y", strtotime($data['datentime']));
  $checkin = date("d-m-Y", strtotime($data['check_in']));
  $checkout = date("d-m-Y", strtotime($data['check_out']));

  $price = number_format($data['price'], 0, ',', '.');
  $total_pay = number_format($data['total_pay'], 0, ',', '.');

  $status_bg = 'bg-secondary';
  $status_note = '';

  if ($data['booking_status'] == 'Đã Huỷ') {
      $status_bg = 'bg-danger';
      $status_note = "<br><small class='text-muted'>Đơn đặt phòng đã bị huỷ.</small>";
  } elseif ($data['booking_status'] == 'Đã Xác Nhận Đặt Phòng') {
      $status_bg = 'bg-primary';
      $status_note = "<br><small class='text-muted'>Đơn đặt phòng đã được xác nhận.</small>";
  } elseif ($data['booking_status'] == 'Đã Thanh Toán') {
      $status_bg = 'bg-success';
      $status_note = "<br><small class='text-muted'>Đã thanh toán đủ.</small>";
  } elseif ($data['booking_status'] == 'Đã Thanh Toán 50%') {
      $status_bg = 'bg-info text-dark';
      $status_note = "<br><small class='text-muted'>Đã thanh toán trước 50%. Vui lòng thanh toán số còn lại khi nhận phòng.</small>";
  } elseif ($data['booking_status'] == 'Chờ Thanh Toán') {
      $status_bg = 'bg-warning text-dark';
      $status_note = "<br><small class='text-muted'>Vui lòng chuyển khoản theo hướng dẫn.</small>";
  }

  echo <<<bookings
      <div class='col-md-4 px-4 mb-4'>
        <div class='bg-white p-3 rounded shadow-sm'>
          <h5 class='fw-bold'>$data[room_name]</h5>
          <p>$price vnđ</p>
          <p>
            <b>Ngày Vào: </b> $checkin <br>
            <b>Ngày Trả: </b> $checkout
          </p>
          <p>
            <b>Tổng: </b> $total_pay vnđ <br>
            <b>ID Đơn: </b> $data[order_id] <br>
            <b>Ngày Đặt: </b> $date
          </p>
          <p>
            <span class='badge $status_bg'>$data[booking_status]</span>
            $status_note
          </p>
        </div>
      </div>
    bookings;
}

if (!$has_visible_booking) {
  echo <<<empty
      <div class='col-12 px-4'>
        <div class='alert alert-info shadow-sm mb-0'>
          Hiện chưa có đặt phòng nào ở trạng thái cần hiển thị.
        </div>
      </div>
    empty;
}
?>


    </div>
  </div>

  <!-- Form Đánh Giá -->
  <div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="review-form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-chat-square-heart-fill fs-3 me-2"></i> Đánh Giá
            </h5>
            <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Đánh Giá</label>
              <select class="form-select shadow-none" name="rating">
                <option value="5">Rất Tốt</option>
                <option value="4">Tốt</option>
                <option value="3">Tạm</option>
                <option value="2">Kém</option>
                <option value="1">Rất Tệ</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="form-label">Nhận Xét</label>
              <textarea name="review" rows="3" required class="form-control shadow-none"></textarea>
            </div>

            <input type="hidden" name="booking_id">
            <input type="hidden" name="room_id">

            <div class="text-end">
              <button type="submit" class="btn custom-bg text-white shadow-none">GỬI</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  
  <?php
    if (isset($_GET['cancel_status'])) {
        alert('success', 'Đặt phòng đã bị hủy!');
    } else if (isset($_GET['review_status'])) {
        alert('success', 'Cảm ơn bạn đã đánh giá!');
    } else if (isset($_GET['pay_status']) && $_GET['pay_status'] == 'success') {
        alert('success', 'Thanh toán trực tiếp thành công!');
    }
    ?>

  <?php require('inc/footer.php'); ?>

  <script>
    function cancel_booking(id) {
      if (confirm('Bạn có chắc chắn hủy đặt phòng không?')) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/cancel_booking.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if (this.responseText == 1) {
            window.location.href = "bookings.php?cancel_status=true";
          } else {
            alert('error', 'Hủy không thành công!');
          }
        }

        xhr.send('cancel_booking&id=' + id);
      }
    }

    let review_form = document.getElementById('review-form');

    function review_room(bid, rid) {
      review_form.elements['booking_id'].value = bid;
      review_form.elements['room_id'].value = rid;
    }

    review_form.addEventListener('submit', function(e) {
      e.preventDefault();

      let data = new FormData();
      data.append('review_form', '');
      data.append('rating', review_form.elements['rating'].value);
      data.append('review', review_form.elements['review'].value);
      data.append('booking_id', review_form.elements['booking_id'].value);
      data.append('room_id', review_form.elements['room_id'].value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/review_room.php", true);

      xhr.onload = function() {
        if (this.responseText == 1) {
          window.location.href = 'bookings.php?review_status=true';
        } else {
          var myModal = document.getElementById('reviewModal');
          var modal = bootstrap.Modal.getInstance(myModal);
          modal.hide();
          alert('error', "Xếp hạng & Đánh giá Không thành công!");
        }
      }

      xhr.send(data);
    });
  </script>

  <script>
    // Auto hide toast after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      var toastElList = [].slice.call(document.querySelectorAll('.toast'));
      var toastList = toastElList.map(function(toastEl) {
        var toast = new bootstrap.Toast(toastEl, {
          autohide: true,
          delay: 5000
        });
        toast.show();
        return toast;
      });
    });
  </script>
</body>

</html>
