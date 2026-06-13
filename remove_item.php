<?php
// remove_item.php
session_start();
require_once './db/db.php';

if (isset($_GET['id']) && isset($_SESSION['id'])) {
    $cart_id_to_cancel = $_GET['id']; 
    $user_id = $_SESSION['id'];

    // ទាញយក product_id និង quantity ពីកន្ត្រក
    $check_sql = "SELECT product_id, quantity FROM cart WHERE id = ? AND cart_id = ? AND status = 'active'";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $cart_id_to_cancel, $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {
        $p_id = $row['product_id'];
        $cart_qty = $row['quantity'];

        $conn->begin_transaction();
        try {
            // បូកចូលស្តុកវិញ (Table products, Column qty)
            $update_stock = "UPDATE products SET qty = qty + ? WHERE id = ?";
            $stmt_stock = $conn->prepare($update_stock);
            $stmt_stock->bind_param("ii", $cart_qty, $p_id);
            $stmt_stock->execute();

            // ប្តូរ status ទៅជា removed ដើម្បីឱ្យបាត់ពីកន្ត្រក
            $cancel_cart = "UPDATE cart SET status = 'removed' WHERE id = ? AND cart_id = ?";
            $stmt_cart = $conn->prepare($cancel_cart);
            $stmt_cart->bind_param("ii", $cart_id_to_cancel, $user_id);
            $stmt_cart->execute();

            $conn->commit();
            $_SESSION['message'] = "✅ បានដកទំនិញចេញ និងបូកចូលស្តុកវិញហើយ។";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "❌ Error: " . $e->getMessage();
        }
    }
    $conn->close();
    header("Location: view_cart.php");
    exit();
}