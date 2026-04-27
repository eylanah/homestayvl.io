<nav id="nav-bar" 
class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3" href="index.php"><?php echo $settings_r['site_title'] ?></a>
    <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link me-2" href="index.php">Trang Chủ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="rooms.php">Phòng</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="facilities.php">Tiện Nghi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="contact.php">Liên Hệ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">Giới Thiệu</a>
        </li>
      </ul>
      <div class="d-flex">
        <?php 
          if(isset($_SESSION['login']) && $_SESSION['login']==true)
          {
            $path = USERS_IMG_PATH;
            echo<<<data
              <div class="btn-group">
                <button type="button" class="btn btn-outline-dark shadow-none dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                  <img src="$path$_SESSION[uPic]" style="width: 25px; height: 25px;" class="me-1 rounded-circle">
                  $_SESSION[uName]
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                  <li><a class="dropdown-item" href="profile.php">Thông Tin</a></li>
                  <li><a class="dropdown-item" href="bookings.php">Phòng Đặt</a></li>
                  <li><a class="dropdown-item" href="logout.php">Đăng Xuất</a></li>
                </ul>
              </div>
            data;
          }
          else
          {
            echo<<<data
              <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                Đăng Nhập
              </button>
              <button type="button" class="btn btn-outline-dark shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                Đăng Ký
              </button>
            data;
          }
        ?>
      </div>
    </div>
  </div>
</nav>

<!-- ==================== LOGIN MODAL ==================== -->
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="login-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-circle fs-3 me-2"></i> Khách Hàng Đăng Nhập
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Email / SĐT</label>
            <input type="text" name="email_mob" required class="form-control shadow-none">
          </div>
          <div class="mb-4">
            <label class="form-label">Mật Khẩu</label>
            <input type="password" name="pass" required class="form-control shadow-none">
          </div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <button type="submit" class="btn btn-dark shadow-none">Đăng Nhập</button>
            <button type="button" class="btn text-secondary text-decoration-none shadow-none p-0"
              data-bs-toggle="modal" data-bs-target="#forgotModal" data-bs-dismiss="modal">
              Quên Mật Khẩu?
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== REGISTER MODAL ==================== -->
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="register-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-lines-fill fs-3 me-2"></i> Khách Hàng Đăng Ký
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Họ và Tên</label>
                <input name="name" type="text" class="form-control shadow-none" required minlength="3" maxlength="50">
                <div class="form-text">Từ 3-50 ký tự</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Số Điện Thoại</label>
                <input name="phonenum" type="tel" class="form-control shadow-none" required pattern="[0-9]{10}" maxlength="10">
                <div class="form-text">10 chữ số</div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Địa chỉ</label>
                <textarea name="address" class="form-control shadow-none" rows="1" required></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Giới Tính</label>
                <select name="gender" class="form-select shadow-none" required>
                  <option value="" selected disabled>-- Chọn giới tính --</option>
                  <option value="Nam">Nam</option>
                  <option value="Nữ">Nữ</option>
                  <option value="Khác">Khác</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Ngày Sinh</label>
                <input name="dob" type="date" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Mật Khẩu</label>
                <input name="pass" type="password" class="form-control shadow-none" required minlength="8" maxlength="50">
                <div class="form-text">Tối thiểu 8 ký tự</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Nhập Lại Mật Khẩu</label>
                <input name="cpass" type="password" class="form-control shadow-none" required minlength="8" maxlength="50">
              </div>
            </div>
          </div>
          <div class="text-center my-1">
            <button type="submit" class="btn btn-dark shadow-none">ĐĂNG KÝ</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== FORGOT MODAL (3 BƯỚC) ==================== -->
