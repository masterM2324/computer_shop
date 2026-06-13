<?php
session_start();
require_once './db/db.php';

// ២. ទាញយកផលិតផលដែលជាប្រភេទ Laptop (ប្តូរត្រង់នេះ)
$sql = "SELECT * FROM products WHERE category = 'Laptop' ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptops - CShop</title>
    <link rel="stylesheet" href="CSS/indexstyle.css">
    <link rel="stylesheet" href="CSS/product-container.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;700&display=swap" rel="stylesheet">

    <script src="https://kit.fontawesome.com/a5f77d1fc9.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .category-title {
            text-align: center;
            margin: 30px 0;
            font-family: 'Kantumruy Pro', sans-serif;
            color: #333;
        }

        /* --- Quick View Modal CSS --- */
        .qv-modal {
            display: none; 
            position: fixed; 
            z-index: 9999; 
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }
        .qv-modal-content {
            background-color: #fff;
            width: 90%;
            max-width: 800px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 5px 25px rgba(0,0,0,0.3);
            animation: qvScaleUp 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }
        @keyframes qvScaleUp {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .qv-close-btn {
            position: absolute;
            right: 15px; top: 10px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            z-index: 10;
            transition: 0.2s;
        }
        .qv-close-btn:hover { color: #000; }
        .qv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; padding: 30px; }
        .qv-image img { width: 100%; border-radius: 8px; object-fit: cover; }
        .qv-details h2 { margin-bottom: 10px; font-size: 24px; color: #333; }
        .qv-price { font-size: 22px; color: #2575fc; font-weight: bold; margin-bottom: 15px; }
        .spec-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .spec-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 14px; }
        .spec-table td:first-child { font-weight: bold; color: #666; width: 30%; }
        @media (max-width: 768px) {
            .qv-grid { grid-template-columns: 1fr; padding: 20px; }
            .qv-modal-content { width: 95%; max-height: 95vh; }
        }
    </style>
</head>

<body>

    <?php include 'Component/header.php'; ?>

    <main>
        <section class="product-grid" id="product-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $qty = $row['qty'] ?? 0;
                    $is_out_of_stock = ($qty <= 0);
                    ?>
                    <div class="product-card">
                        <div class="product-image-container" style="position: relative;">
                            <?php
                            $is_faved = false;

                            // ពិនិត្យមើលថាតើ User បាន Login ឬនៅ
                            if (isset($_SESSION['id'])) {
                                $u_id = $_SESSION['id'];
                                $p_id = $row['id']; // ✨ បានបន្ថែមបន្ទាត់នេះ (កាលពីមុនបាត់)
                                    
                                // ប្រើ Query ដើម្បីឆែកមើលទិន្នន័យក្នុង table favorites
                                $check_fave = $conn->query("SELECT id FROM favorites WHERE user_id = $u_id AND product_id = $p_id");
                                if ($check_fave && $check_fave->num_rows > 0) {
                                    $is_faved = true;
                                }
                            }
                            ?>

                            <button type="button" class="wishlist-btn" onclick="toggleFavorite(<?php echo $row['id']; ?>, this)"
                                style="position: absolute; top: 10px; right: 10px; background: white; border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 5; color: #ff4757;">
                                <i class="<?php echo $is_faved ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>

                            <img src="uploads/<?php echo $row['image_main']; ?>" alt="<?php echo $row['name']; ?>"
                                 class="open-quickview" data-id="<?php echo $row['id']; ?>" style="cursor: pointer;">
                        </div>
                        <div class="product-details">
                            <h3 class="product-name open-quickview" data-id="<?php echo $row['id']; ?>" style="cursor: pointer;">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </h3>
                            <p><strong><?php echo htmlspecialchars($row['category']); ?></strong></p>

                            <div class="stock-info" style="margin-bottom: 10px;">
                                <?php echo $is_out_of_stock ?
                                    '<span style="color: #B12704; font-weight: bold;"><i class="fas fa-times-circle"></i> Out of Stock</span>' :
                                    '<span style="color: #007600;"><i class="fas fa-check-circle"></i> In Stock: ' . $qty . '</span>'; ?>
                            </div>

                            <div class="price-action">
                                <span class="price">$<?php echo number_format($row['price'], 2); ?></span>

                                <?php if ($is_out_of_stock): ?>
                                    <button class="add-to-cart" disabled>Sold Out</button>
                                <?php else: ?>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                        <input type="number" id="qty_<?php echo $row['id']; ?>" value="1" min="1"
                                            max="<?php echo $qty; ?>"
                                            style="width: 55px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">

                                        <button type="button" class="add-to-cart"
                                            onclick="addToCart(<?php echo $row['id']; ?>, document.getElementById('qty_<?php echo $row['id']; ?>').value)">
                                            Add to Cart
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div style='grid-column: 1/-1; text-align: center; padding: 50px;'>
                <i class='fas fa-search' style='font-size: 50px; color: #ccc;'></i>
                <p style='margin-top: 10px;'>រកមិនឃើញផលិតផលដែលអ្នកចង់ស្វែងរកទេ។</p>
                <a href='index.php' style='color: #007bff;'>បង្ហាញផលិតផលទាំងអស់ឡើងវិញ</a>
              </div>";
            }
            ?>
        </section>
    </main>

    <div id="quickViewModal" class="qv-modal">
        <div class="qv-modal-content">
            <span class="qv-close-btn">&times;</span>
            <div class="qv-modal-body" id="qvLoadContent">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 30px; color: #6a11cb;"></i>
                    <p style="margin-top: 10px;">កំពុងផ្ទុកទិន្នន័យ...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // បន្ថែម isLoggedIn ដើម្បីឱ្យ Card.js ស្គាល់
        const isLoggedIn = <?php echo isset($_SESSION['id']) ? 'true' : 'false'; ?>;

        function toggleFavorite(productId, element) {
            let formData = new FormData();
            formData.append('product_id', productId);
            fetch('toggle_favorite.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'added') element.querySelector('i').classList.replace('far', 'fas');
                else if (data.status === 'removed') element.querySelector('i').classList.replace('fas', 'far');
                else if (data.status === 'not_logged_in') window.location.href = 'login.php';
            });
        }

        // --- ✨ JavaScript សម្រាប់ដំណើរការ Quick View Modal តាមរយៈ Fetch API ---
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("quickViewModal");
            const closeBtn = document.querySelector(".qv-close-btn");
            const contentArea = document.getElementById("qvLoadContent");
            const productContainer = document.getElementById('product-container');

            if (productContainer) {
                productContainer.addEventListener('click', function(e) {
                    const target = e.target.closest('.open-quickview');
                    if (target) {
                        const productId = target.getAttribute('data-id');
                        modal.style.display = "flex"; 

                        contentArea.innerHTML = `
                            <div style="text-align: center; padding: 50px; width:100%;">
                                <i class="fas fa-spinner fa-spin" style="font-size: 35px; color: #6a11cb;"></i>
                                <p style="margin-top: 10px;">កំពុងផ្ទុកទិន្នន័យលម្អិត...</p>
                            </div>`;

                        fetch(`get_product_details.php?id=${productId}`)
                            .then(response => response.text())
                            .then(html => {
                                contentArea.innerHTML = html;
                            })
                            .catch(err => {
                                contentArea.innerHTML = "<p style='color:red; padding:20px; text-align:center;'>មិនអាចទាញទិន្នន័យបានទេ។</p>";
                            });
                    }
                });
            }

            closeBtn.onclick = function() { modal.style.display = "none"; }
            window.onclick = function(event) {
                if (event.target == modal) { modal.style.display = "none"; }
            }
        });
    </script>

    <script src="js/script.js"></script>
    <script src="js/Card.js"></script>
</body>
</html>