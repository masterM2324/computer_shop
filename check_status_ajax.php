<?php
session_start(); // ត្រូវតែមាន Session ដើម្បីដឹងថាជា User ណា
header('Content-Type: application/json');
require_once './db/db.php';

// ១. ពិនិត្យមើលថាតើមានការ Login ដែរឬទេ
if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$user_id = $_SESSION['id']; // ID របស់អ្នកដែលកំពុងប្រើប្រាស់
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Order ID']);
    exit;
}

// ២. ឆែកមើលស្ថានភាពក្នុង Database ដោយបង្ខំឱ្យត្រូវទាំង Order ID និង User ID
// ការធ្វើបែបនេះការពារមិនឱ្យ User ម្នាក់ទៅលួចមើល Status របស់ User ម្នាក់ទៀត (ជៀសវាងការជាន់ ID)
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if ($order) {
    // ៣. បញ្ជូនលទ្ធផលទៅឱ្យ JavaScript
    echo json_encode([
        'status' => $order['status'] 
    ]);
} else {
    // ប្រសិនបើរកមិនឃើញ ឬ ID នោះមិនមែនជារបស់ User នេះ
    echo json_encode([
        'status' => 'not_found'
    ]);
}

$stmt->close();
$conn->close();
?>