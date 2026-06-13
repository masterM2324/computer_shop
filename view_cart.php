<?php
session_start();
require_once './db/db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
// view_cart.php
$user_id = $_SESSION['id'];

// SQL Query ថ្មីដែលចម្រាញ់យកតែទំនិញមិនទាន់លុប
$sql = "SELECT 
            cart.id as cart_id, 
            products.name, 
            products.price, 
            products.image_main, 
            cart.quantity 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.cart_id = ? 
        AND cart.status = 'active'"; // <--- បន្ថែមបន្ទាត់នេះដើម្បី "ដក" ទិន្នន័យដែល Cancel ហើយចេញ

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
    <title>កន្ត្រកទំនិញ - Premium Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Khmer+OS+Siemreap&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary-hex: #6366f1;
            --primary-light: #eef2ff;
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Khmer OS Siemreap', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
        }

        /* Progress Steps */
        .step-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #94a3b8;
        }

        .step-item.active {
            color: var(--primary-hex);
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: white;
        }

        .active .step-num {
            background: var(--primary-hex);
        }

        .cart-container {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            transition: transform 0.3s ease;
        }

        .product-img:hover {
            transform: scale(1.05);
        }

        .quantity-control {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            width: fit-content;
        }

        .quantity-input {
            width: 45px;
            border: none;
            background: transparent;
            text-align: center;
            font-weight: 700;
            color: var(--primary-hex);
        }

        .btn-update {
            background: white;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
        }

        .btn-update:hover {
            background: var(--primary-hex);
            color: white;
        }

        .summary-box {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            position: sticky;
            top: 20px;
        }

        .btn-checkout {
            background: var(--primary-hex);
            color: white;
            border-radius: 16px;
            padding: 16px;
            font-weight: 700;
            border: none;
            width: 100%;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-checkout:hover {
            background: #4f46e5;
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .remove-link {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fee2e2;
            color: #ef4444;
            transition: 0.2s;
            text-decoration: none;
        }

        .remove-link:hover {
            background: #ef4444;
            color: white;
            transform: rotate(90deg);
        }

        /* Table Styling */
        .table thead th {
            border: none;
            padding-bottom: 20px;
        }

        .table tbody td {
            padding: 20px 0;
            border-color: #f8fafc;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="step-container">
            <div class="step-item active"><span class="step-num">1</span> កន្ត្រកទំនិញ</div>
            <div style="width: 40px; height: 2px; background: #e2e8f0;"></div>
            <div class="step-item"><span class="step-num">2</span> ការទូទាត់</div>
            <div style="width: 40px; height: 2px; background: #e2e8f0;"></div>
            <div class="step-item"><span class="step-num">3</span> ជោគជ័យ</div>
        </div>

        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold"><i class="fas fa-shopping-bag text-primary me-2"></i>កន្ត្រកទំនិញ</h2>
            </div>
            <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border">
                <i class="fas fa-arrow-left me-2"></i> បន្តការទិញ
            </a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-container p-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="text-muted small uppercase">
                                    <tr>
                                        <th>ផលិតផល</th>
                                        <th>តម្លៃ</th>
                                        <th>ចំនួន</th>
                                        <th class="text-end">សរុប</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_price = 0;
                                    while ($row = $result->fetch_assoc()):
                                        $subtotal = $row['price'] * $row['quantity'];
                                        $total_price += $subtotal;
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="uploads/<?php echo htmlspecialchars($row['image_main']); ?>"
                                                        class="product-img me-3">
                                                    <div>
                                                        <div class="fw-bold text-dark mb-0">
                                                            <?php echo htmlspecialchars($row['name']); ?>
                                                        </div>
                                                        <span class="badge bg-light text-muted fw-normal"
                                                            style="font-size: 10px;">ID: #<?php echo $row['cart_id']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-semibold text-dark">$<?php echo number_format($row['price'], 2); ?>
                                            </td>
                                            <td>
                                                <!--បង្កើត Form សម្រាប់ Update ចំនួន-->
                                                <form action="update_quantity.php" method="POST" class="quantity-control">
                                                    <input type="hidden" name="cart_item_id"
                                                        value="<?php echo $row['cart_id']; ?>">
                                                    <input type="number" name="new_quantity"
                                                        value="<?php echo $row['quantity']; ?>" min="1"
                                                        class="quantity-input shadow-none">
                                                    <button type="submit" name="update_cart" class="btn-update">
                                                        <i class="fas fa-sync-alt text-muted fa-xs"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold text-primary">
                                                $<?php echo number_format($subtotal, 2); ?></td>
                                            <td class="text-end ps-4">
                                                <a href="remove_item.php?id=<?php echo $row['cart_id']; ?>" class="remove-link"
                                                    onclick="return confirm('តើអ្នកច្បាស់ទេថានឹងលុបទំនិញនេះ?')">
                                                    <i class="fas fa-times small"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart-container p-4 summary-box">
                        <h5 class="fw-bold mb-4">សេចក្តីសង្ខេបការបញ្ជាទិញ</h5>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">តម្លៃសរុបទំនិញ</span>
                            <span class="fw-bold text-dark">$<?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">សេវាដឹកជញ្ជូន</span>
                            <span class="text-success fw-bold">ឥតគិតថ្លៃ</span>
                        </div>

                        <div class="p-3 bg-light rounded-4 my-4 border border-dashed">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">សរុបរួម</span>
                                <span
                                    class="h3 fw-bold text-primary mb-0">$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                        </div>

                        <a href="payment.php" class="btn btn-checkout">
                            បន្តទៅកាន់ការទូទាត់ <i class="fas fa-arrow-right ms-2"></i>
                        </a>

                        <div class="mt-4 text-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" height="20"
                                class="mx-2 opacity-50">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" height="20"
                                class="mx-2 opacity-50">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" height="15"
                                class="mx-2 opacity-50">
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="cart-container p-5 text-center border-dashed">
                <div class="mb-4 d-inline-block p-4 bg-light rounded-circle">
                    <i class="fas fa-shopping-basket fa-4x text-muted opacity-50"></i>
                </div>
                <h4 class="fw-bold">មិនមានទំនិញក្នុងកន្ត្រក!</h4>
                <p class="text-muted mx-auto" style="max-width: 400px;">
                    អ្នកមិនទាន់បានជ្រើសរើសទំនិញណាមួយចូលក្នុងកន្ត្រកនៅឡើយទេ។ សូមត្រលប់ទៅកាន់ហាងវិញដើម្បីមើលទំនិញថ្មីៗ។</p>
                <a href="index.php" class="btn btn-primary px-5 py-3 rounded-pill fw-bold mt-3 border-0"
                    style="background: var(--primary-hex);">
                    ទៅកាន់ហាងឥឡូវនេះ
                </a>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>