<div class="modal fade" id="forgotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center" id="forgotModalTitle">
          <i class="bi bi-shield-lock fs-3 me-2"></i>
          <span id="forgotTitleText">Quên Mật Khẩu</span>
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
          aria-label="Close" onclick="fpGoStep(1)"></button>
      </div>

      <div class="modal-body">

        <!-- ── BƯỚC 1: Nhập Email ── -->
        <div id="fp-step-1">
          <p class="text-muted small mb-3">
            Nhập email đã đăng ký. Chúng tôi sẽ gửi mã PIN <strong>6 chữ số</strong>
            có hiệu lực trong <strong>15 phút</strong>.
          </p>
          <div class="mb-4">
            <label class="form-label">Email</label>
            <input type="email" id="fp-email" class="form-control shadow-none"
                   placeholder="example@gmail.com" required>
          </div>
          <div id="fp-step1-msg" class="mb-2 small"></div>
          <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn shadow-none"
              data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
              ← Quay lại
            </button>
            <button type="button" class="btn btn-dark shadow-none" id="fp-send-btn"
              onclick="fpSendPin()">
              GỬI MÃ PIN
            </button>
          </div>
        </div>

        <!-- ── BƯỚC 2: Nhập PIN ── -->
        <div id="fp-step-2" style="display:none;">
          <p class="text-muted small mb-3">
            Nhập mã PIN <strong>6 chữ số</strong> vừa được gửi đến email của bạn.
          </p>

          <!-- 6 ô nhập PIN -->
          <div class="d-flex justify-content-center gap-2 mb-3" id="fp-pin-boxes">
            <input type="text" class="form-control text-center fw-bold fs-4 shadow-none fp-pin-digit"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   style="width:48px;height:56px;">
            <input type="text" class="form-control text-center fw-bold fs-4 shadow-none fp-pin-digit"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   style="width:48px;height:56px;">
            <input type="text" class="form-control text-center fw-bold fs-4 shadow-none fp-pin-digit"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   style="width:48px;height:56px;">
            <input type="text" class="form-control text-center fw-bold fs-4 shadow-none fp-pin-digit"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   style="width:48px;height:56px;">
            <input type="text" class="form-control text-center fw-bold fs-4 shadow-none fp-pin-digit"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   style="width:48px;height:56px;">
            <input type="text" class="form-control text-center fw-bold fs-4 shadow-none fp-pin-digit"
                   maxlength="1" inputmode="numeric" pattern="[0-9]"
                   style="width:48px;height:56px;">
          </div>

          <!-- Đếm ngược 15 phút -->
          <p class="text-center small text-muted mb-1">
            Mã hết hạn sau: <span id="fp-countdown" class="fw-bold text-danger">15:00</span>
          </p>

          <div id="fp-step2-msg" class="mb-2 small text-center"></div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <button type="button" class="btn shadow-none text-muted small"
              onclick="fpGoStep(1)">
              ← Gửi lại mã
            </button>
            <button type="button" class="btn btn-dark shadow-none" id="fp-verify-btn"
              onclick="fpVerifyPin()">
              XÁC NHẬN PIN
            </button>
          </div>
        </div>

        <!-- ── BƯỚC 3: Đặt mật khẩu mới ── -->
        <div id="fp-step-3" style="display:none;">
          <p class="text-muted small mb-3">PIN hợp lệ! Hãy tạo mật khẩu mới.</p>
          <div class="mb-3">
            <label class="form-label">Mật Khẩu Mới</label>
            <input type="password" id="fp-new-pass" class="form-control shadow-none"
                   placeholder="Tối thiểu 8 ký tự" minlength="8">
          </div>
          <div class="mb-4">
            <label class="form-label">Xác Nhận Mật Khẩu</label>
            <input type="password" id="fp-conf-pass" class="form-control shadow-none"
                   placeholder="Nhập lại mật khẩu mới">
          </div>
          <div id="fp-step3-msg" class="mb-2 small"></div>
          <div class="text-end">
            <button type="button" class="btn btn-dark shadow-none w-100"
              onclick="fpResetPass()">
              ĐẶT LẠI MẬT KHẨU
            </button>
          </div>
        </div>

      </div><!-- /.modal-body -->
    </div>
  </div>
