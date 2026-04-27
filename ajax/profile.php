<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();

if(isset($_POST['info_form']))
{
    $frm_data = filteration($_POST);

    $u_exist = select(
        "SELECT * FROM `user_cred` WHERE `phonenum`=? AND `id`!=? LIMIT 1",
        [$frm_data['phonenum'], $_SESSION['uId']],
        "si"
    );

    if(mysqli_num_rows($u_exist)!=0){
        echo 'phone_already';
        exit;
    }

    $query = "UPDATE `user_cred`
              SET `name`=?,`address`=?,`phonenum`=?,`gender`=?,`dob`=?
              WHERE `id`=? LIMIT 1";

    $values = [
        $frm_data['name'],
        $frm_data['address'],
        $frm_data['phonenum'],
        $frm_data['gender'],
        $frm_data['dob'],
        $_SESSION['uId']
    ];

    if(update($query,$values,'sssssi')){
        $_SESSION['uName']=$frm_data['name'];
        echo 1;
    }else{
        echo 0;
    }
}


if(isset($_POST['profile_form']))
{

    if(!isset($_FILES['profile'])){
        echo 'no_file';
        exit;
    }

    $img = uploadUserImage($_FILES['profile']);

    if($img=='inv_img'){
        echo 'inv_img';
        exit;
    }

    if($img=='upd_failed'){
        echo 'upd_failed';
        exit;
    }

    // lấy ảnh cũ
    $res = select(
        "SELECT `profile` FROM `user_cred` WHERE `id`=? LIMIT 1",
        [$_SESSION['uId']],
        "i"
    );

    $u_fetch = mysqli_fetch_assoc($res);

    if($u_fetch['profile']!=''){
        deleteImage($u_fetch['profile'], USERS_FOLDER);
    }

    // update db
    $query="UPDATE `user_cred` SET `profile`=? WHERE `id`=? LIMIT 1";
    $values=[$img,$_SESSION['uId']];

    if(update($query,$values,'si')){
        $_SESSION['uPic']=$img;
        echo 1;
    }else{
        echo 0;
    }
}


if(isset($_POST['pass_form']))
{
    $frm_data = filteration($_POST);

    if($frm_data['new_pass']!=$frm_data['confirm_pass']){
        echo 'mismatch';
        exit;
    }

    if(strlen($frm_data['new_pass'])<8){
        echo 'short_pass';
        exit;
    }

    if(!isset($_SESSION['uId'])){
        echo 'no_session';
        exit;
    }

    $enc_pass=password_hash($frm_data['new_pass'],PASSWORD_BCRYPT);

    $query="UPDATE `user_cred` SET `password`=? WHERE `id`=? LIMIT 1";
    $values=[$enc_pass,$_SESSION['uId']];

    if(update($query,$values,'si')){
        echo 1;
    }else{
        echo 0;
    }
}
?>