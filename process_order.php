<?php
session_start();
require_once './db/db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];

    // ១. គណនាតម្លៃសរុបតែទំនិញណាដែល 'active' ប៉ុណ្ណោះ
    $total_amount = 0;
    $cart_check = "SELECT p.price, c.quantity FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.cart_id = ? AND c.status = 'active'";
    
    $stmt_check = $conn->prepare($cart_check);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $res_cart = $stmt_check->get_result();

    if ($res_cart->num_rows == 0) {
        die("កំហុស៖ គ្មានទំនិញក្នុងកន្ត្រកសម្រាប់ទូទាត់ឡើយ។");
    }

    while ($row = $res_cart->fetch_assoc()) {
        $total_amount += ($row['price'] * $row['quantity']);
    }

    // ២. បញ្ចូលទៅក្នុងតារាង orders
    $order_sql = "INSERT INTO orders (user_id, full_name, phone, address, total_amount, payment_method, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
    $stmt_order = $conn->prepare($order_sql);
    $stmt_order->bind_param("isssds", $user_id, $full_name, $phone, $address, $total_amount, $payment_method);

    if ($stmt_order->execute()) {
        $last_order_id = $stmt_order->insert_id;

        // ៣. ចំណុចសំខាន់ (Fix 76): ប្តូរ status ក្នុង cart ទៅជា 'removed' ភ្លាម
        // ដើម្បីកុំឱ្យការទិញលើកក្រោយយកតម្លៃនេះទៅបូកបញ្ចូលទៀត
        $update_cart = "UPDATE cart SET status = 'removed' WHERE cart_id = ? AND status = 'active'";
        $stmt_up = $conn->prepare($update_cart);
        $stmt_up->bind_param("i", $user_id);
        $stmt_up->execute();

        // ទៅកាន់ទំព័របង់ប្រាក់
        $_SESSION['last_order_id'] = $last_order_id;
        header("Location: pay_aba.php?order_id=" . $last_order_id);
        exit();
    }
}
?>