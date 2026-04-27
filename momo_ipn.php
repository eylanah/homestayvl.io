<?php
// IPN đơn giản nhất - chỉ ghi log và trả về OK
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Ghi log đơn giản
$log = date('Y-m-d H:i:s') . " | IPN CALLED\n";
$log .= "INPUT: " . $input . "\n";
$log .= "PARSED: " . print_r($data, true) . "\n";
$log .= "-----------------\n";
file_put_contents("momo_ipn_log.txt", $log, FILE_APPEND);

// Trả về OK cho Momo - QUAN TRỌNG: phải trả về HTTP 200
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(["message" => "OK"]);
?>
