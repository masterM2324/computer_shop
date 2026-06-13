<?php
// invoice.php
session_start();
require_once './db/db.php'; 

// Check Authentication
if (!isset($_SESSION['id']) || !isset($_SESSION['order_success'])) {
    // បើមិនមានការ Login ឬមិនមែនទើបតែ Checkout ទេ បញ្ជូនទៅកាន់ Cart
    header("Location: view_cart.php");
    exit();
}

$user_id = $_SESSION['id'];
$total_price = 0;

// 1. Fetch Order Details (Joining orders, products, and user data)
// ទាញយកទិន្នន័យការកម្ម៉ង់ដែលទើបតែបញ្ចូល (មាន status = 'Pending')
$sql = "SELECT 
            o.id, 
            p.name, 
            p.price, 
            o.quantity, 
            o.order_date,
            u.username,
            u.email
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id
        WHERE o.user_id = ? AND o.status = 'Pending'
        ORDER BY o.order_date DESC"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Clear the success flag after fetching data
unset($_SESSION['order_success']);

?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>វិក័យប័ត្របញ្ជាក់ការកម្ម៉ង់ - Invoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Siemreap:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* CSS បានកែសម្រួលឱ្យកាន់តែស្អាត */
        body { 
            font-family: 'Khmer OS Siemreap', 'Arial', sans-serif; 
            background-color: #f0f2f5; 
            margin: 0; 
            padding: 20px; 
            font-size: 14px;
        }
        .invoice-box { 
            max-width: 800px; 
            margin: 30px auto; 
            padding: 40px; 
            border: 1px solid #ddd; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08); 
            line-height: 1.6; 
            color: #333; 
            background: #fff; 
            border-radius: 8px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            color: #007bff;
            font-size: 28px;
            margin: 0;
        }
        .shop-info p {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        .details-table, .item-table { 
            width: 100%; 
            line-height: inherit; 
            text-align: left; 
            margin-bottom: 25px; 
            border-collapse: collapse;
        }
        .details-table td { 
            padding: 8px 0; 
            vertical-align: top;
        }
        .item-table { 
            border: 1px solid #ddd; 
        }
        .item-table th, .item-table td { 
            padding: 12px; 
            border: 1px solid #ddd; 
        }
        .item-table th { 
            background-color: #f2f2f2; 
            color: #333;
            text-align: center; 
        }
        .total-row td {
            padding: 12px;
            text-align: right;
            border-top: 3px solid #007bff; /* បន្ទាត់ខណ្ឌពណ៌ខៀវ */
            font-size: 18px;
            font-weight: 600;
        }
        .grand-total {
            background-color: #e6f3ff; /* ផ្ទៃខាងក្រោយខៀវស្រាល */
            color: #007bff;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .footer-links { margin-top: 40px; text-align: center; }
        .footer-links a { 
            padding: 12px 25px; 
            background-color: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .footer-links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    
    <div class="invoice-header">
        <div class="shop-info">
            <p style="font-size: 20px; color: #333;">**CShop Electronics**</p>
            <small>888, ផ្លូវ ឧកញ៉ា ទេពផន, ភ្នំពេញ</small>
        </div>
        <h1>វិក័យប័ត្រ (Invoice)</h1>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php $first_row = $result->fetch_assoc(); // Fetch the first row to get user data ?>
        <?php $result->data_seek(0); // Reset pointer to start ?>

        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    **ព័ត៌មានអតិថិជន (Customer Details):**<br>
                    **ឈ្មោះ (Name):** <?php echo htmlspecialchars($first_row['username']); ?><br>
                    **អ៊ីមែល (Email):** <?php echo htmlspecialchars($first_row['email']); ?>
                </td>
                <td class="right" style="width: 50%;">
                    **លេខវិក័យប័ត្រ (Invoice #):** <span style="font-weight: bold; color: #d9534f;">ORDER-<?php echo str_pad($user_id, 5, '0', STR_PAD_LEFT) . time(); ?></span><br>
                    **កាលបរិច្ឆេទ (Date):** <?php echo date('Y-m-d H:i:s'); ?>
                </td>
            </tr>
        </table>
        
        <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-top: 25px;">បញ្ជីទំនិញ (Item List)</h3>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ល.រ. (No.)</th>
                    <th style="width: 45%;">ឈ្មោះផលិតផល (Product)</th>
                    <th style="width: 15%;">តម្លៃរាយ (Price)</th>
                    <th style="width: 15%;">បរិមាណ (Qty)</th>
                    <th style="width: 20%;">តម្លៃសរុប (Subtotal)</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; while ($row = $result->fetch_assoc()): 
                    $subtotal = $row['price'] * $row['quantity'];
                    $total_price += $subtotal;
                ?>
                <tr>
                    <td class="center"><?php echo $counter++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="right">$<?php echo number_format($row['price'], 2); ?></td>
                    <td class="center"><?php echo (int)$row['quantity']; ?></td>
                    <td class="right">$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <table style="width: 100%;" class="total-row">
            <tr>
                <td style="width: 80%;" class="right">**សរុបរួម (Grand Total):**</td>
                <td style="width: 20%;" class="right grand-total">$<?php echo number_format($total_price, 2); ?></td>
            </tr>
        </table>

        <div class="footer-links">
            <p style="margin-bottom: 20px; font-style: italic; color: #5cb85c; font-size: 16px;">សូមអរគុណសម្រាប់ការកម្ម៉ង់របស់អ្នក។</p>
            <a href="index.php">← ត្រឡប់ទៅទំព័រដើម</a>
        </div>

    <?php else: ?>
        <p class="center" style="padding: 20px; background-color: #fcf8e3; border: 1px solid #faebcc;">មិនមានទិន្នន័យបញ្ជាក់ការកម្ម៉ង់សម្រាប់បង្ហាញទេ។</p>
    <?php endif; ?>

</div>

</body>
</html>
<?php $stmt->close(); $conn->close(); ?>