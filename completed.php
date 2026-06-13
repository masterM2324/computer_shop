<?php
session_start();
require_once './db/db.php';

// ១. ទទួលលេខកូដបញ្ជាទិញពី URL
$order_id = $_GET['order_id'] ?? 0;

if ($order_id == 0) {
    header("Location: index.php");
    exit;
}

// ២. ទាញយកព័ត៌មានបញ្ជាទិញដំបូងដើម្បីពិនិត្យមើលថាមាន Order ហ្នឹងពិតមែនឬអត់
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit;
}

// បង្កើត API តូចមួយនៅក្នុងទំព័រនេះតែម្តង សម្រាប់ឱ្យ JavaScript ហៅមកសួររកស្ថានភាព Order (Check Status)
if (isset($_GET['action']) && $_GET['action'] == 'check_status') {
    header('Content-Type: application/json');
    
    // ទាញទិន្នន័យស្ថានភាពចុងក្រោយបង្អស់ពី Database
    $stmt_check = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt_check->bind_param("i", $order_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result()->fetch_assoc();
    
    $status = $res_check['status'] ?? 'pending';
    
    // ប្រសិនបើ Admin ចុច Accept (status ទៅជា paid) ហើយ គឺយើងធ្វើការសម្អាត Cart តែម្តង
    if (strtolower($status) == 'paid') {
        $user_id = $_SESSION['id'] ?? 0; 
        if ($user_id > 0) {
            $sql_update_cart = "UPDATE cart SET status = 'removed' WHERE cart_id = ? AND status = 'active'";
            $stmt_cart = $conn->prepare($sql_update_cart);
            $stmt_cart->bind_param("i", $user_id);
            $stmt_cart->execute();
        }
        
        if (isset($_SESSION['cart'])) unset($_SESSION['cart']);
        if (isset($_SESSION['total_items'])) $_SESSION['total_items'] = 0;
    }
    
    echo json_encode(['status' => strtolower($status)]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ស្ថានភាពការបញ្ជាទិញ - Order Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Kantumruy Pro', sans-serif; }
        .success-card { 
            max-width: 500px; 
            margin: 80px auto; 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: #fff;
            padding: 40px 20px;
            text-align: center;
        }
        .check-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .order-details {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        .btn-home {
            background-color: #005a9c;
            color: white;
            border-radius: 10px;
            padding: 12px 30px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-home:hover {
            background-color: #003e6b;
            color: white;
        }
        
        /* គំនូរជីវចល Loading Spinner */
        .spinner-container {
            padding: 30px 0;
        }
        .custom-spinner {
            width: 3.5rem;
            height: 3.5rem;
            color: #005a9c;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="success-card">
        
        <div id="waiting-section">
            <div class="spinner-container">
                <div class="spinner-border custom-spinner mb-3" role="status"></div>
            </div>
            <h3 class="fw-bold text-dark">កំពុងរង់ចាំការបញ្ជាក់...</h3>
            <p class="text-muted px-3">ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់ការទូទាត់ប្រាក់របស់អ្នក។ សូមកុំចាកចេញ ឬ Refresh ទំព័រនេះអី!</p>
            <div class="p-3 bg-light border rounded-3 text-start mx-3">
                <small class="text-secondary d-block">លេខកូដបញ្ជាទិញ៖ <strong>#<?php echo $order['id']; ?></strong></small>
                <small class="text-secondary d-block">ទឹកប្រាក់ត្រូវទូទាត់៖ <strong class="text-danger">$<?php echo number_format($order['total_amount'], 2); ?></strong></small>
            </div>
        </div>

        <div id="success-section" class="d-none">
            <i class="fa-solid fa-circle-check check-icon"></i>
            <h2 class="fw-bold text-dark">ការបង់ប្រាក់ជោគជ័យ!</h2>
            <p class="text-muted">សូមអរគុណសម្រាប់ការបញ្ជាទិញរបស់លោកអ្នក។</p>

            <div class="order-details">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">លេខកូដបញ្ជាទិញ:</span>
                    <span class="fw-bold">#<?php echo $order['id']; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">ចំនួនទឹកប្រាក់:</span>
                    <span class="fw-bold text-primary">$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">ស្ថានភាព:</span>
                    <span class="badge bg-success">បានបង់ប្រាក់រួច</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">កាលបរិច្ឆេទ:</span>
                    <span><?php echo date('d-M-Y H:i A', strtotime($order['order_date'] ?? 'now')); ?></span>
                </div>
            </div>

            <p class="small text-muted mb-4">យើងនឹងចាប់ផ្តើមរៀបចំទំនិញរបស់អ្នកដើម្បីដឹកជញ្ជូនក្នុងពេលឆាប់ៗនេះ។</p>

            <div class="d-grid gap-2">
                <a href="index.php" class="btn-home fw-bold">
                    <i class="fa-solid fa-house me-2"></i> ត្រឡប់ទៅទំព័រដើម
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm border-0">
                    <i class="fa-solid fa-print me-1"></i> បោះពុម្ពបង្កាន់ដៃ
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // ចាប់យក ID នៃ Order ពី PHP
    const orderId = <?php echo $order_id; ?>;
    
    // មុខងារហៅទៅសួរ Database រាល់ ៣ វិនាទីម្តង
    const checkInterval = setInterval(() => {
        fetch(`?order_id=${orderId}&action=check_status`)
            .then(response => response.json())
            .then(data => {
                // ប្រសិនបើស្ថានភាពទៅជា 'paid' (Admin ចុច Accept ហើយ)
                if (data.status === 'paid') {
                    // ១. ឈប់ហៅទៅកាន់ Database ទៀត
                    clearInterval(checkInterval);
                    
                    // ២. រង់ចាំរយៈពេល ២ វិនាទី (2000 ms) សម្រាប់ការបង្ហាញ Animation រួចទើបបង្ហាញផ្ទាំងជោគជ័យ
                    setTimeout(() => {
                        document.getElementById('waiting-section').classList.add('d-none');
                        document.getElementById('success-section').classList.remove('d-none');
                    }, 2000);
                }
            })
            .catch(error => console.error("Error checking status:", error));
    }, 3000); // ៣ វិនាទី ទៅសួរ Database ម្តង
</script>

</body>
</html>