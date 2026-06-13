<?php
session_start();
require_once './db/db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// ចាប់យកព័ត៌មានពី Form បញ្ជាទិញ
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method'];
$total_amount = $_POST['total_amount'];

// ចាប់ផ្ដើម Transaction ដើម្បីធានាសុវត្ថិភាពទិន្នន័យ
$conn->begin_transaction();

try {
    // ១. បញ្ចូលទិន្នន័យទៅក្នុង Table `orders`
    $order_sql = "INSERT INTO orders (user_id, full_name, phone, address, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($order_sql);
    $stmt_order->bind_param("isssds", $user_id, $full_name, $phone, $address, $total_amount, $payment_method);
    $stmt_order->execute();
    $order_id = $conn->insert_id;

    // ២. ទាញទំនិញពី `cart` របស់ User មកពិនិត្យ និងកាត់ស្តុក
    $cart_sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.qty as current_stock 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.cart_id = ?";
    $stmt_cart = $conn->prepare($cart_sql);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart_result = $stmt_cart->get_result();

    if ($cart_result->num_rows == 0) {
        throw new Exception("កន្ត្រកទំនិញរបស់អ្នកគឺទទេ!");
    }

    while ($item = $cart_result->fetch_assoc()) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $product_name = $item['name'];
        $current_stock = $item['current_stock'];

        // ក. ពិនិត្យថាតើស្តុកគ្រប់គ្រាន់ដែរឬទេ
        if ($quantity > $current_stock) {
            throw new Exception("ទំនិញ '{$product_name}' មិនមានស្តុកគ្រប់គ្រាន់ទេ! (នៅសល់: {$current_stock})");
        }

        // ខ. កាត់ចំនួនចេញពីស្តុកក្នុង Table `products`
        $update_stock = "UPDATE products SET qty = qty - ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_stock);
        $stmt_update->bind_param("ii", $quantity, $product_id);
        $stmt_update->execute();

        // គ. បញ្ចូលទៅក្នុង Table `order_items`
        $item_sql = "INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($item_sql);
        $stmt_item->bind_param("isdi", $order_id, $product_name, $price, $quantity);
        $stmt_item->execute();
    }

    // ៣. លុបទិន្នន័យក្នុងកន្ត្រក (Cart) ក្រោយទិញរួច
    $delete_cart = "DELETE FROM cart WHERE cart_id = ?";
    $stmt_del = $conn->prepare($delete_cart);
    $stmt_del->bind_param("i", $user_id);
    $stmt_del->execute();

    // បញ្ជាក់ថាជោគជ័យទាំងអស់
    $conn->commit();
    $_SESSION['message'] = "✅ ការបញ្ជាទិញជោគជ័យ! ទំនិញត្រូវបានកាត់ចេញពីស្តុក។";
    header("Location: success.php?order_id=" . $order_id);

} catch (Exception $e) {
    // បើមានបញ្ហា វានឹងមិនកាត់ស្តុក ហើយមិនបង្កើត Order ឡើយ
    $conn->rollback();
    $_SESSION['message'] = "❌ កំហុស៖ " . $e->getMessage();
    header("Location: view_cart.php");
}
?>