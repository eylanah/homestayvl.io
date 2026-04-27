let current_search = '';

function get_bookings(search = '') {
  current_search = search;

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/new_bookings.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    document.getElementById('table-data').innerHTML = this.responseText;
  }

  xhr.send('get_bookings&search=' + encodeURIComponent(search));
}

// XÁC NHẬN 50%
function confirm_payment(id) {
  if (confirm("Xác nhận đã nhận 50%?")) {

    let data = new FormData();
    data.append('booking_id', id);
    data.append('confirm_payment', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);

    xhr.onload = function () {
      let res = this.responseText.trim();

      if (res == 1) {
        alert("Đã nhận 50%");

        // 🔥 CHUYỂN QUA HỒ SƠ
        window.location.href = 'booking_records.php';

      } else {
        alert("Lỗi: " + res);
      }
    }

    xhr.send(data);
  }
}

//  XÁC NHẬN 100%
function confirm_full_payment(id) {
  if (confirm("Xác nhận đã nhận đủ tiền?")) {

    let data = new FormData();
    data.append('booking_id', id);
    data.append('confirm_full_payment', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);

    xhr.onload = function () {
      let res = this.responseText.trim();

      if (res == 1) {
        alert("Đã thanh toán đủ!");

        window.location.href = 'booking_records.php';

      } else {
        alert("Lỗi: " + res);
      }
    }

    xhr.send(data);
  }
}

//  HỦY
function cancel_booking(id) {
  if (confirm("Bạn muốn hủy?")) {

    let data = new FormData();
    data.append('booking_id', id);
    data.append('cancel_booking', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert("Đã hủy");
        window.location.href = 'booking_records.php';
      } else {
        alert("Lỗi!");
      }
    }

    xhr.send(data);
  }
}

window.onload = function () {
  get_bookings();
}