<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

$image_url = 'https://cf.bstatic.com/xdata/images/hotel/square600/773687784.webp?k=cef2193e292d328795bbc32dce2582d8edebcbd3aeca4be239fa0c72034680ce&o=';

// Tìm room_id của "Phòng Bình Dân"
$room_query = "SELECT id, name FROM rooms WHERE name LIKE '%Bình Dân%' LIMIT 1";
$room_result = mysqli_query($con, $room_query);

if(mysqli_num_rows($room_result) == 0) {
    die("Không tìm thấy phòng 'Bình Dân'");
}

$room = mysqli_fetch_assoc($room_result);
$room_id = $room['id'];
$room_name = $room['name'];

echo "Tìm thấy phòng: $room_name (ID: $room_id)\n\n";

// Download image
echo "Đang tải ảnh từ URL...\n";

$context = stream_context_create([
  'http' => [
    'method' => 'GET',
    'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'timeout' => 10
  ],
  'https' => [
    'method' => 'GET',
    'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'timeout' => 10
  ],
  'ssl' => [
    'verify_peer' => false,
    'verify_peer_name' => false
  ]
]);

$image_content = @file_get_contents($image_url, false, $context);

if($image_content === false || empty($image_content)){
  die("Lỗi: Không thể tải ảnh từ URL!");
}

echo "✓ Đã tải ảnh thành công (" . strlen($image_content) . " bytes)\n\n";

// Check file size
if(strlen($image_content) > 2097152){
  die("Lỗi: Ảnh quá lớn (>2MB)");
}

// Detect image type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->buffer($image_content);

$allowed_types = [
  'image/jpeg' => 'jpg',
  'image/jpg' => 'jpg',
  'image/png' => 'png',
  'image/webp' => 'webp'
];

if(!isset($allowed_types[$mime_type])){
  die("Lỗi: Định dạng ảnh không hợp lệ ($mime_type)");
}

$ext = $allowed_types[$mime_type];
echo "✓ Định dạng ảnh: $ext\n\n";

// Generate filename
$img_name = 'IMG_'.random_int(11111,99999).'.'.$ext;
$img_path = __DIR__ . '/images/rooms/' . $img_name;

echo "Đang lưu ảnh vào: $img_path\n";

// Save image
$result = file_put_contents($img_path, $image_content);

if($result === false || $result == 0){
  die("Lỗi: Không thể lưu file!");
}

if(!file_exists($img_path)){
  die("Lỗi: File không tồn tại sau khi lưu!");
}

echo "✓ Đã lưu file thành công ($result bytes)\n\n";

// Insert to database
$q = "INSERT INTO `room_images`(`room_id`, `image`, `thumb`) VALUES (?,?,?)";
$stmt = mysqli_prepare($con, $q);
$thumb = 0; // Không set làm thumbnail mặc định
mysqli_stmt_bind_param($stmt, 'isi', $room_id, $img_name, $thumb);

if(mysqli_stmt_execute($stmt)){
  echo "✓ Đã thêm ảnh vào database!\n";
  echo "\n=== HOÀN THÀNH ===\n";
  echo "Phòng: $room_name\n";
  echo "File: $img_name\n";
  echo "URL xem ảnh: http://localhost/images/rooms/$img_name\n";
} else {
  echo "Lỗi: Không thể insert vào database - " . mysqli_error($con) . "\n";
  // Xóa file nếu insert thất bại
  unlink($img_path);
}

mysqli_stmt_close($stmt);
?>
