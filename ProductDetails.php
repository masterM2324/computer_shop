<?php
require_once './db/db.php';

// ចាប់យក ID ពី URL (ឧទាហរណ៍៖ ProductDetails.php?id=1)
$product_id = isset($_GET['id']) ? $_GET['id'] : 1;

$sql = "SELECT * FROM Products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("រកមិនឃើញផលិតផលនេះទេ!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/ProductDetails.css">
    <title><?php echo $product['name']; ?> - CShop</title>
</head>

<body>

    <div class="product-card">
        <div class="main-product">
            <div class="product-image-container">
                <img src="images/<?php echo $product['image_main']; ?>" alt="<?php echo $product['name']; ?>">
            </div>

            <div class="product-details">
                <div class="title-arrow">
                    <h2><?php echo $product['name']; ?></h2>
                    <a href="index.php" class="arrow">&#8592; ត្រឡប់ក្រោយ</a>
                </div>

                <ul class="specs">
                    <li>CPU: <?php echo $product['cpu']; ?></li>
                    <li>RAM: <?php echo $product['ram']; ?></li>
                    <li>SSD: <?php echo $product['ssd']; ?></li>
                    <li>GPU: <?php echo $product['gpu']; ?></li>
                </ul>

                <p class="price">Price: $<?php echo number_format($product['price'], 2); ?></p>
            </div>
        </div>

        <div class="thumbnail-gallery">
            <img src="images/<?php echo $product['image_main']; ?>" alt="Thumb">
            <img src="images/<?php echo $product['image_main']; ?>" alt="Thumb">
        </div>

        <div class="order-container">
            <form action="Order.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit" class="order-button">Order now</button>
            </form>
        </div>
    </div>

</body>

</html>