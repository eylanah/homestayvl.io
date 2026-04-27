<?php
// =====================================================
// inc/db.php
// Kết nối database - dùng chung cho forgot_handler.php
// và verify_pin.php
// =====================================================

$host   = 'localhost';
$dbname = 'khachsan';
$user   = 'root';
$pass   = '';          // XAMPP mặc định không có password

$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con) {
    die(json_encode([
        'status' => 'error',
        'msg'    => 'Không thể kết nối database: ' . mysqli_connect_error()
    ]));
}

mysqli_set_charset($con, 'utf8mb4');