</div>

<!-- ==================== SCRIPT FORGOT PASSWORD ==================== -->
<script>
(function () {
  'use strict';

  /* ---------- Helpers ---------- */
  const $  = (id) => document.getElementById(id);
  let countdownTimer = null;

  function showMsg(elId, msg, type = 'danger') {
    const el = $(elId);
    el.innerHTML = `<div class="alert alert-${type} py-1 px-2 mb-0">${msg}</div>`;
  }
  function clearMsg(elId) { $(elId).innerHTML = ''; }

  function setLoading(btnId, loading) {
    const btn = $(btnId);
    if (loading) {
      btn.disabled = true;
      btn.dataset.orig = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang gửi...';
    } else {
      btn.disabled = false;
      btn.innerHTML = btn.dataset.orig || btn.innerHTML;
    }
  }

  /* ---------- Chuyển bước ---------- */
  window.fpGoStep = function (step) {
    [1, 2, 3].forEach(s => $(`fp-step-${s}`).style.display = 'none');
    $(`fp-step-${step}`).style.display = 'block';

    const titles = {
      1: 'Quên Mật Khẩu',
      2: 'Nhập Mã PIN',
      3: 'Đặt Mật Khẩu Mới'
    };
    $('forgotTitleText').textContent = titles[step];

    if (step === 1) {
      clearCountdown();
      clearMsg('fp-step1-msg');
    }
    if (step === 2) {
      resetPinBoxes();
      startCountdown(15 * 60);
      clearMsg('fp-step2-msg');
      // Focus ô đầu tiên
      setTimeout(() => document.querySelectorAll('.fp-pin-digit')[0].focus(), 300);
    }
    if (step === 3) {
      clearCountdown();
      clearMsg('fp-step3-msg');
    }
  };

  // Tự động in đậm link trang hiện tại
document.addEventListener('DOMContentLoaded', () => {
  const currentPage = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('fw-bold', 'active');
    }
  });
});

  /* ---------- Reset khi đóng/mở modal ---------- */
  document.getElementById('forgotModal').addEventListener('show.bs.modal', () => {
    fpGoStep(1);
    $('fp-email').value = '';
    $('fp-new-pass').value = '';
    $('fp-conf-pass').value = '';
  });

  /* ---------- Bước 1: Gửi PIN ---------- */
  window.fpSendPin = function () {
    const email = $('fp-email').value.trim();
    clearMsg('fp-step1-msg');

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showMsg('fp-step1-msg', 'Vui lòng nhập email hợp lệ!');
      return;
    }

    setLoading('fp-send-btn', true);

    const fd = new FormData();
    fd.append('send_pin', '');
    fd.append('email', email);

    fetch('ajax/forgot_handler.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        setLoading('fp-send-btn', false);
        if (res.status === 'success') {
          fpGoStep(2);
        } else {
          showMsg('fp-step1-msg', res.msg);
        }
      })
      .catch(() => {
        setLoading('fp-send-btn', false);
        showMsg('fp-step1-msg', 'Lỗi kết nối. Vui lòng thử lại!');
      });
  };

  /* ---------- 6 ô PIN: auto-focus & xử lý paste ---------- */
  function resetPinBoxes() {
    document.querySelectorAll('.fp-pin-digit').forEach(box => box.value = '');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const boxes = document.querySelectorAll('.fp-pin-digit');

    boxes.forEach((box, idx) => {
      box.addEventListener('input', () => {
        box.value = box.value.replace(/\D/g, '').slice(-1);
        if (box.value && idx < boxes.length - 1) {
          boxes[idx + 1].focus();
        }
      });

      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !box.value && idx > 0) {
          boxes[idx - 1].focus();
        }
      });

      // Hỗ trợ paste toàn bộ 6 số
      box.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData)
          .getData('text').replace(/\D/g, '').slice(0, 6);
        boxes.forEach((b, i) => { b.value = pasted[i] || ''; });
        const last = Math.min(pasted.length, boxes.length - 1);
        boxes[last].focus();
      });
    });
  });

  function getPinValue() {
    return Array.from(document.querySelectorAll('.fp-pin-digit'))
      .map(b => b.value).join('');
  }

  /* ---------- Đếm ngược ---------- */
  function startCountdown(seconds) {
    clearCountdown();
    let remaining = seconds;
    updateCountdownDisplay(remaining);

    countdownTimer = setInterval(() => {
      remaining--;
      updateCountdownDisplay(remaining);
      if (remaining <= 0) {
        clearCountdown();
        showMsg('fp-step2-msg', 'Mã PIN đã hết hạn! Vui lòng gửi lại.', 'warning');
        $('fp-verify-btn').disabled = true;
      }
    }, 1000);
  }

  function updateCountdownDisplay(s) {
    const m = String(Math.floor(s / 60)).padStart(2, '0');
    const sec = String(s % 60).padStart(2, '0');
    $('fp-countdown').textContent = `${m}:${sec}`;
    $('fp-countdown').style.color = s <= 60 ? '#dc3545' : '#6c757d';
  }

  function clearCountdown() {
    if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
  }

  /* ---------- Bước 2: Xác minh PIN ---------- */
  window.fpVerifyPin = function () {
    const pin = getPinValue();
    clearMsg('fp-step2-msg');

    if (pin.length !== 6) {
      showMsg('fp-step2-msg', 'Vui lòng nhập đủ 6 chữ số!');
      return;
    }

    $('fp-verify-btn').disabled = true;
    $('fp-verify-btn').innerHTML =
      '<span class="spinner-border spinner-border-sm me-1"></span>Đang kiểm tra...';

    const fd = new FormData();
    fd.append('verify_pin', '');
    fd.append('pin', pin);

    fetch('ajax/verify_pin.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        $('fp-verify-btn').disabled = false;
        $('fp-verify-btn').innerHTML = 'XÁC NHẬN PIN';
        if (res.status === 'success') {
          fpGoStep(3);
        } else {
          showMsg('fp-step2-msg', res.msg);
          // Rung các ô PIN khi sai
          document.querySelectorAll('.fp-pin-digit').forEach(b => {
            b.classList.add('border-danger');
            setTimeout(() => b.classList.remove('border-danger'), 1500);
          });
        }
      })
      .catch(() => {
        $('fp-verify-btn').disabled = false;
        $('fp-verify-btn').innerHTML = 'XÁC NHẬN PIN';
        showMsg('fp-step2-msg', 'Lỗi kết nối. Vui lòng thử lại!');
      });
  };

  /* ---------- Bước 3: Đặt lại mật khẩu ---------- */
  window.fpResetPass = function () {
    const newPass  = $('fp-new-pass').value;
    const confPass = $('fp-conf-pass').value;
    clearMsg('fp-step3-msg');

    if (newPass.length < 8) {
      showMsg('fp-step3-msg', 'Mật khẩu phải có ít nhất 8 ký tự!');
      return;
    }
    if (newPass !== confPass) {
      showMsg('fp-step3-msg', 'Mật khẩu xác nhận không khớp!');
      return;
    }

    const fd = new FormData();
    fd.append('reset_pass', '');
    fd.append('new_pass', newPass);
    fd.append('conf_pass', confPass);

    fetch('ajax/verify_pin.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success') {
          // Đóng modal, thông báo thành công
          bootstrap.Modal.getInstance(document.getElementById('forgotModal')).hide();
          // Dùng hàm alert() có sẵn trong project (hoặc đổi thành alert thường)
          if (typeof alert === 'function') {
            alert('success', res.msg);
          } else {
            alert(res.msg);
          }
        } else {
          showMsg('fp-step3-msg', res.msg);
        }
      })
      .catch(() => {
        showMsg('fp-step3-msg', 'Lỗi kết nối. Vui lòng thử lại!');
      });
  };

  

})();
</script>