<?php
session_start();
require_once 'check_auth.php';
require_once './db/db.php'; 
header('Content-Type: application/json');

// ១. ចាប់យកតម្លៃឱ្យបានច្បាស់
$user_id = $_SESSION['id'] ?? 0;
$p_id = (int)($_POST['product_id'] ?? 0);
$qty = (int)($_POST['quantity'] ?? 1); // នេះជាលេខ 5 ដែលផ្ញើមកពី JS

if ($user_id == 0 || $p_id == 0) {
    echo json_encode(['status' => 'error', 'message' => 'ទិន្នន័យមិនគ្រប់គ្រាន់']);
    exit();
}

$conn->begin_transaction();
try {
    // ២. ឆែកមើលក្នុងកន្ត្រក
    $check = $conn->prepare("SELECT id FROM cart WHERE cart_id = ? AND product_id = ? AND status = 'active'");
    $check->bind_param("ii", $user_id, $p_id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if ($res) {
        // បើមានស្រាប់៖ UPDATE quantity + $qty
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $stmt->bind_param("ii", $qty, $res['id']);
    } else {
        // បើថ្មី៖ INSERT quantity យកតាម $qty តែម្តង
        $stmt = $conn->prepare("INSERT INTO cart (cart_id, product_id, quantity, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("iii", $user_id, $p_id, $qty);
    }
    $stmt->execute();

    // ៣. កាត់ស្តុកចេញពី table products (Column 'qty')
    $stock = $conn->prepare("UPDATE products SET qty = qty - ? WHERE id = ?");
    $stock->bind_param("ii", $qty, $p_id);
    $stock->execute();

    $conn->commit();

    // ៤. រាប់ចំនួនផ្ញើទៅវិញ
    $count = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE cart_id = ? AND status = 'active'");
    $count->bind_param("i", $user_id);
    $count->execute();
    $total = $count->get_result()->fetch_assoc()['total'] ?? 0;

    echo json_encode(['status' => 'success', 'message' => "បានថែម $qty គ្រឿង", 'cart_count' => $total]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}