<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo htmlspecialchars($settings_r['site_title']); ?> - THANH TOÁN</title>
  <style>
    .payment-container {
      max-width: 900px;
      margin: 40px auto;
    }
    .payment-method {
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      cursor: pointer;
      transition: all 0.3s;
    }
    .payment-method:hover {
      border-color: #2ec1ac;
      background-color: #f8f9fa;
    }
    .payment-method.active {
      border-color: #2ec1ac;
      background-color: #e8f5f3;
    }
    .payment-method input[type="radio"] {
      width: 20px;
      height: 20px;
      margin-right: 15px;
    }
    .payment-icon {
      width: 40px;
      height: 40px;
      margin-right: 15px;
    }
    .booking-summary {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      position: sticky;
      top: 20px;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid #dee2e6;
    }
    .summary-row:last-child {
      border-bottom: none;
      font-weight: bold;
      font-size: 18px;
      color: #2ec1ac;
    }
    .secure-badge {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-top: 15px;
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <?php
  if (!isset($_POST['pay_now']) || !isset($_SESSION['login']) || $_SESSION['login'] != true) {
    redirect('rooms.php');
  }

  $data = filteration($_POST);
  
  // Lưu thông tin booking vào session
  $_SESSION['booking_data'] = [
    'name' => $data['name'],
    'phonenum' => $data['phonenum'],
    'address' => $data['address'],
    'num_rooms' => $data['num_rooms'],
    'checkin' => $data['checkin'],
    'checkout' => $data['checkout']
  ];

  // Tính toán số ngày và tổng tiền
  $checkin_date = new DateTime($data['checkin']);
  $checkout_date = new DateTime($data['checkout']);
  $days = $checkout_date->diff($checkin_date)->days;
  
  $room_price = $_SESSION['room']['price'];
  $num_rooms = $data['num_rooms'];
  $total_amount = $room_price * $days * $num_rooms;
  $deposit_amount = $total_amount * 0.5; // Thanh toán trước 50%
  
  $_SESSION['payment_amount'] = $deposit_amount;
  $_SESSION['total_amount'] = $total_amount;
  $_SESSION['remaining_amount'] = $total_amount - $deposit_amount;
  ?>

  <div class="container payment-container">
    <div class="row">
      <div class="col-12 mb-4">
        <h2 class="fw-bold">CHỌN PHƯƠNG THỨC THANH TOÁN</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">TRANG CHỦ</a>
          <span class="text-secondary"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none">PHÒNG</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">THANH TOÁN</a>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-body">
            <h5 class="mb-4">Chọn phương thức thanh toán</h5>
            
            <form action="process_payment.php" method="POST" id="payment_form">
            <!-- Chuyển khoản ngân hàng -->
              <div class="payment-method" onclick="selectPayment('bank')">
                <div class="d-flex align-items-center">
                  <input type="radio" name="payment_method" value="bank" id="bank" required>
                  <div class="payment-icon">
                    <i class="bi bi-bank" style="font-size: 40px; color: #007bff;"></i>
                  </div>
                  <div>
                    <strong>Chuyển khoản ngân hàng</strong>
                    <p class="mb-0 text-muted small">Chuyển khoản qua tài khoản ngân hàng (Techcombank)</p>
                  </div>
                </div>
              </div> 
              

              <!-- Thẻ tín dụng/ghi nợ -->
               <!-- <div class="payment-method" onclick="selectPayment('card')">
                <div class="d-flex align-items-center">
                  <input type="radio" name="payment_method" value="card" id="card">
                  <div class="payment-icon">
                    <i class="bi bi-credit-card" style="font-size: 40px; color: #6c757d;"></i>
                  </div>
                  <div>
                    <strong>Thẻ tín dụng / Thẻ ghi nợ</strong>
                    <p class="mb-0 text-muted small">Visa, Mastercard, JCB</p>
                  </div>
                </div>
              </div> -->

              <!-- Ví điện tử -->
              <div class="payment-method" onclick="selectPayment('ewallet')">
                <div class="d-flex align-items-center">
                  <input type="radio" name="payment_method" value="ewallet" id="ewallet">
                  <div class="payment-icon">
                    <i class="bi bi-wallet2" style="font-size: 40px; color: #ff6b6b;"></i>
                  </div>
                  <div>
                    <strong>Ví điện tử</strong>
                    <p class="mb-0 text-muted small">MOMO</p>
                  </div>
                </div>
              </div>

              <div class="secure-badge">
                <i class="bi bi-shield-check me-2"></i>
                Thanh toán an toàn và bảo mật
              </div>

              <button type="button" onclick="handleSubmit()" class="btn custom-bg text-white w-100 mt-4 py-3">
                <i class="bi bi-lock-fill me-2"></i>
                XÁC NHẬN THANH TOÁN
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="booking-summary">
          <h5 class="mb-3">Tóm tắt đặt phòng</h5>
          
          <div class="summary-row">
            <span>Phòng:</span>
            <strong><?php echo htmlspecialchars($_SESSION['room']['name']); ?></strong>
          </div>
          
          <div class="summary-row">
            <span>Nhận phòng:</span>
            <strong><?php echo date('d/m/Y', strtotime($data['checkin'])); ?></strong>
          </div>
          
          <div class="summary-row">
            <span>Trả phòng:</span>
            <strong><?php echo date('d/m/Y', strtotime($data['checkout'])); ?></strong>
          </div>
          
          <div class="summary-row">
            <span>Số đêm:</span>
            <strong><?php echo $days; ?> đêm</strong>
          </div>
          
          <div class="summary-row">
            <span>Số phòng:</span>
            <strong><?php echo $num_rooms; ?> phòng</strong>
          </div>
          
          <div class="summary-row">
            <span>Giá mỗi đêm:</span>
            <strong><?php echo number_format($room_price, 0, ',', '.'); ?> vnđ</strong>
          </div>
          
          <div class="summary-row">
            <span>Tổng tiền phòng:</span>
            <strong><?php echo number_format($total_amount, 0, ',', '.'); ?> vnđ</strong>
          </div>
          
          <div class="alert alert-info mt-3 mb-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Thanh toán trước 50%</strong><br>
            <small>Còn lại thanh toán tại quầy lễ tân khi nhận phòng</small>
          </div>
          
          <div class="summary-row">
            <span>CẦN THANH TOÁN TRƯỚC:</span>
            <strong><?php echo number_format($total_amount * 0.5, 0, ',', '.'); ?> vnđ</strong>
          </div>

          <div class="mt-3 p-3 bg-white rounded">
            <h6>Thông tin khách hàng</h6>
            <p class="mb-1"><strong>Tên:</strong> <?php echo htmlspecialchars($data['name']); ?></p>
            <p class="mb-1"><strong>SĐT:</strong> <?php echo htmlspecialchars($data['phonenum']); ?></p>
            <p class="mb-0"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($data['address']); ?></p>
          </div>
          
          <div class="mt-3 p-3 bg-light rounded border">
            <h6 class="text-muted">Thanh toán tại lễ tân</h6>
            <p class="mb-0"><strong class="text-success"><?php echo number_format($total_amount * 0.5, 0, ',', '.'); ?> vnđ</strong></p>
            <small class="text-muted">Số tiền còn lại khi nhận phòng</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  
  <script>
function selectPayment(method) {
  document.querySelectorAll('.payment-method').forEach(el => {
    el.classList.remove('active');
  });

  let radio = document.getElementById(method);
  radio.checked = true;
  radio.closest('.payment-method').classList.add('active');
}

document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.payment-method').forEach(el => {
      el.classList.remove('active');
    });
    this.closest('.payment-method').classList.add('active');
  });
});

function checkPayment() {
  let selected = document.querySelector('input[name="payment_method"]:checked');

  if (!selected) {
    alert("Vui lòng chọn phương thức thanh toán!");
    return false;
  }

  return true;
}
</script>

<script>
function handleSubmit() {
  let selected = document.querySelector('input[name="payment_method"]:checked');

  if (!selected) {
    alert("Vui lòng chọn phương thức thanh toán!");
    return;
  }

  // Nếu chọn chuyển khoản ngân hàng, chuyển sang trang QR
  if (selected.value === 'bank') {
    window.location.href = 'bank_qr.php';
    return;
  }

  document.getElementById("payment_form").submit();
}
</script>
</body>
</html>
