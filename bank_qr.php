<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================= LOGIN CHECK =================
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  header('Location: index.php');
  exit;
}

// ================= CHECK DATA =================
if (!isset($_SESSION['booking_data']) || !isset($_SESSION['room']) || !isset($_SESSION['payment_amount'])) {
  header('Location: payment.php');
  exit;
}

$booking_data = $_SESSION['booking_data'];
$ROOM = $_SESSION['room'];
$TXN_AMOUNT = $_SESSION['payment_amount'];
$TOTAL_AMOUNT = $_SESSION['total_amount'];
$CUST_ID = $_SESSION['uId'];

// Tạo ORDER_ID nếu chưa có
if (!isset($_SESSION['ORDER_ID'])) {
  $ORDER_ID = 'ORD_' . $CUST_ID . time();
  $_SESSION['ORDER_ID'] = $ORDER_ID;
} else {
  $ORDER_ID = $_SESSION['ORDER_ID'];
}

// Format số tiền
$formatted_amount = number_format($TXN_AMOUNT, 0, ',', '.');
$formatted_total = number_format($TOTAL_AMOUNT, 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo htmlspecialchars($settings_r['site_title']); ?> - CHUYỂN KHOẢN NGÂN HÀNG</title>
  <style>
    .qr-container {
      max-width: 600px;
      margin: 40px auto;
    }
    .qr-card {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      text-align: center;
    }
    .qr-image {
      max-width: 350px;
      width: 100%;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      margin: 20px 0;
    }
    .bank-info {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      margin: 20px 0;
      text-align: left;
    }
    .bank-info p {
      margin-bottom: 8px;
      font-size: 15px;
    }
    .bank-info strong {
      color: #333;
    }
    .amount-highlight {
      color: #2ec1ac;
      font-size: 22px;
      font-weight: bold;
    }
    .notice-box {
      background: #fff3cd;
      border: 1px solid #ffc107;
      border-radius: 8px;
      padding: 15px;
      margin: 20px 0;
      text-align: left;
    }
    .notice-box i {
      color: #856404;
      margin-right: 8px;
    }
    .notice-box strong {
      color: #856404;
    }
    .btn-continue {
      background: #2ec1ac;
      color: #fff;
      border: none;
      padding: 14px 40px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s;
    }
    .btn-continue:hover {
      background: #25a898;
    }
    .btn-back {
      background: #6c757d;
      color: #fff;
      border: none;
      padding: 10px 25px;
      font-size: 14px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s;
      margin-right: 10px;
    }
    .btn-back:hover {
      background: #5a6268;
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container qr-container">
    <div class="qr-card">
      <h3 class="fw-bold mb-3">
        <i class="bi bi-bank me-2"></i>
        CHUYỂN KHOẢN NGÂN HÀNG
      </h3>
      <p class="text-muted">Vui lòng quét mã QR hoặc chuyển khoản theo thông tin bên dưới</p>

      <!-- Ảnh QR -->
      <img src="images/qr_techcombank.jpg" alt="QR Techcombank" class="qr-image">

      <!-- Thông tin chuyển khoản -->
      <div class="bank-info">
        <p><strong>Ngân hàng:</strong> Techcombank</p>
        <p><strong>Số tài khoản:</strong> 1903 6363 8888 9999</p>
        <p><strong>Chủ tài khoản:</strong> HOMESTAY VINH LONG</p>
        <p><strong>Nội dung CK:</strong> <span class="text-primary fw-bold"><?php echo $ORDER_ID; ?></span></p>
        <p class="amount-highlight">Số tiền: <?php echo $formatted_amount; ?> vnđ</p>
      </div>

      <!-- Thông tin đơn hàng -->
      <div class="bank-info">
        <p><strong>Phòng:</strong> <?php echo htmlspecialchars($ROOM['name']); ?></p>
        <p><strong>Nhận phòng:</strong> <?php echo date('d/m/Y', strtotime($booking_data['checkin'])); ?></p>
        <p><strong>Trả phòng:</strong> <?php echo date('d/m/Y', strtotime($booking_data['checkout'])); ?></p>
        <p><strong>Tổng tiền:</strong> <?php echo $formatted_total; ?> vnđ</p>
        <p><strong>Thanh toán trước (50%):</strong> <?php echo $formatted_amount; ?> vnđ</p>
      </div>

      <!-- Lưu ý -->
      <div class="notice-box">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Lưu ý quan trọng:</strong>
        <ul class="mb-0 mt-2">
          <li>Vui lòng chuyển đúng số tiền <strong><?php echo $formatted_amount; ?> vnđ</strong></li>
          <li>Ghi đúng nội dung chuyển khoản: <strong><?php echo $ORDER_ID; ?></strong></li>
          <li>Sau khi chuyển khoản, nhấn <strong>"Tiếp tục"</strong> để hoàn tất đặt phòng</li>
          <li>Đơn hàng sẽ ở trạng thái <strong>"Chờ Thanh Toán"</strong> cho đến khi admin xác nhận</li>
        </ul>
      </div>

      <!-- Buttons -->
      <div class="mt-4">
        <a href="payment.php" class="btn btn-back text-decoration-none">
          <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
        <form method="POST" action="process_payment.php" style="display: inline;">
          <input type="hidden" name="payment_method" value="bank">
          <button type="submit" class="btn-continue">
            <i class="bi bi-check-circle-fill me-2"></i>
            Tiếp tục
          </button>
        </form>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>
</body>
</html>
