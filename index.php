<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link  rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <link rel="stylesheet" href="css/anh.css">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - TRANG CHỦ</title>
  <style>
    .availability-form{
      margin-top: -50px;
      z-index: 2;
      position: relative;
    }

    @media screen and (max-width: 575px) {
      .availability-form{
        margin-top: 25px;
        padding: 0 35px;
      } 
    }

  </style>
  
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="hero-banner">
    <img src="images/image.png" alt="Ảnh nền khách sạn" class="hero-img">

    <div class="hero-content">
      <h2 class="fw-bold h-font text-center">GIỚI THIỆU</h2>
      <div class="h-line bg-light mx-auto"></div>
      <p class="text-center mt-3 text-white">
        Chào mừng đến với Homestay Vĩnh Long – nơi mang đến sự kết hợp hoàn hảo giữa sang trọng, tiện nghi và sự thân thiện.<br>
        Là nơi nghỉ ngơi lý tưởng cho các gia đình đi du lịch và các cặp đôi thay đổi bầu không khí tình yêu.
      </p>
    </div>
  </div>

  <div class="container">
    <div class="row justify-content-between align-items-center">
      <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
        <h3 class="mb-3">HomeStay Vĩnh Long</h3>
        <p>
          Homestay Vĩnh Long là một điểm lưu trú thân thiện và gần gũi với thiên nhiên, được thiết kế theo phong cách đơn giản nhưng đầy đủ tiện nghi. Homestay cung cấp các phòng nghỉ thoải mái, không gian sinh hoạt chung, khu vực ăn uống và khu vườn xanh mát, mang đến cho du khách trải nghiệm thư giãn và cảm nhận cuộc sống miền Tây sông nước.
        </p>
      </div>

    <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1 mt-3">
      <img src="images/anhvinh.png" class="w-100">
    </div>

  </div>
</div>


