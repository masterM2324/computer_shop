<?php
session_start();
require_once './db/db.php';

if (!isset($_SESSION['id']) || !isset($_POST['update_cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['id'];
$cart_id = (int)$_POST['cart_item_id'];
$new_qty = (int)$_POST['new_quantity'];

if ($new_qty <= 0) {
    header("Location: remove_item.php?id=" . $cart_id);
    exit();
}

$conn->begin_transaction();

try {
    // ១. ទាញយកទិន្នន័យចាស់ពី Cart និង Stock បច្ចុប្បន្ន
    $sql = "SELECT c.quantity, c.product_id, p.qty as stock_qty 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.id = ? AND c.cart_id = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    if (!$item) throw new Exception("រកមិនឃើញទំនិញ!");

    $old_qty = $item['quantity'];
    $product_id = $item['product_id'];
    $current_stock = $item['stock_qty'];
    
    // គណនាភាពខុសគ្នា (ឧទាហរណ៍៖ បើថែម ១ ក្នុង Cart ត្រូវដក ១ ចេញពីស្តុក)
    $diff = $new_qty - $old_qty;

    if ($diff > $current_stock) {
        throw new Exception("ស្តុកមិនគ្រប់គ្រាន់!");
    }

    // ២. Update ចំនួនក្នុង Cart
    $up_cart = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $up_cart->bind_param("ii", $new_qty, $cart_id);
    $up_cart->execute();

    // ៣. Update ស្តុកក្នុង Products
    $up_stock = $conn->prepare("UPDATE products SET qty = qty - ? WHERE id = ?");
    $up_stock->bind_param("ii", $diff, $product_id);
    $up_stock->execute();

    $conn->commit();
    header("Location:view_cart.php?status=updated");

} catch (Exception $e) {
    $conn->rollback();
    header("Location: cart.php?error=" . urlencode($e->getMessage()));
}
?>