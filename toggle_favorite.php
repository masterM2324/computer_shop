<?php
session_start();
require_once './db/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$user_id = $_SESSION['id'];
$product_id = $_POST['product_id'];

// ឆែកមើលថាមានហើយឬនៅ
$check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // បើមានហើយ លុបចេញវិញ (Unlike)
    $del = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    echo json_encode(['status' => 'removed']);
} else {
    // បើមិនទាន់មាន បញ្ចូលថ្មី (Like)
    $ins = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $product_id);
    $ins->execute();
    echo json_encode(['status' => 'added']);
}
?>