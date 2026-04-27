# TODO - Thanh toán chuyển khoản ngân hàng (Techcombank QR)

## Plan
- [x] Step 1: Tạo file `bank_qr.php` - Trang hiển thị QR Techcombank
- [x] Step 2: Sửa `payment.php` - Bật lại option chuyển khoản ngân hàng, chuyển sang `bank_qr.php`
- [x] Step 3: Sửa `process_payment.php` - Thêm xử lý `payment_method == 'bank'`
- [x] Step 4: Test luồng hoạt động

## Chi tiết

### Step 1: `bank_qr.php`
- Hiển thị ảnh `images/qr_techcombank.jpg`
- Hiển thị số tiền cần chuyển khoản (50%)
- Hiển thị nội dung chuyển khoản (mã đơn hàng)
- Nút "Tiếp tục" → POST sang `process_payment.php` với `payment_method=bank`

### Step 2: `payment.php`
- Bỏ comment phần "Chuyển khoản ngân hàng"
- Khi chọn `bank`, form action chuyển sang `bank_qr.php` thay vì `process_payment.php`

### Step 3: `process_payment.php`
- Thêm khối `if ($payment_method == 'bank')`:
  - Tạo `booking_order` với `booking_status='Chờ Thanh Toán'`, `trans_status='Chờ chuyển khoản'`, `trans_amt=TXN_AMOUNT`
  - Tạo `booking_details`
  - Gửi email `payment_50_confirmed`
  - Chuyển về `bookings.php`

