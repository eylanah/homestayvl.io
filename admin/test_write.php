<?php
require('inc/essentials.php');

$test_content = "Test file created at " . date('Y-m-d H:i:s');
$test_path = UPLOAD_IMAGE_PATH . ROOMS_FOLDER . 'test_write.txt';

echo "Trying to write to: $test_path<br>";
echo "Directory: " . UPLOAD_IMAGE_PATH . ROOMS_FOLDER . "<br>";
echo "Directory exists: " . (is_dir(UPLOAD_IMAGE_PATH . ROOMS_FOLDER) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable(UPLOAD_IMAGE_PATH . ROOMS_FOLDER) ? 'YES' : 'NO') . "<br><br>";

$result = file_put_contents($test_path, $test_content);

if($result !== false) {
    echo "✓ SUCCESS! Wrote $result bytes<br>";
    echo "File exists: " . (file_exists($test_path) ? 'YES' : 'NO') . "<br>";
    if(file_exists($test_path)) {
        echo "Content: " . file_get_contents($test_path);
    }
} else {
    echo "✗ FAILED to write file<br>";
    echo "Error: " . error_get_last()['message'];
}
?>