<div class="container px-4">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper mb-5">
      <?php 
        $about_r = selectAll('team_details');
        $path = ABOUT_IMG_PATH;
        while($row = mysqli_fetch_assoc($about_r)){
          echo<<<data
            <div class="swiper-slide bg-white text-center overflow-hidden rounded">
              <img src="$path$row[picture]" class="w-100">
              <h5 class="mt-2">$row[name]</h5>
            </div>
          data;
        }
      ?>
    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>

   <!-- Check Availability Form -->
    <div class="row mt-5">
      <div class="col-lg-12 d-flex justify-content-center">
        <div class="availability-form bg-white shadow p-4 rounded" style="width: 100%; max-width: 800px;">
          <h5 class="mb-4 text-center">Tìm Phòng</h5>
          <form action="rooms.php">
            <div class="row align-items-end">

              <div class="col-lg-5 mb-3">
                <label class="form-label" style="font-weight: 500;">Giá</label>
                <select class="form-select shadow-none" name="price_range">
                  <option value="">Tất cả</option>
                  <option value="1">Dưới 1 triệu</option>
                  <option value="2">1 - 5 triệu</option>
                  <option value="3">Trên 5 triệu</option>
                </select>
              </div>

              <div class="col-lg-3 mb-3">
                <label class="form-label" style="font-weight: 500;">Ngày Nhận Phòng</label>
                <input type="date" class="form-control shadow-none" name="checkin" required>
              </div>
              <div class="col-lg-3 mb-3">
                <label class="form-label" style="font-weight: 500;">Ngày Trả Phòng</label>
                <input type="date" class="form-control shadow-none" name="checkout" required>
              </div>
              <div class="col-lg-2 mb-3">
                <label class="form-label" style="font-weight: 500;">Người Lớn</label>
                <select class="form-select shadow-none" name="adult">
                  <?php 
                    $guests_q = mysqli_query($con,"SELECT MAX(adult) AS `max_adult`, MAX(children) AS `max_children` 
                      FROM `rooms` WHERE `status`='1' AND `removed`='0'");  
                    $guests_res = mysqli_fetch_assoc($guests_q);
                    
                    for($i=1; $i<=$guests_res['max_adult']; $i++){
                      echo"<option value='$i'>$i</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="col-lg-2 mb-3">
                <label class="form-label" style="font-weight: 500;">Trẻ Em</label>
                <select class="form-select shadow-none" name="children">
                  <?php 
                    for($i=1; $i<=$guests_res['max_children']; $i++){
                      echo"<option value='$i'>$i</option>";
                    }
                  ?>
                </select>
              </div>
              <input type="hidden" name="check_availability">
              <div class="col-lg-2 mb-lg-3 mt-2">
                <button type="submit" class="btn text-white shadow-none custom-bg w-100">Tìm Phòng</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>


  </div>


  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">PHÒNG</h2>

  <div class="container">
    <div class="row">

      <?php 
            
        $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 3",[1,0],'ii');

        while($room_data = mysqli_fetch_assoc($room_res))
        {
          // get features of room

          $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f 
            INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
            WHERE rfea.room_id = '$room_data[id]'");

          $features_data = "";
          while($fea_row = mysqli_fetch_assoc($fea_q)){
            $features_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
              $fea_row[name]
            </span>";
          }

          // get facilities of room

          $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f 
            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = '$room_data[id]'");

          $facilities_data = "";
          while($fac_row = mysqli_fetch_assoc($fac_q)){
            $facilities_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
              $fac_row[name]
            </span>";
          }

          // get thumbnail of image

          $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
          $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` 
            WHERE `room_id`='$room_data[id]' 
            AND `thumb`='1'");

          if(mysqli_num_rows($thumb_q)>0){
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
          }

          $book_btn = "";

          if(!$settings_r['shutdown']){
            $login=0;
            if(isset($_SESSION['login']) && $_SESSION['login']==true){
              $login=1;
            }

            $book_btn = "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm text-white custom-bg shadow-none'>Đặt Ngay</button>";
          }

          $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review`
            WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

          $rating_res = mysqli_query($con,$rating_q);
          $rating_fetch = mysqli_fetch_assoc($rating_res);

          $rating_data = "";

          if($rating_fetch['avg_rating']!=NULL)
          {
            $rating_data = "<div class='rating mb-4'>
              <h6 class='mb-1'>Rating</h6>
              <span class='badge rounded-pill bg-light'>
            ";

            for($i=0; $i<$rating_fetch['avg_rating']; $i++){
              $rating_data .="<i class='bi bi-star-fill text-warning'></i> ";
            }

            $rating_data .= "</span>
              </div>
            ";
          }

          // print room card
       $price = number_format($room_data['price'], 0, ',', '.');

echo <<<HTML
<div class="col-lg-4 col-md-6 my-3">
  <div class="card border-0 shadow room-card mx-auto">

    <!-- Ảnh phòng -->
    <img src="$room_thumb" class="card-img-top room-img" alt="Room image">

    <!-- Nội dung -->
    <div class="card-body d-flex flex-column">

      <h5 class="mb-1">$room_data[name]</h5>
      
      <h6 class="text-muted mb-3">$price vnđ / đêm</h6>

      <div class="mb-3">
        <h6 class="mb-1">Cơ sở</h6>
        <div class="text-wrap">$features_data</div>
      </div>

      <div class="mb-3">  
        <h6 class="mb-1">Tiện nghi & Trang thiết bị</h6>
        <div class="text-wrap">$facilities_data</div>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">Khách Hàng</h6>
        <span class="badge rounded-pill bg-light text-dark">$room_data[adult] Người Lớn</span>
        <span class="badge rounded-pill bg-light text-dark">$room_data[children] Trẻ Em</span>
      </div>

      $rating_data

      <!-- Nút luôn nằm đáy -->
      <div class="mt-auto d-flex justify-content-evenly">
        $book_btn
        <a href="room_details.php?id=$room_data[id]" class="btn btn-sm btn-outline-dark shadow-none">
          Chi tiết
        </a>
      </div>

    </div>
  </div>
</div>
HTML;


        }

      ?>

      <div class="col-lg-12 text-center mt-5">
        <a href="rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Xem Thêm >>></a>
      </div>
    </div>
  </div>

  <!-- Our Facilities -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">CÁC TIỆN NGHI</h2>

  <div class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
      <?php 
        $res = mysqli_query($con,"SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 5");
        $path = FACILITIES_IMG_PATH;

        while($row = mysqli_fetch_assoc($res)){
          echo<<<data
            <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
              <img src="$path$row[icon]" width="60px">
              <h5 class="mt-3">$row[name]</h5>
            </div>
          data;
        }
      ?>
  
      <div class="col-lg-12 text-center mt-5">
        <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Xem thêm >>></a>
      </div>
    </div>
  </div>

<div class="container my-5">
  <div class="row">

    <!-- BÊN TRÁI: BẢN ĐỒ -->
    <div class="col-lg-6 mb-4 mb-lg-0">
      <iframe 
        class="border rounded w-100"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3926.479841305686!2d105.9591484!3d10.2498396!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310a82ce95555555%3A0x451cc8d95d6039f8!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBTxrAgcGjhuqFtIEvhu7kgdGh14bqtdCBWxKluaCBMb25n!5e0!3m2!1svi!2s!4v1706940000000"
        height="450"
        style="border:0;"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>

    <!-- BÊN PHẢI: ĐÁNH GIÁ -->
    <div class="col-lg-6">
      <h2 class="mb-4 text-center fw-bold h-font">ĐÁNH GIÁ TỪ KHÁCH HÀNG</h2>

      <div class="swiper swiper-testimonials">
        <div class="swiper-wrapper mb-5">
          <?php
            $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, r.name AS rname
                        FROM `rating_review` rr
                        INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                        INNER JOIN `rooms` r ON rr.room_id = r.id
                        ORDER BY `sr_no` DESC LIMIT 6";

            $review_res = mysqli_query($con,$review_q);
            $img_path = USERS_IMG_PATH;

            if(mysqli_num_rows($review_res)==0){
              echo '<div class="text-center">Chưa có đánh giá nào!</div>';
            }
            else{
              while($row = mysqli_fetch_assoc($review_res))
              {
                $stars = "<i class='bi bi-star-fill text-warning'></i>";
                for($i=1; $i<$row['rating']; $i++){
                  $stars .= " <i class='bi bi-star-fill text-warning'></i>";
                }

                echo<<<slides
                  <div class="swiper-slide bg-white p-4 rounded shadow-sm">
                    <div class="profile d-flex align-items-center mb-3">
                      <img src="$img_path$row[profile]" class="rounded-circle" width="35">
                      <h6 class="m-0 ms-2">$row[uname]</h6>
                    </div>
                    <p class="mb-2">$row[review]</p>
                    <div class="rating">$stars</div>
                  </div>
                slides;
              }
            }
          ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>
</div>
    
  <!-- Password reset modal and code -->

  


  <?php require('inc/footer.php'); ?>

  
  <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>

  <script>
      new Swiper(".swiper-testimonials", {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: false,            
      grabCursor: true,

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },

      breakpoints: {
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });
    // recover account
    

  </script>

</body>
</html>