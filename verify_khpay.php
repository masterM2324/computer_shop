<?php
session_start();
require_once './db/db.php';

$api_key = "ak_27b555b6f0675a96e90a2921d101c37e25a841066531cbad";
$order_id = $_POST['order_id'] ?? '';

if (empty($order_id)) {
    die("រកមិនឃើញលេខកូដបញ្ជាទិញឡើយ។");
}

// ឆែកស្ថានភាពជាមួយ KHPay API
$url = "https://khpay.site/api/v1/qr/check-status?note=" . urlencode($order_id);
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $api_key],
]);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

// ប្រសិនបើបង់ប្រាក់រួចរាល់ (is_paid == true)
if (isset($data['data']['is_paid']) && $data['data']['is_paid'] == true) {
    // កែប្រែ Status ទៅជា 'Paid' ក្នុង Database
    $stmt = $conn->prepare("UPDATE orders SET status = 'Paid' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        header("Location: success.php?id=" . $order_id);
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    // បើមិនទាន់បង់ទេ ឱ្យត្រឡប់ទៅវិញ
    header("Location: ABA_pay.php?id=$order_id&status=unpaid");
}
exit;