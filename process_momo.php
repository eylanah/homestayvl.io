<?php
header('Content-type: text/html; charset=utf-8');

function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    // Execute post
    $result = curl_exec($ch);
    // Close connection
    curl_close($ch);
    return $result;
}

$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
$orderInfo = "Thanh toán qua MoMo";
session_start();

$orderId = $_SESSION['ORDER_ID']; // dùng order thật

$redirectUrl = "http://localhost/khachsan/momo_return.php";
$ipnUrl = "http://localhost/khachsan/momo_ipn.php";
$extraData = "";
$requestId = time() . "";
$requestType = "captureWallet";

$amount = intval($_SESSION['payment_amount']);

if ($amount < 1000 || $amount > 50000000) {
    echo "Lỗi: Số tiền giao dịch không hợp lệ. Vui lòng kiểm tra lại.";
    exit();
}

// Before sign HMAC SHA256 signature
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

$dataJson = json_encode($data);
$result = execPostRequest($endpoint, $dataJson);
$jsonResult = json_decode($result, true);  // Decode json

// Ghi log phản hồi để kiểm tra nội dung
file_put_contents('momo_response_log.txt', print_r($jsonResult, true), FILE_APPEND);

// Kiểm tra xem khóa 'payUrl' có tồn tại không
if (isset($jsonResult['payUrl'])) {
    // Redirect to MoMo payment URL
    header('Location: ' . $jsonResult['payUrl']);
    exit();
} else {
    // Xử lý khi không có khóa 'payUrl' trong phản hồi
    echo "Lỗi: Không thể lấy URL thanh toán. Vui lòng kiểm tra log để biết thêm chi tiết.";
    // In ra toàn bộ phản hồi để kiểm tra lỗi
    echo '<pre>' . print_r($jsonResult, true) . '</pre>';
}
?>
