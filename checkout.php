<?php
session_start();
include '../config.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$total_price = isset($_GET['total_price']) ? floatval($_GET['total_price']) : 0;

if($order_id == 0 || $total_price <= 0){
    die("Invalid order ID or total price.");
}

// Optional: verify order exists in DB
$stmt = $conn->prepare("SELECT id FROM purchase_orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    die("Invalid order ID.");
}
$stmt->close();

// SSLCOMMERZ Payment
$post_data = array();
$post_data['store_id'] = "onlin68de205b6c555";
$post_data['store_passwd'] = "onlin68de205b6c555@ssl";
$post_data['total_amount'] = $total_price;
$post_data['currency'] = "BDT";
$post_data['tran_id'] = "SSLCZ_TEST_".uniqid();

// ✅ Pass order_id in success URL
$post_data['success_url'] = "http://localhost/stock/admin/success.php?order_id=".$order_id;
$post_data['fail_url'] = "http://localhost/new_sslcz_gw/fail.php";
$post_data['cancel_url'] = "http://localhost/new_sslcz_gw/cancel.php";

// Customer Info
$post_data['cus_name'] = "Test Customer";
$post_data['cus_email'] = "test@test.com";
$post_data['cus_add1'] = "Dhaka";
$post_data['cus_city'] = "Dhaka";
$post_data['cus_country'] = "Bangladesh";
$post_data['cus_phone'] = "01711111111";

// Send request
$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $direct_api_url );
curl_setopt($handle, CURLOPT_TIMEOUT, 30);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($handle, CURLOPT_POST, 1 );
curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);

$content = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);

if($code == 200 && $content){
    $sslcz = json_decode($content, true);
    if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!=""){
        header("Location: ".$sslcz['GatewayPageURL']);
        exit;
    } else {
        die("Failed to connect to payment gateway.");
    }
} else {
    die("Curl Error: Could not connect to SSLCOMMERZ.");
}
?>
