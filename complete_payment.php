<?php
session_start();
require_once './db/db.php';
$user_id = $_SESSION['id'];
$order_id = $_SESSION['last_order_id'];

// លុប Cart ចោលព្រោះការទិញបានសម្រេច
$conn->query("DELETE FROM cart WHERE cart_id = $user_id");

header("Location: order_success.php?id=" . $order_id);
exit();