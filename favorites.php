<?php
session_start();
require_once './db/db.php';

// ១. ពិនិត្យមើលថាតើអ្នកប្រើប្រាស់បាន Login ឬនៅ
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// ២. ទាញយកផលិតផលដែលអ្នកប្រើប្រាស់បានបញ្ចូលក្នុងបញ្ជី "ចូលចិត្ត"
$sql = "SELECT p.* FROM products p 
        JOIN favorites f ON p.id = f.product_id 
        WHERE f.user_id = ? AND p.status = 'in_stock'
        ORDER BY f.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>បញ្ជីដែលចូលចិត្ត | CShop</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #ff5722;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f9f9f9;
            --border-color: #ddd;
        }

        body {
            background-color: #fff;
            color: var(--text-dark);
            font-family: 'Kantumruy Pro', sans-serif;
            margin: 0;
            padding: 0;
        }

        .favorite-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 30px auto 0;
            padding: 0 20px;
        }

        .favorite-header h2 {
            font-size: 1.5rem;
            margin: 0;
        }

        .product-container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .product-grid {
            display: grid;
            /* កែសម្រួលទំហំ Card អប្បបរមាឡើងដល់ 300px ដើម្បីឱ្យសមស្របនឹងរូបភាពរាងផ្តេក */
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        /* --- រចនាបថ Card --- */
        .product-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        /* ប៊ូតុងបេះដូងសម្រាប់លុបចេញ */
        .remove-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #e74c3c;
            cursor: pointer;
            font-size: 20px;
            z-index: 10;
            background: white;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .remove-favorite:hover {
            transform: scale(1.2);
            background: #e74c3c;
            color: white;
        }

        /* កែសម្រួលប្រអប់ផ្ទុកដាក់រូបភាព */
        .product-image-container {
            background-color: #f8f8f8;
            border-radius: 12px;
            height: 160px; /* បន្ថយកម្ពស់ចុះដើម្បីកុំឱ្យ Card មើលទៅធំពេក */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }

        /* កែសម្រួលស្ទីលរូបភាពដើម្បីកុំឱ្យរួមតូចពេក */
        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* កាត់តម្រឹមរូបភាពឱ្យពេញប្រអប់ស្អាត (មិនឱ្យសល់ផ្ទៃពណ៌ប្រផេះសងខាង) */
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image-container img {
            transform: scale(1.05);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-name {
            font-size: 1.05rem;
            font-weight: bold;
            color: var(--text-dark);
            margin: 0 0 10px 0;
            height: 2.4em; 
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .price-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto; 
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .add-to-cart {
            background-color: var(--text-dark);
            color: white;
            border: 2px solid black;
            padding: 8px 15px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
            font-weight: bold;
        }

        .add-to-cart:hover {
            background-color: white;
            color: black;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .empty-container {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
        }

        /* ==========================================================================
           --- Responsive (Media Queries) ---
           ========================================================================== */
        
        @media (max-width: 768px) {
            .favorite-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
                margin-top: 20px;
            }
            .favorite-header h2 {
                font-size: 1.3rem;
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 15px;
            }
            .product-image-container {
                height: 140px;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr; /* លើទូរស័ព្ទតូច បង្ហាញ ១ជួរពេញតែម្ដង ព្រោះរូបភាពជាប្រភេទ Banner ផ្តេក */
                gap: 15px;
            }
            .product-container {
                padding: 0 15px;
            }
            .favorite-header {
                padding: 0 15px;
            }
            .product-card {
                padding: 15px;
                border-radius: 15px;
            }
            .product-image-container {
                height: 150px;
            }
            .price-action {
                flex-direction: row; /* រក្សាតម្លៃ និងប៊ូតុងឱ្យនៅទន្ទឹមគ្នាដដែល */
                align-items: center;
            }
            .add-to-cart {
                width: auto;
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="favorite-header">
        <h2 class="category-title"><i class="fas fa-heart" style="color: #e74c3c;"></i> បញ្ជីដែលខ្ញុំចូលចិត្ត</h2>
        <a href="index.php" style="text-decoration: none; color: #3498db; font-weight: bold;">
            <i class="fas fa-arrow-left"></i> ត្រឡប់ទៅទិញទំនិញវិញ
        </a>
    </div>

    <div class="product-container">
        <div class="product-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="remove-favorite" onclick="toggleFavorite(<?php echo $row['id']; ?>, this)" title="លុបចេញ">
                            <i class="fas fa-heart"></i>
                        </div>

                        <div class="product-image-container">
                            <img src="uploads/<?php echo htmlspecialchars($row['image_main']); ?>" alt="Product">
                        </div>

                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                            
                            <div class="price-action">
                                <div class="price">$<?php echo number_format($row['price'], 2); ?></div>
                                <button type="button" class="add-to-cart" onclick="addToCart(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-cart-plus"></i> បន្ថែម
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-container">
                    <i class="fas fa-heart-broken" style="font-size: 60px; color: #ccc;"></i>
                    <h3 class="mt-3">មិនទាន់មានផលិតផលទេ</h3>
                    <p>ផលិតផលដែលអ្នក Like នឹងបង្ហាញនៅទីនេះ</p>
                    <a href="index.php" class="add-to-cart" style="display: inline-block; text-decoration: none; margin-top: 15px;">ទៅមើលផលិតផល</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function addToCart(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ជោគជ័យ!',
                        text: 'ផលិតផលត្រូវបានបន្ថែមទៅកន្ត្រក',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
            .catch(err => console.error('Error:', err));
        }

        function toggleFavorite(productId, element) {
            let formData = new FormData();
            formData.append('product_id', productId);
            
            fetch('toggle_favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'removed') {
                    const card = element.closest('.product-card');
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px) scale(0.8)';
                    card.style.transition = '0.4s';
                    
                    setTimeout(() => {
                        card.remove();
                        if (document.querySelectorAll('.product-card').length === 0) {
                            location.reload();
                        }
                    }, 400);
                }
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
</body>
</html>