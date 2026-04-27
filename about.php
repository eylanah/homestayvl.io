<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link  rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - GIỚI THIỆU</title>
  <style>
    .box{
      border-top-color: var(--teal) !important;
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">GIỚI THIỆU</h2>
    <div class="h-line bg-dark"></div>
      <p class="text-center mt-3">
        Homestay Vĩnh Long là một điểm lưu trú thân thiện và gần gũi với thiên nhiên,<br>
        được thiết kế theo phong cách đơn giản nhưng đầy đủ tiện nghi.<br>
        Homestay cung cấp các phòng nghỉ thoải mái, không gian sinh hoạt chung,<br>
        khu vực ăn uống và khu vườn xanh mát,<br>
        mang đến cho du khách trải nghiệm thư giãn và cảm nhận cuộc sống miền Tây sông nước.
      </p>
  </div>

  <div class="container">
    <div class="row justify-content-between align-items-center">
      <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
        <h3 class="mb-3">Đặc trưng</h3>
        <p>
            Điểm đặc trưng của homestay là không gian yên tĩnh, thoáng mát,<br>
            mang đậm nét văn hóa miền Tây Nam Bộ.<br>
            Du khách có thể thưởng thức các món ăn đặc sản địa phương,<br>
            tham quan vườn trái cây, đi thuyền trên sông và trải nghiệm cuộc sống<br>
            bình dị của người dân địa phương.
        </p>
      </div>
      <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1">
        <img src="images\homestay3.jpg" class="w-100">
      </div>
    </div>
  </div>
  <h3 class="my-5 fw-bold h-font text-center">ĐỘI NGŨ</h3>
  <div class="container mt-5">
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
          <img src="images/about/hotel.svg" width="70px">
          <h4 class="mt-3">50+ PHÒNG</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
          <img src="images/about/customers.svg" width="70px">
          <h4 class="mt-3">100+ KHÁCH HÀNG</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
          <img src="images/about/rating.svg" width="70px">
          <h4 class="mt-3">150+ ĐÁNH GIÁ</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
          <img src="images/about/staff.svg" width="70px">
          <h4 class="mt-3">200+ NHÂN VIÊN</h4>
        </div>
      </div>
    </div>
  </div>



  <div class="container px-4">
    <div class="swiper mySwiper">
      <div class="swiper-wrapper mb-5">
        <?php 
          $about_r = selectAll('team_details');
          $path=ABOUT_IMG_PATH;
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


  <?php require('inc/footer.php'); ?>

  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <script>
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 40,
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 3,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });
  </script>


</body>
</html>