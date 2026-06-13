<?php
require_once 'db/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Query យកទិន្នន័យផលិតផលរួមទាំង Spec តាម ID
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        $qty = $product['qty'];
        $is_out_of_stock = ($qty <= 0);
        $category = $product['category']; // ចាប់យកប្រភេទ Category
        ?>
        <div class="qv-grid">
            <div class="qv-image">
                <img src="uploads/<?php echo $product['image_main']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="qv-details">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p style="color: #777; margin-bottom: 5px;">Category: <?php echo htmlspecialchars($category); ?></p>
                <div class="qv-price">$<?php echo number_format($product['price'], 2); ?></div>
                
                <?php 
                // ប្រសិនបើប្រភេទជា Accessories បង្ហាញតែស្ថានភាព Stock (មិនបង្ហាញ CPU, RAM, SSD, GPU ទេ)
                if ($category === 'Accessories'): 
                ?>
                    <div style="margin-bottom: 15px; font-size: 14px;">
                        <strong>Stock:</strong> 
                        <?php echo $is_out_of_stock ? 
                            '<span style="color: #B12704; font-weight: bold;">Out of Stock</span>' : 
                            '<span style="color: #007600;">In Stock ('.$qty.')</span>'; ?>
                    </div>
                <?php 
                // ប្រសិនបើមិនមែនជា Accessories (ដូចជា Laptop, Desktop) ឱ្យបង្ហាញតារាង Specs ទាំងអស់
                else: 
                ?>
                    <table class="spec-table">
                        <tr><td>CPU</td><td><?php echo htmlspecialchars($product['cpu'] ?: 'N/A'); ?></td></tr>
                        <tr><td>RAM</td><td><?php echo htmlspecialchars($product['ram'] ?: 'N/A'); ?></td></tr>
                        <tr><td>SSD</td><td><?php echo htmlspecialchars($product['ssd'] ?: 'N/A'); ?></td></tr>
                        <tr><td>GPU</td><td><?php echo htmlspecialchars($product['gpu'] ?: 'N/A'); ?></td></tr>
                        <tr><td>Stock</td><td>
                            <?php echo $is_out_of_stock ? 
                                '<span style="color: #B12704; font-weight: bold;">Out of Stock</span>' : 
                                '<span style="color: #007600;">In Stock ('.$qty.')</span>'; ?>
                        </td></tr>
                    </table>
                <?php endif; ?>

                <div style="margin-top: 15px;">
                    <h4 style="margin-bottom: 5px; font-size: 15px; color:#444;">ការពិពណ៌នា៖</h4>
                    <p style="font-size: 13px; color: #666; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($product['description'] ?: 'មិនមានការពិពណ៌នាឡើយ។')); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<p style='padding:20px; text-align:center;'>រកមិនឃើញផលិតផលឡើយ។</p>";
    }
    $stmt->close();
    $conn->close();
}
?>