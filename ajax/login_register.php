<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
require("../inc/sendgrid/sendgrid-php.php");

date_default_timezone_set('Asia/Ho_Chi_Minh');


function send_mail($uemail, $token, $type)
{

  if ($type == "email_confirmation") {
    $page = 'email_confirm.php';
    $subject = "Account Verification Link";
    $content = "confirm your email";
  } 
  else {
    $page = 'index.php';
    $subject = "Account Reset Link";
    $content = "reset your account";
  }

  $email = new \SendGrid\Mail\Mail();
  $email->setFrom(SENDGRID_EMAIL, SENDGRID_NAME);
  $email->setSubject($subject);
  $email->addTo($uemail);

  $email->addContent(
    "text/html",
    "Click the link to $content:<br>
    <a href='" . SITE_URL . "$page?$type&email=$uemail&token=$token'>CLICK ME</a>"
  );

  $sendgrid = new \SendGrid(SENDGRID_API_KEY);

  try {
    $sendgrid->send($email);
    return 1;
  } 
  catch (Exception $e) {
    return 0;
  }
}



# REGISTER
if (isset($_POST['register'])) {

  $data = filteration($_POST);

  // Validation tên
  if (strlen($data['name']) < 3 || strlen($data['name']) > 50) {
    echo 'name_invalid';
    exit;
  }

  // Validation số điện thoại
  if (!preg_match('/^[0-9]{10}$/', $data['phonenum'])) {
    echo 'phone_invalid';
    exit;
  }

  // Validation mật khẩu
  if (strlen($data['pass']) < 8) {
    echo 'pass_short';
    exit;
  }

  if ($data['pass'] != $data['cpass']) {
    echo 'pass_mismatch';
    exit;
  }

  # kiểm tra email hoặc phone đã tồn tại
  $u_exist = select(
    "SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1",
    [$data['email'], $data['phonenum']],
    "ss"
  );

  if (mysqli_num_rows($u_exist) != 0) {

    $u_exist_fetch = mysqli_fetch_assoc($u_exist);

    if ($u_exist_fetch['email'] == $data['email']) {
      echo 'email_already';
    }
    else {
      echo 'phone_already';
    }

    exit;
  }

  # mã hoá mật khẩu
  $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

  # ảnh mặc định
  $img = "user.png";

  $query = "INSERT INTO `user_cred`
  (`name`,`email`,`address`,`phonenum`,`dob`,`gender`,`profile`,`password`,`is_verified`,`status`)
  VALUES (?,?,?,?,?,?,?,?,?,?)";

  $values = [
    $data['name'],
    $data['email'],
    $data['address'],
    $data['phonenum'],
    $data['dob'],
    $data['gender'],
    $img,
    $enc_pass,
    1,
    1
  ];

  if (insert($query,$values,'ssssssssss')) {
    echo 1;
  }
  else {
    echo 'ins_failed';
  }

}




# LOGIN
if (isset($_POST['login'])) {

  $data = filteration($_POST);

  $u_exist = select(
    "SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1",
    [$data['email_mob'], $data['email_mob']],
    "ss"
  );

  if (mysqli_num_rows($u_exist) == 0) {

    echo 'inv_email_mob';

  } 
  else {

    $u_fetch = mysqli_fetch_assoc($u_exist);

    if ($u_fetch['is_verified'] == 0) {
      echo 'not_verified';
    }

    else if ($u_fetch['status'] == 0) {
      echo 'inactive';
    }

    else {

      if (!password_verify($data['pass'], $u_fetch['password'])) {
        echo 'invalid_pass';
      }

      else {

        session_start();

        $_SESSION['login'] = true;
        $_SESSION['uId'] = $u_fetch['id'];
        $_SESSION['uName'] = $u_fetch['name'];
        $_SESSION['uPic'] = $u_fetch['profile'];
        $_SESSION['uPhone'] = $u_fetch['phonenum'];

        echo 1;

      }

    }

  }

}




# FORGOT PASSWORD
if (isset($_POST['forgot_pass'])) {

  $data = filteration($_POST);

  // Kiểm tra email có tồn tại không
  $u_exist = select(
    "SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1",
    [$data['email']],
    "s"
  );

  if (mysqli_num_rows($u_exist) == 0) {
    echo 'email_not_found';
    exit;
  }

  $u_fetch = mysqli_fetch_assoc($u_exist);

  // Tạo token reset password
  $token = bin2hex(random_bytes(16));
  $t_expire = date("Y-m-d", strtotime("+1 day"));

  // Cập nhật token vào database
  $query = "UPDATE `user_cred` SET `token`=?, `t_expire`=? WHERE `email`=?";
  $values = [$token, $t_expire, $data['email']];

  if (update($query, $values, 'sss')) {
    // Gửi email (tạm thời trả về success, bạn có thể tích hợp SendGrid sau)
    // $send_result = send_mail($data['email'], $token, 'password_reset');
    echo 1; // Success
  }
  else {
    echo 'upd_failed';
  }
}

# RESET PASSWORD
if (isset($_POST['reset_pass'])) {

  $data = filteration($_POST);

  // Validation mật khẩu
  if (strlen($data['pass']) < 8) {
    echo 'pass_short';
    exit;
  }

  if ($data['pass'] != $data['cpass']) {
    echo 'pass_mismatch';
    exit;
  }

  // Kiểm tra token
  $u_exist = select(
    "SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`>=? LIMIT 1",
    [$data['email'], $data['token'], date("Y-m-d")],
    "sss"
  );

  if (mysqli_num_rows($u_exist) == 0) {
    echo 'invalid_token';
    exit;
  }

  // Mã hoá mật khẩu mới
  $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

  // Cập nhật mật khẩu và xoá token
  $query = "UPDATE `user_cred` SET `password`=?, `token`=NULL, `t_expire`=NULL WHERE `email`=?";
  $values = [$enc_pass, $data['email']];

  if (update($query, $values, 'ss')) {
    echo 1;
  }
  else {
    echo 'upd_failed';
  }
}
