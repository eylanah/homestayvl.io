<?php
// =====================================================
// CẤU HÌNH GMAIL SMTP - PHPMailer
// Đặt file này ở: inc/mail_config.php
//
// HƯỚNG DẪN LẤY APP PASSWORD GMAIL:
// 1. Vào Google Account → Security → 2-Step Verification (BẬT)
// 2. Vào Security → App passwords
// 3. Chọn "Mail" + "Windows Computer" → Generate
// 4. Copy 16 ký tự vào MAIL_PASSWORD bên dưới
// =====================================================

define('MAIL_HOST',      'smtp.gmail.com');
define('MAIL_PORT',      587);
define('MAIL_USERNAME',  'ntdata2105@gmail.com');   // ← Email Gmail của bạn
define('MAIL_PASSWORD',  'zfxo oftg mawo jilx');    // ← App Password 16 ký tự
define('MAIL_FROM_NAME', 'Homestay Vĩnh Long');
define('PIN_EXPIRE_MINUTES', 15);                   // PIN hết hạn sau 15 phút
