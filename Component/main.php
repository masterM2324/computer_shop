<main>
    <style>
        /* Modal Background Backdrop */
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

        /* Modal Content Box */
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

        /* Close Button */
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

        /* Grid Layout Inside Modal */
        .qv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            padding: 30px;
        }
        .qv-image img {
            width: 100%;
            border-radius: 8px;
            object-fit: cover;
        }
        .qv-details h2 { margin-bottom: 10px; font-size: 24px; color: #333; }
        .qv-price { font-size: 22px; color: #2575fc; font-weight: bold; margin-bottom: 15px; }

        /* Table spec style */
        .spec-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .spec-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .spec-table td:first-child { font-weight: bold; color: #666; width: 30%; }

        /* Responsive សម្រាប់ទូរស័ព្ទ */
        @media (max-width: 768px) {
            .qv-grid { grid-template-columns: 1fr; padding: 20px; }
            .qv-modal-content { width: 95%; max-height: 95vh; }
        }
    </style>

    <section class="hero-banner">
        <div class="slider-container">
            <div class="slide fade"><img src="images/banner1.png" alt="promotion 1"></div>
            <div class="slide fade"><img src="images/C.png" alt="promotion 2"></div>
            <div class="slide fade"><img src="images/lda.png" alt="promotion 3"></div>
        </div>
        <div class="dot-container">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </section>

    <section class="product-grid" id="product-container">
        <?php
        require_once './db/db.php';

        // ១. ចាប់យកតម្លៃពី URL (Category និង Search)
        $category_filter = isset($_GET['category']) ? $_GET['category'] : '';
        $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

        // ២. រៀបចំ SQL ជាមូលដ្ឋាន (បង្ហាញតែផលិតផលដែលមាន status = 'in_stock')
        $sql = "SELECT * FROM products WHERE status = 'in_stock'";
        $params = [];
        $types = "";

        // ៣. បន្ថែមលក្ខខណ្ឌ Search ប្រសិនបើមានការវាយបញ្ចូល
        if (!empty($search_query)) {
            $sql .= " AND name LIKE ?";
            $params[] = "%$search_query%";
            $types .= "s";
        }

        // ៤. បន្ថែមលក្ខខណ្ឌ Category ប្រសិនបើមានការជ្រើសរើស
        if (!empty($category_filter)) {
            $sql .= " AND category = ?";
            $params[] = $category_filter;
            $types .= "s";
        }

        $sql .= " ORDER BY id DESC";

        // ៥. Prepare និង Execute
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // ៦. បង្ហាញលទ្ធផល
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $qty = $row['qty'] ?? 0;
                $is_out_of_stock = ($qty <= 0);

                $is_faved = false;
                if (isset($_SESSION['id'])) {
                    $u_id = $_SESSION['id'];
                    $p_id = $row['id'];
                    $check_fave = $conn->query("SELECT id FROM favorites WHERE user_id = $u_id AND product_id = $p_id");
                    if ($check_fave && $check_fave->num_rows > 0) {
                        $is_faved = true;
                    }
                }
                ?>
                
                <div class="product-card">
                    <div class="product-image-container" style="position: relative;">
                        <button type="button" class="wishlist-btn" onclick="toggleFavorite(<?php echo $row['id']; ?>, this)"
                            style="position: absolute; top: 10px; right: 10px; background: white; border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 5; color: #ff4757;">
                            <i class="<?php echo $is_faved ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>

                        <img src="uploads/<?php echo $row['image_main']; ?>" alt="<?php echo $row['name']; ?>" 
                             class="open-quickview" data-id="<?php echo $row['id']; ?>" style="cursor: pointer; width: 100%;">
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
        function addToCart(productId, quantity) {
            quantity = parseInt(quantity);

            if (quantity <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'ខុសឆ្គង!',
                    text: 'សូមបញ្ចូលចំនួនទំនិញយ៉ាងតិច ១'
                });
                return;
            }

            if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
                Swal.fire({
                    icon: 'warning',
                    title: 'សូមចូលគណនីជាមុនសិន!',
                    text: 'អ្នកត្រូវតែ Login ចូលប្រព័ន្ធទើបអាចទិញទំនិញបាន biographies។',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ទៅកាន់ទំព័រ Login',
                    cancelButtonText: 'បោះបង់'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
                return;
            }

            let formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if(document.getElementById('cart-count')) {
                        document.getElementById('cart-count').innerText = data.cart_count;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'ជោគជ័យ!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'មានបញ្ហា!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'មានបញ្ហា!',
                    text: 'មិនអាចទាក់ទងទៅកាន់ Server បានទេ'
                });
            });
        }

        function toggleFavorite(productId, element) {
            const icon = element.querySelector('i');
            let formData = new FormData();
            formData.append('product_id', productId);

            fetch('toggle_favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'added') {
                    icon.className = 'fas fa-heart';
                    Swal.fire({
                        icon: 'success',
                        title: 'ជោគជ័យ!',
                        text: 'បានបន្ថែមទៅក្នុងបញ្ជីដែលចូលចិត្ត',
                        showConfirmButton: false,
                        timer: 1500,
                    });
                } else if (data.status === 'removed') {
                    icon.className = 'far fa-heart';
                    Swal.fire({
                        icon: 'info',
                        title: 'បានលុបចេញ!',
                        text: 'ផលិតផលត្រូវបានដកចេញពីបញ្ជីចូលចិត្ត',
                        showConfirmButton: false,
                        timer: 1500,
                    });
                } else if (data.status === 'not_logged_in') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'សូមចូលប្រើប្រាស់!',
                        text: 'អ្នកត្រូវតែ Login ជាមុនសិនដើម្បីប្រើមុខងារនេះ',
                        confirmButtonText: 'ទៅកាន់ទំព័រ Login',
                        confirmButtonColor: '#3085d6',
                        showCancelButton: true,
                        cancelButtonText: 'បោះបង់'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'មានបញ្ហា!',
                    text: 'មិនអាចទាក់ទងទៅកាន់ Server បានទេ'
                });
            });
        }

        // --- Quick View Modal Functionality ---
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("quickViewModal");
            const closeBtn = document.querySelector(".qv-close-btn");
            const contentArea = document.getElementById("qvLoadContent");

            // ប្រើ Event Delegation ដើម្បីកុំឱ្យប៉ះពាល់ពេលមាន Filter ឬ Search លោតមកថ្មី
            document.getElementById('product-container').addEventListener('click', function(e) {
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

            // ចុចប៊ូតុងខ្វែងបិទ Modal
            closeBtn.onclick = function() { modal.style.display = "none"; }

            // ចុចខាងក្រៅប្រអប់សដើម្បីបិទ Modal
            window.onclick = function(event) {
                if (event.target == modal) { modal.style.display = "none"; }
            }
        });
    </script>
</main>