<?php
require_once './db/db.php';

$search = isset($_POST['query']) ? mysqli_real_escape_string($conn, $_POST['query']) : '';

if ($search !== '') {
    $sql = "SELECT * FROM Products WHERE name LIKE '%$search%' OR cpu LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM Products";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $qty = $row['qty'];
        $is_out_of_stock = ($qty <= 0);
        
        // បង្ហាញ Card ផលិតផល (Amazon Style របស់អ្នក)
        ?>
<div class="product-card">
    <h3><?php echo $row['name']; ?></h3>
    <p>តម្លៃ: $<?php echo number_format($row['price'], 2); ?></p>
    <button class="add-to-cart" <?php echo $is_out_of_stock ? 'disabled' : ''; ?>>
        <?php echo $is_out_of_stock ? 'Sold Out' : 'Add to Cart'; ?>
    </button>
</div>
<?php
    }
} else {
    echo "<p style='padding: 20px;'>រកមិនឃើញផលិតផល!</p>";
}
?>