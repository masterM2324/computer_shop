<?php
session_start();
require_once './db/db.php'; // កុំភ្លេច require db ផង

// ១. ទាញ ID ពី URL (ឧទាហរណ៍៖ order_success.php?id=25)
$order_id = $_GET['id'] ?? 0;

// ២. ទាញទិន្នន័យតម្លៃសរុបពី Database ផ្ទាល់
$sql = "SELECT total_amount FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
// ៣. កំណត់តម្លៃសម្រាប់បង្ហាញ
$final_total = $order['total_amount'] ?? 0;
?>


<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ការបញ្ជាទិញជោគជ័យ | Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Khmer+OS+Siemreap&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --primary: #6366f1;
        --primary-soft: #f5f7ff;
        --slate-900: #1e293b;
        --slate-500: #64748b;
    }

    body {
        background-color: #f8fafc;
        font-family: 'Plus Jakarta Sans', 'Khmer OS Siemreap', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    /* Progress Steps ឱ្យដូចទំព័រមុនៗ */
    .step-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
        width: 100%;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--slate-500);
    }

    .step-item.active {
        color: var(--primary);
    }

    .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
    }

    .active .step-num {
        background: var(--primary);
    }

    .step-check {
        background: #22c55e !important;
    }

    .success-card {
        background: white;
        padding: 50px 40px;
        border-radius: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        text-align: center;
        max-width: 500px;
        margin: 0 auto;
    }

    .icon-box {
        width: 90px;
        height: 90px;
        background: #dcfce7;
        color: #22c55e;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 25px;
        font-size: 40px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 15px rgba(34, 197, 94, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }

    .order-id-badge {
        background: var(--primary-soft);
        color: var(--primary);
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-block;
    }

    .btn-home {
        background: var(--primary);
        color: white;
        border-radius: 16px;
        padding: 16px;
        font-weight: 700;
        text-decoration: none;
        display: block;
        transition: 0.3s;
        border: none;
        width: 100%;
    }

    .btn-home:hover {
        background: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
        color: white;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="step-container">
            <div class="step-item active"><span class="step-num step-check"><i class="fas fa-check"></i></span>
                កន្ត្រកទំនិញ</div>
            <div style="width: 40px; height: 2px; background: #22c55e;"></div>
            <div class="step-item active"><span class="step-num step-check"><i class="fas fa-check"></i></span>
                ការទូទាត់</div>
            <div style="width: 40px; height: 2px; background: #22c55e;"></div>
            <div class="step-item active"><span class="step-num">3</span> ជោគជ័យ</div>
        </div>

        <div class="success-card">
            <div class="icon-box">
                <i class="fas fa-check"></i>
            </div>

            <h2 class="fw-bold mb-2">ការបញ្ជាទិញជោគជ័យ!</h2>
            <p class="text-muted small">
                សូមអរគុណចំពោះការជាវទំនិញពីហាងយើងខ្ញុំ។<br>ការបញ្ជាទិញរបស់អ្នកកំពុងត្រូវបានរៀបចំសម្រាប់ដឹកជញ្ជូន។</p>

            <div class="my-4">
                <span class="order-id-badge">លេខសម្គាល់៖ #ORD-<?php echo $order_id; ?></span>
            </div>

            <div class="p-3 bg-light rounded-4 mb-4 border border-dashed">
                <div class="row align-items-center">
                    <div class="col-6 text-start">
                        <span class="text-muted small d-block">ស្ថានភាព</span>
                        <span class="fw-bold text-success"><i class="fas fa-circle-check me-1"></i> បានបង់ប្រាក់</span>
                    </div>
                    <div class="col-6 text-end border-start">
                        <span class="text-muted small d-block">សរុបប្រាក់</span>
                        <span class="h4 fw-bold text-primary mb-0">$<?php echo number_format($final_total, 2); ?></span>
                    </div>
                </div>
            </div>

            <a href="index.php" class="btn-home">
                ត្រឡប់ទៅទំព័រដើម <i class="fas fa-home ms-2"></i>
            </a>

            <p class="mt-4 small text-muted">
                <i class="fas fa-info-circle me-1"></i> យើងបានផ្ញើវិក្កយបត្រទៅកាន់អ៊ីមែល/លេខទូរស័ព្ទរបស់អ្នករួចរាល់ហើយ។
            </p>
        </div>
    </div>

</body>

</html>