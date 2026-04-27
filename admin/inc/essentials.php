<?php 

  //frontend purpose data
  
  // Auto-detect protocol and host (host already includes port if non-standard)
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
  $site_url = $protocol.'://'.$_SERVER['HTTP_HOST'].'/khachsan/';

  define('SITE_URL', $site_url);
  define('ABOUT_IMG_PATH',SITE_URL.'images/about/');
  define('CAROUSEL_IMG_PATH',SITE_URL.'images/carousel/');
  define('FACILITIES_IMG_PATH',SITE_URL.'images/facilities/');
  define('ROOMS_IMG_PATH',SITE_URL.'images/rooms/');
  define('USERS_IMG_PATH',SITE_URL.'images/users/');


  //backend upload process needs this data

  define('UPLOAD_IMAGE_PATH',$_SERVER['DOCUMENT_ROOT'].'/khachsan/images/');
  define('ABOUT_FOLDER','about/');
  define('CAROUSEL_FOLDER','carousel/');
  define('FACILITIES_FOLDER','facilities/');
  define('ROOMS_FOLDER','rooms/');
  define('USERS_FOLDER','users/');

  // sendgrid api key

  define('SENDGRID_API_KEY',"PASTE YOUR API KEY GENERATED FROM SENDGRID WEBSITE");
  define('SENDGRID_EMAIL',"PUT YOU EMAIL");
  define('SENDGRID_NAME',"ANY NAME");

  function adminLogin()
  {
    session_start();
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
      echo"<script>
        window.location.href='index.php';
      </script>";
      exit;
    }
  }

  function redirect($url){
    echo"<script>
      window.location.href='$url';
    </script>";
    exit;
  }

  function alert($type,$msg){    
    $bs_class = ($type == "success") ? "alert-success" : "alert-danger";

    echo <<<alert
      <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
        <strong class="me-3">$msg</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    alert;
  }

  function uploadImage($image,$folder)
  {
    $valid_mime = ['image/jpeg','image/png','image/webp','image/jpg'];
    $img_mime = $image['type'];

    // Nếu MIME type rỗng hoặc không xác định, kiểm tra extension
    if(empty($img_mime) || $img_mime == 'application/octet-stream'){
      $ext = strtolower(pathinfo($image['name'],PATHINFO_EXTENSION));
      if(in_array($ext, ['jpg','jpeg','png','webp'])){
        // Accept file based on extension
        $img_mime = 'image/' . ($ext == 'jpg' ? 'jpeg' : $ext);
      }
    }

    if(!in_array($img_mime,$valid_mime)){
      return 'inv_img'; //invalid image mime or format
    }
    else if(($image['size']/(1024*1024))>5){
      return 'inv_size'; //invalid size greater than 5mb
    }
    else{
      $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
      $rname = 'IMG_'.random_int(11111,99999).".$ext";

      $img_path = UPLOAD_IMAGE_PATH.$folder.$rname;
      if(move_uploaded_file($image['tmp_name'],$img_path)){
        return $rname;
      }
      else{
        return 'upd_failed';
      }
    }
  }

  function deleteImage($image, $folder)
  {
    if(unlink(UPLOAD_IMAGE_PATH.$folder.$image)){
      return true;
    }
    else{
      return false;
    }
  }

  function uploadSVGImage($image,$folder)
  {
    $valid_mime = ['image/svg+xml'];
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
      return 'inv_img'; //invalid image mime or format
    }
    else if(($image['size']/(1024*1024))>1){
      return 'inv_size'; //invalid size greater than 1mb
    }
    else{
      $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
      $rname = 'IMG_'.random_int(11111,99999).".$ext";

      $img_path = UPLOAD_IMAGE_PATH.$folder.$rname;
      if(move_uploaded_file($image['tmp_name'],$img_path)){
        return $rname;
      }
      else{
        return 'upd_failed';
      }
    }
  }

  function uploadUserImage($image)
  {
      $valid_mime = ['image/jpeg','image/png','image/webp'];

      if(!in_array($image['type'],$valid_mime)){
          return 'inv_img';
      }

      $ext = pathinfo($image['name'], PATHINFO_EXTENSION);

      $rname = 'USER_'.random_int(11111,99999).".".$ext;

      // ĐƯỜNG DẪN THẬT TRÊN SERVER
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/khachsan/images/users/";

      if(!is_dir($upload_path)){
          mkdir($upload_path,0777,true);
      }

      if(move_uploaded_file($image['tmp_name'],$upload_path.$rname)){
          return $rname;
      }else{
          return 'upd_failed';
      }
  }
?>