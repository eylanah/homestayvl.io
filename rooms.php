<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - PHÒNG</title>
  <style>
    .pagination .page-link {
      color: #2c3e50;
      border: 1px solid #dee2e6;
    }
    .pagination .page-item.active .page-link {
      background-color: #2c3e50;
      border-color: #2c3e50;
      color: white;
    }
    .pagination .page-link:hover {
      background-color: #f8f9fa;
      color: #2c3e50;
    }
    .pagination .page-item.disabled .page-link {
      color: #6c757d;
      pointer-events: none;
      background-color: #fff;
      border-color: #dee2e6;
    }
    .btn-dark {
      background-color: #2c3e50;
      border-color: #2c3e50;
      font-weight: 500;
      padding: 10px 20px;
    }
    .btn-dark:hover {
      background-color: #1a252f;
      border-color: #1a252f;
    }
    .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: white;
    }
  </style>
</head>
<body class="bg-light">

  <?php 
    require('inc/header.php'); 

    $checkin_default="";
    $checkout_default="";
    $adult_default="";
    $children_default="";

    if(isset($_GET['check_availability']))
    {
      $frm_data = filteration($_GET);

      $checkin_default = $frm_data['checkin'];
      $checkout_default = $frm_data['checkout'];
      $adult_default = $frm_data['adult'];
      $children_default = $frm_data['children'];
    }
  ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">PHÒNG</h2>
    <div class="h-line bg-dark"></div>
  </div>

  <div class="container-fluid">
    <div class="row">

      <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
          <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2">Tìm Kiếm</h4>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">
              <!-- Kiểm tra tính khả dụng -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>Kiểm Tra Ngày</span>
                  <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <label class="form-label">Ngày Nhận Phòng</label>
                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkin_default ?>" id="checkin">
                <label class="form-label">Ngày Trả Phòng</label>
                <input type="date" class="form-control shadow-none" value="<?php echo $checkout_default ?>"  id="checkout">
              </div>

              <!-- Cơ sở -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>CƠ SỞ</span>
                  <button id="location_btn" onclick="location_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="location" value="all" id="loc_all" checked>
                  <label class="form-check-label" for="loc_all">Tất cả</label>
                </div>

                <?php 
                  $loc_q = mysqli_query($con, "SELECT DISTINCT location FROM rooms WHERE location != ''");

                  while($loc = mysqli_fetch_assoc($loc_q)){
                    $id = 'loc_' . strtolower(str_replace(' ', '_', $loc['location']));

                    echo "
                      <div class='form-check mb-2'>
                        <input class='form-check-input' type='radio' name='location' value='{$loc['location']}' id='$id'>
                        <label class='form-check-label' for='$id'>
                          {$loc['location']}
                        </label>
                      </div>
                    ";
                  }
                ?>

              </div>
              <!-- Tiện nghi -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>TIỆN NGHI</span>
                  <button id="facilities_btn" onclick="facilities_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <?php 
                  $facilities_q = selectAll('facilities');
                  while($row = mysqli_fetch_assoc($facilities_q))
                  {
                    echo<<<facilities
                      <div class="mb-2">
                        <input type="checkbox" name="facilities" value="$row[id]" class="form-check-input shadow-none me-1" id="$row[id]">
                        <label class="form-check-label" for="$row[id]">$row[name]</label>
                      </div>
                    facilities;
                  }
                ?>
              </div>

              <!-- Khách -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>KHÁCH</span>
                  <button id="guests_btn" onclick="guests_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <div class="d-flex">
                  <div class="me-3">
                    <label class="form-label">Người Lớn</label>
                    <input type="number" min="1" id="adults" value="<?php echo $adult_default ?>" class="form-control shadow-none">                 
                  </div>
                  <div>
                    <label class="form-label">Trẻ Em</label>
                    <input type="number" min="1" id="children" value="<?php echo $children_default ?>" class="form-control shadow-none">                 
                  </div>
                </div>
              </div>

              <!-- Nút Tìm Kiếm -->
              <div class="text-center">
                <button type="button" onclick="fetch_rooms()" class="btn btn-dark shadow-none w-100">
                  <i class="bi bi-search me-2"></i>TÌM PHÒNG
                </button>
                <button type="button" onclick="reset_all_filters()" class="btn btn-outline-secondary shadow-none w-100 mt-2">
                  <i class="bi bi-arrow-clockwise me-2"></i>ĐẶT LẠI TẤT CẢ
                </button>
              </div>
            </div>
          </div>
        </nav>
      </div>

      <div class="col-lg-9 col-md-12 px-4" id="rooms-data">
      </div>

    </div>
  </div>


  <script>

    let rooms_data = document.getElementById('rooms-data');

    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let chk_avail_btn = document.getElementById('chk_avail_btn');

    let adults = document.getElementById('adults');
    let children = document.getElementById('children');
    let guests_btn = document.getElementById('guests_btn');
    
    let facilities_btn = document.getElementById('facilities_btn');

    let current_page = 1;

    function fetch_rooms(page = 1)
    {
      current_page = page;

      //Tạo chuỗi JSON
      let chk_avail = JSON.stringify({
        checkin: checkin.value,
        checkout: checkout.value
      });

      let guests = JSON.stringify({
        adults: adults.value,
        children: children.value
      });

      let facility_list = {"facilities":[]};

      let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
      if(get_facilities.length>0)
      {
        get_facilities.forEach((facility)=>{
          facility_list.facilities.push(facility.value);
        });
        facilities_btn.classList.remove('d-none');
      }
      else{
        facilities_btn.classList.add('d-none');
      }

      facility_list = JSON.stringify(facility_list);

      // Lấy location
      let location = document.querySelector('[name="location"]:checked').value;
      let location_btn = document.getElementById('location_btn');
      if(location !== 'all') {
        location_btn.classList.remove('d-none');
      } else {
        location_btn.classList.add('d-none');
      }

      // Hiển thị nút reset cho dates
      if(checkin.value !== '' && checkout.value !== '') {
        chk_avail_btn.classList.remove('d-none');
      }

      // Hiển thị nút reset cho guests
      if(adults.value !== '' || children.value !== '') {
        guests_btn.classList.remove('d-none');
      }

      let xhr = new XMLHttpRequest();
      xhr.open("GET","ajax/rooms.php?fetch_rooms&chk_avail="+chk_avail+"&guests="+guests+"&facility_list="+facility_list+"&location="+location+"&page="+page,true);

      xhr.onprogress = function(){
        rooms_data.innerHTML = `<div class="spinner-border text-info mb-3 d-block mx-auto" id="loader" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>`;
      }

      xhr.onload = function(){
        if (this.responseText.includes('Không còn phòng trống để đặt!')) {
          rooms_data.innerHTML = `<h3 class='text-center text-danger'>Không còn phòng trống để đặt!</h3>`;
        } else {
          rooms_data.innerHTML = this.responseText;
        }
      }

      xhr.send();
    }

    function change_page(page) {
      fetch_rooms(page);
      window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function chk_avail_filter(){
      if(checkin.value!='' && checkout.value !=''){
        chk_avail_btn.classList.remove('d-none');
      }
    }

    function chk_avail_clear(){
      checkin.value='';
      checkout.value='';
      chk_avail_btn.classList.add('d-none');
      fetch_rooms();
    }

    function guests_filter(){
      if(adults.value>0 || children.value>0){
        guests_btn.classList.remove('d-none');
      }
    }

    function guests_clear(){
      adults.value='';
      children.value='';
      guests_btn.classList.add('d-none');
      fetch_rooms();
    }

    function facilities_clear(){
      let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
      get_facilities.forEach((facility)=>{
        facility.checked=false;
      });
      facilities_btn.classList.add('d-none');
      fetch_rooms();
    }

    function location_clear(){
      document.getElementById('loc_all').checked = true;
      document.getElementById('location_btn').classList.add('d-none');
      fetch_rooms();
    }

    function reset_all_filters(){
      // Reset dates
      checkin.value='';
      checkout.value='';
      chk_avail_btn.classList.add('d-none');
      
      // Reset guests
      adults.value='';
      children.value='';
      guests_btn.classList.add('d-none');
      
      // Reset facilities
      let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
      get_facilities.forEach((facility)=>{
        facility.checked=false;
      });
      facilities_btn.classList.add('d-none');
      
      // Reset location
      document.getElementById('loc_all').checked = true;
      document.getElementById('location_btn').classList.add('d-none');
      
      // Fetch rooms
      fetch_rooms();
    }

    window.onload = function(){
      fetch_rooms();
    }

  </script>

  <?php require('inc/footer.php'); ?>

</body>
</html>
