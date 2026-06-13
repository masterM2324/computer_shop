<?php

session_start();
require_once './db/db.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = "⚠️ សូមចូលគណនីដើម្បីបន្តការទូទាត់។";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$total_price = 0;

// កែសម្រួល SQL ដើម្បីឆែក status 'active' ចេញពីតារាង cart (c.status)
$sql = "SELECT p.name, p.image_main, p.price, c.quantity, c.status 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.cart_id = ? AND c.status = 'active'";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

$items_data = [];
while ($item = $cart_items->fetch_assoc()) {
    $total_price += ($item['price'] * $item['quantity']);
    $items_data[] = $item;
}

if ($total_price == 0) {
    $_SESSION['message'] = "⚠️ រទេះទំនិញរបស់អ្នកនៅទទេ!";
    header("Location: view_cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Secure Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1;
            --primary-soft: #eef2ff;
            --slate-900: #0f172a;
            --slate-500: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif;
            /* បន្ថែមរូបភាព Background បែប Abstract ស្រទន់ */
            background: linear-gradient(rgba(248, 250, 252, 0.8), rgba(248, 250, 252, 0.8)),
                url('https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--slate-900);
        }

        /* បង្កើនសម្រស់ Card ឱ្យមានលក្ខណៈ Glassmorphism */
        .glass-checkout {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            margin-top: 20px;
        }

        .step-container {
            margin-bottom: 30px;
        }

        .step-num {
            width: 28px;
            height: 28px;
            font-weight: bold;
        }

        .form-section {
            padding: 50px;
        }

        .summary-section {
            background: rgba(249, 250, 251, 0.5);
            padding: 50px;
            border-left: 1px solid #edf2f7;
        }

        .payment-option {
            border: 2px solid #f1f5f9;
            background: white;
            padding: 20px;
            border-radius: 20px;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .payment-option.active {
            border-color: var(--primary);
            background: var(--primary-soft);
            transform: scale(1.02);
        }

        .product-img {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-confirm {
            background: var(--primary);
            padding: 18px;
            border-radius: 18px;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .badge-free {
            background: #dcfce7;
            color: #166534;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="step-container d-none d-md-flex align-items-center justify-content-center gap-3">
            <div class="step-item d-flex align-items-center gap-2 text-success">
                <span
                    class="step-num border border-success rounded-circle d-flex align-items-center justify-content-center small"><i
                        class="fas fa-check"></i></span>
                <span class="fw-bold">កន្ត្រកទំនិញ</span>
            </div>
            <div style="width: 50px; height: 2px; background: #22c55e;"></div>
            <div class="step-item d-flex align-items-center gap-2 text-primary">
                <span
                    class="step-num bg-primary text-white rounded-circle d-flex align-items-center justify-content-center small">2</span>
                <span class="fw-bold">ការទូទាត់</span>
            </div>
            <div style="width: 50px; height: 2px; background: #e2e8f0;"></div>
            <div class="step-item d-flex align-items-center gap-2 text-muted">
                <span
                    class="step-num bg-light border rounded-circle d-flex align-items-center justify-content-center small text-secondary">3</span>
                <span class="fw-bold">ជោគជ័យ</span>
            </div>
        </div>
        <!--ត្រឡប់ក្រោយ-->
			<a href="view_cart.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border">
                <i class="fas fa-arrow-left me-2"></i> ត្រឡប់ក្រោយ
            </a>
        <div class="glass-checkout">
            <form action="process_order.php" method="POST">
                <div class="row g-0">
                    <div class="col-lg-7 form-section">
                        <h4 class="fw-bold mb-4">ព័ត៌មានដឹកជញ្ជូន</h4>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">ឈ្មោះពេញ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i
                                            class="far fa-user"></i></span>
                                    <input type="text" name="full_name" class="form-control border-start-0"
                                        placeholder="ឧ. សុខ មករា" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">លេខទូរស័ព្ទ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i
                                            class="fas fa-phone-alt"></i></span>
                                    <input type="text" name="phone" class="form-control border-start-0"
                                        placeholder="012 345 678" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">អាសយដ្ឋាន</label>
                                <textarea name="address" class="form-control" rows="3"
                                    placeholder="លេខផ្ទះ, ផ្លូវ, ខណ្ឌ/ក្រុង..." required></textarea>
                            </div>
                        </div>

                        <h4 class="fw-bold mb-4 mt-5">វិធីសាស្ត្របង់ប្រាក់</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="payment-option active w-100" id="aba_opt" style="cursor: pointer;">
                                    <input type="radio" name="payment_method" value="aba" checked
                                        onchange="togglePayment('aba_opt')" class="d-none">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white p-2 rounded-3 shadow-sm">
                                            <img src="https://www.ababank.com/typo3conf/ext/aba/Resources/Public/images/aba-logo.png"
                                                width="35">
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">KHQR Pay</h6>
                                            <small class="text-muted">ទូទាត់រហ័ស</small>
                                        </div>
                                        <i class="fas fa-check-circle ms-auto text-primary"></i>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="payment-option w-100" id="cod_opt" style="cursor: pointer;">
                                    <input type="radio" name="payment_method" value="cod"
                                        onchange="togglePayment('cod_opt')" class="d-none">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white p-2 rounded-3 shadow-sm text-primary">
                                            <i class="fas fa-truck fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">បង់លុយផ្ទាល់</h6>
                                            <small class="text-muted">ពេលទំនិញដល់</small>
                                        </div>
                                        <i class="fas fa-check-circle ms-auto text-light check-icon"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 summary-section">
                        <h5 class="fw-bold mb-4 d-flex justify-content-between">
                            <span>សេចក្តីសង្ខេប</span>
                            <span
                                class="badge bg-primary-subtle text-primary rounded-pill fw-normal small px-3"><?php echo count($items_data); ?>
                                មុខ</span>
                            </h5>
                        
                        <div class="order-items pe-2" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($items_data as $item): ?>
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <img src="uploads/<?php echo $item['image_main']; ?>"
                                        class="product-img">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small mb-1">
                                            <?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="text-muted extra-small">ចំនួន:
                                            <?php echo $item['quantity']; ?></div>
                                        </div>
                                    <div class="fw-bold text-dark">
                                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                    </div>
                            <?php endforeach; ?>
                            </div>

                        <div class="mt-4 pt-4 border-top">
                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>តម្លៃសរុបទំនិញ</span>
                                <span>$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>សេវាដឹកជញ្ជូន</span>
                                <span class="badge-free">ឥតគិតថ្លៃ</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                                <span class="h5 fw-bold">តម្លៃសរុបរួម</span>
                                <span
                                    class="h3 fw-bold text-primary mb-0">$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-confirm w-100 text-white fw-bold mt-4 border-0">
                            បញ្ជាក់ការបញ្ជាទិញ <i class="fas fa-shield-check ms-2"></i>
                        </button>

                        <div class="text-center mt-4 opacity-75">
                            <small class="text-muted"><i
                                    class="fas fa-lock me-2"></i>ប្រព័ន្ធទូទាត់ប្រកបដោយសុវត្ថិភាពខ្ពស់</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePayment(id) {
            // ប្តូរ UI សំរាប់ Payment Option
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('active');
                const icon = opt.querySelector('.fa-check-circle');
                icon.classList.replace('text-primary', 'text-light');
            });

            const selected = document.getElementById(id);
            selected.classList.add('active');
            selected.querySelector('.fa-check-circle').classList.replace('text-light', 'text-primary');
        }
    </script>

</body>

</html>