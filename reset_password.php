<?php
  $page_title = "Đặt Lại Mật Khẩu";
  require('inc/links.php');

  // Kiểm tra token từ URL
  if (!isset($_GET['email']) || !isset($_GET['token'])) {
    header('Location: index.php');
    exit;
  }

  $email = $_GET['email'];
  $token = $_GET['token'];

  // Kiểm tra token có hợp lệ không
  $u_exist = select(
    "SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`>=? LIMIT 1",
    [$email, $token, date("Y-m-d")],
    "sss"
  );

  if (mysqli_num_rows($u_exist) == 0) {
    echo "<script>alert('Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn!'); window.location.href='index.php';</script>";
    exit;
  }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  <?php require('inc/links.php'); ?>
  <style>
    .reset-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .reset-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      padding: 40px;
      max-width: 450px;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="reset-container">
  <div class="reset-card">
    <div class="text-center mb-4">
      <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
      <h3 class="mt-3">Đặt Lại Mật Khẩu</h3>
      <p class="text-muted">Nhập mật khẩu mới của bạn</p>
    </div>

    <form id="reset-form">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

      <div class="mb-3">
        <label class="form-label">Mật Khẩu Mới</label>
        <input type="password" name="pass" class="form-control shadow-none" required minlength="8" maxlength="50">
        <div class="form-text">Tối thiểu 8 ký tự</div>
      </div>

      <div class="mb-4">
        <label class="form-label">Nhập Lại Mật Khẩu</label>
        <input type="password" name="cpass" class="form-control shadow-none" required minlength="8" maxlength="50">
      </div>

      <button type="submit" class="btn btn-primary w-100 shadow-none">
        <i class="bi bi-check-circle me-2"></i>Đặt Lại Mật Khẩu
      </button>

      <div class="text-center mt-3">
        <a href="index.php" class="text-decoration-none">Quay lại trang chủ</a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let reset_form = document.getElementById('reset-form');

  reset_form.addEventListener('submit', (e) => {
    e.preventDefault();
    
    let data = new FormData();
    data.append('reset_pass', '');
    data.append('email', reset_form.elements['email'].value);
    data.append('token', reset_form.elements['token'].value);
    data.append('pass', reset_form.elements['pass'].value);
    data.append('cpass', reset_form.elements['cpass'].value);

    var myModal = document.getElementById('alert-modal');
    var modal = bootstrap.Modal.getInstance(myModal);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/login_register.php", true);

    xhr.onload = function() {
      if (this.responseText == 'pass_short') {
        alert('Mật khẩu phải có ít nhất 8 ký tự!');
      }
      else if (this.responseText == 'pass_mismatch') {
        alert('Mật khẩu không khớp!');
      }
      else if (this.responseText == 'invalid_token') {
        alert('Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn!');
      }
      else if (this.responseText == 'upd_failed') {
        alert('Có lỗi xảy ra, vui lòng thử lại!');
      }
      else if (this.responseText == 1) {
        alert('Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay bây giờ.');
        window.location.href = 'index.php';
      }
      else {
        alert('Lỗi: ' + this.responseText);
      }
    }

    xhr.send(data);
  });
</script>

</body>
</html>
