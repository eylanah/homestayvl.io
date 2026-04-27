<?php 

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();

if(isset($_GET['fetch_rooms'])) {
    // Giải mã dữ liệu kiểm tra tình trạng có phòng
    $chk_avail = json_decode($_GET['chk_avail'], true);
    
    // Xác thực các ngày checkin và checkout
    if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
        $today_date = new DateTime(date("Y-m-d"));
        $checkin_date = new DateTime($chk_avail['checkin']);
        $checkout_date = new DateTime($chk_avail['checkout']);

        if ($checkin_date == $checkout_date || $checkout_date < $checkin_date || $checkin_date < $today_date) {
            echo "<h3 class='text-center text-danger'>Ngày đã nhập không hợp lệ!</h3>";
            exit;
        }
    }

    // Giải mã thông tin khách
    $guests = json_decode($_GET['guests'], true);
    $adults = !empty($guests['adults']) ? $guests['adults'] : 0;
    $children = !empty($guests['children']) ? $guests['children'] : 0;

    // Giải mã danh sách tiện nghi
    $facility_list = json_decode($_GET['facility_list'], true);

    // Lấy location filter
    $location = isset($_GET['location']) ? $_GET['location'] : 'all';

    // Pagination
    $limit = 4; // Số phòng mỗi trang
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Biến lưu trữ số lượng phòng và thông tin phòng
    $count_rooms = 0;
    $output = "";
    $total_rooms = 0;

    // Lấy thông tin cài đặt từ bảng settings
    $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
    $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));

    // Lưu trữ tất cả phòng phù hợp để đếm tổng số
    $all_rooms = [];

    // Truy vấn để lấy các phòng theo bộ lọc của khách hàng
    if($location == 'all') {
        $room_res = select("SELECT * FROM `rooms` WHERE `adult` >= ? AND `children` >= ? AND `status` = ? AND `removed` = ?", [$adults, $children, 1, 0], 'iiii');
    } else {
        $room_res = select("SELECT * FROM `rooms` WHERE `adult` >= ? AND `children` >= ? AND `status` = ? AND `removed` = ? AND `location` = ?", [$adults, $children, 1, 0, $location], 'iiiis');
    }

    while ($room_data = mysqli_fetch_assoc($room_res)) {
        // Kiểm tra tình trạng phòng có sẵn
        if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
            $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
            WHERE booking_status IN ('Đã Đặt', 'Đã Xác Nhận Đặt Phòng')
            AND room_id = ?
            AND NOT (check_out < ? OR check_in > ?)";
            $values = [$room_data['id'], $chk_avail['checkin'], $chk_avail['checkout']];
            $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'iss'));

            if (($room_data['quantity'] - $tb_fetch['total_bookings']) <= 0) {
                continue; // Nếu không còn phòng trống
            }
        }

        // Lấy danh sách tiện nghi của phòng
        $fac_count = 0;
        $fac_q = mysqli_query($con, "SELECT f.name, f.id FROM `facilities` f 
            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = '$room_data[id]'");

        $facilities_data = "";
        while ($fac_row = mysqli_fetch_assoc($fac_q)) {
            if (in_array($fac_row['id'], $facility_list['facilities'])) {
                $fac_count++;
            }

            $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                $fac_row[name]
            </span>";
        }

        if (count($facility_list['facilities']) != $fac_count) {
            continue; // Không đủ tiện nghi yêu cầu
        }

        // Lưu phòng vào mảng tạm
        $all_rooms[] = [
            'data' => $room_data,
            'facilities' => $facilities_data
        ];
    }

    // Tính tổng số phòng và số trang
    $total_rooms = count($all_rooms);
    $total_pages = ceil($total_rooms / $limit);

    // Lấy phòng theo trang hiện tại
    $rooms_on_page = array_slice($all_rooms, $offset, $limit);

    foreach ($rooms_on_page as $room_item) {
        $room_data = $room_item['data'];
        $facilities_data = $room_item['facilities'];

        // Lấy danh sách đặc điểm của phòng
        $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
            INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
            WHERE rfea.room_id = '$room_data[id]'");

        $features_data = "";
        while ($fea_row = mysqli_fetch_assoc($fea_q)) {
            $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                $fea_row[name]
            </span>";
        }

        // Lấy hình ảnh thu nhỏ
        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` 
            WHERE `room_id` = '$room_data[id]' 
            AND `thumb` = '1'");

        if (mysqli_num_rows($thumb_q) > 0) {
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
        }

        $book_btn = "";

        if (!$settings_r['shutdown']) {
            $login = isset($_SESSION['login']) && $_SESSION['login'] ? 1 : 0;
            $book_btn = "<button onclick='checkLoginToBook($login, $room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Đặt Ngay</button>";
        }

        // In thẻ phòng
        $price = number_format($room_data['price'], 0, ',', '.'); // Giả sử giá là trong cơ sở dữ liệu
        $output .= "
            <div class='card mb-4 border-0 shadow'>
                <div class='row g-0 p-3 align-items-center'>
                    <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
                  
                        <img src='$room_thumb' style='width:100%; height:230px; object-fit:cover;' class='rounded'>
                    </div>
                    <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                        <h5 class='mb-1'>$room_data[name]</h5>
                        <div class='features mb-3'>
                            <h6 class='mb-1'>Cơ Sở</h6>
                            $features_data
                        </div>
                        <div class='facilities mb-3'>
                            <h6 class='mb-1'>Tiện Nghi</h6>
                            $facilities_data
                        </div>
                        <div class='guests'>
                            <h6 class='mb-1'>Khách Hàng</h6>
                            <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                $room_data[adult] Người Lớn
                            </span>
                            <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                $room_data[children] Trẻ Em
                            </span>
                        </div>
                    </div>
                    
                    <div class='col-md-2 mt-lg-0 mt-md-0 mt-4 text-center'>
                        <h6 class='mb-4'>$price vnđ mỗi đêm</h6>
                        $book_btn
                        <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>Chi Tiết</a>
                    </div>
                </div>
            </div>
        ";

        $count_rooms++;
    }

    if ($count_rooms > 0) {
        echo $output;
        
        // Hiển thị pagination
        if ($total_pages > 1) {
            echo '<div class="col-12 mt-4">';
            echo '<nav aria-label="Page navigation">';
            echo '<ul class="pagination justify-content-center">';
            
            // Nút Previous
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="change_page(' . ($page - 1) . ')">Trước</a></li>';
            } else {
                echo '<li class="page-item disabled"><span class="page-link">Trước</span></li>';
            }
            
            // Các số trang
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo '<li class="page-item ' . $active . '"><a class="page-link" href="javascript:void(0)" onclick="change_page(' . $i . ')">' . $i . '</a></li>';
            }
            
            // Nút Next
            if ($page < $total_pages) {
                echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="change_page(' . ($page + 1) . ')">Sau</a></li>';
            } else {
                echo '<li class="page-item disabled"><span class="page-link">Sau</span></li>';
            }
            
            echo '</ul>';
            echo '</nav>';
            echo '<p class="text-center text-muted">Trang ' . $page . ' / ' . $total_pages . ' (Tổng ' . $total_rooms . ' phòng)</p>';
            echo '</div>';
        }
    } else {
        echo "<h3 class='text-center text-danger'>Không có phòng nào !!!</h3>";
    }
}

// Xử lý đặt phòng
if (isset($_POST['booking_id']) && isset($_POST['room_id']) && isset($_POST['quantity'])) {
  $booking_id = $_POST['booking_id'];
  $room_id = $_POST['room_id'];
  $requested_quantity = $_POST['quantity']; // Số lượng phòng yêu cầu

  // Truy vấn để lấy số lượng phòng hiện tại
  $room_query = "SELECT quantity FROM rooms WHERE id=?";
  $room_result = select($room_query, [$room_id], 'i');
  $room_data = mysqli_fetch_assoc($room_result);

  // Kiểm tra số lượng phòng còn lại
  if ($room_data && $room_data['quantity'] >= $requested_quantity) {
      // Giảm số lượng phòng còn lại
      $new_quantity = $room_data['quantity'] - $requested_quantity;
      $update_query = "UPDATE rooms SET quantity=? WHERE id=?";
      execute($update_query, [$new_quantity, $room_id], 'ii');

      echo "Đặt phòng thành công!";
  } else {
      echo "<h3 class='text-center text-danger'>Không còn đủ phòng trống để đặt!</h3>";
  }
}

?>
