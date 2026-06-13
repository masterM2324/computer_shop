<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once './db/db.php'; 

// ១. កំណត់ព័ត៌មាន API
$api_key = "ak_d6e8380df390f97ae129d3d8d4f05240e2822c88add1acb9"; 
$url = 'https://khpay.site/api/v1/qr/generate'; 

// ២. ទទួល និងផ្ទៀងផ្ទាត់លេខកូដបញ្ជាទិញ (Order ID)
$order_id = 0;
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']); 
} elseif (isset($_SESSION['last_order_id'])) {
    $order_id = intval($_SESSION['last_order_id']); 
}

if ($order_id <= 0) {
    header("Location: index.php"); 
    exit;
}

// ៣. ទាញយកទិន្នន័យពី Database ដើម្បីផ្ទៀងផ្ទាត់
$stmt = $conn->prepare("SELECT total_amount, status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id); 
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close(); 

if (!$order) {
    die("កំហុស៖ រកមិនឃើញទិន្នន័យបញ្ជាទិញឡើយនៅក្នុងប្រព័ន្ធ។"); 
}

if ($order['status'] === 'paid') {
    header("Location: completed.php?order_id=$order_id"); 
    exit;
}

$final_amount = (float)$order['total_amount']; 

// ៤. ហៅទៅកាន់ API តាមរយៈ cURL ដើម្បីបង្កើត QR Code
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'amount'   => number_format($final_amount, 2, '.', ''), // ទម្រង់ "1.00" ឬ "599.00"
        'note'     => "Order #$order_id",
        'currency' => 'USD' // បញ្ជាក់ប្រភេទលុយ (សំខាន់សម្រាប់ API ស្រុកខ្មែរ)
    ]),
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false, // រំលងការពិនិត្យ SSL បើតេស្តនៅលើ Localhost (XAMPP)
]);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

$api_data = json_decode($response, true);

// បង្កើត Variable សម្រាប់ត្រួតពិនិត្យស្ថានភាព QR
$qr_image_url = null;
$error_message = null;
$raw_api_response = $response; // រក្សាទុក JSON ដើមសម្រាប់ Debug

if ($curl_error) {
    $error_message = "cURL Error: " . $curl_error;
} elseif (isset($api_data['success']) && $api_data['success'] == true && !empty($api_data['data']['qr_string'])) {
    $qr_string = $api_data['data']['qr_string']; 
    $qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_string); 
} else {
    // ចាប់យកសារ Error ពី API បើមាន ឬដាក់សារលំនាំដើម
    $error_message = isset($api_data['message']) ? $api_data['message'] : "API មិនអាចបង្កើត QR បានទេ។ ប្រហែលមកពី API Key មិនត្រឹមត្រូវ ឬគណនីធនាគារមិនទាន់បានភ្ជាប់។";
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>បង់ប្រាក់ - ABA PAY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Kantumruy Pro', sans-serif; }
        .payment-card { max-width: 400px; margin: 50px auto; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: #fff; }
        .aba-header { background: #005a9c; color: white; padding: 25px; border-radius: 20px 20px 0 0; text-align: center; }
        .qr-section { padding: 30px; text-align: center; }
        .qr-img { width: 100%; max-width: 230px; border: 8px solid #f1f1f1; border-radius: 15px; }
        .timer-box { font-weight: bold; color: #dc3545; font-size: 1.1rem; margin-bottom: 10px; }
        .alert-custom { font-size: 0.85rem; padding: 15px; border-radius: 10px; text-align: left; }
        .debug-box { background: #212529; color: #0dfd0d; font-family: monospace; font-size: 0.75rem; padding: 10px; border-radius: 5px; text-align: left; max-height: 150px; overflow-y: auto; }
    </style>
</head>
<body>

<div class="card payment-card">
    <div class="aba-header">
        <h5 class="mb-1">ABA PAY</h5>
        <div class="display-6 fw-bold mb-1">$<?php echo number_format($final_amount, 2); ?></div>
        <p class="small mb-0 opacity-75">Order ID: #<?php echo htmlspecialchars($order_id); ?></p>
    </div>

    <div class="qr-section">
        <?php if ($qr_image_url): ?>
            <div id="timer-display" class="timer-box">05:00</div>
            <img src="<?php echo htmlspecialchars($qr_image_url); ?>" class="qr-img mb-3" alt="ABA QR">
            <h6 class="fw-bold">ស្កេនដើម្បីបង់ប្រាក់</h6>
            
            <div id="status-area" class="mt-3">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                <span class="text-muted small">កំពុងរង់ចាំការបង់ប្រាក់...</span>
            </div>
        <?php else: ?>
            <div class="alert alert-danger alert-custom mb-3">
                <h6 class="fw-bold mb-1">⚠️ មិនអាចបង្កើត QR Code បានទេ</h6>
                <p class="mb-2 text-secondary"><?php echo htmlspecialchars($error_message); ?></p>
                
                <div class="fw-bold small mb-1 text-dark">API Raw Response:</div>
                <div class="debug-box">
                    <?php echo htmlspecialchars($raw_api_response ? $raw_api_response : 'No response from API server.'); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <hr>
        <a href="index.php" class="btn btn-link btn-sm text-decoration-none text-muted">បោះបង់</a>
    </div>
</div>

<script>
<?php if ($qr_image_url): ?>
let timeLeft = 5 * 60; 
const timerDisplay = document.getElementById('timer-display');
const orderId = <?php echo intval($order_id); ?>;

const timerInterval = setInterval(() => {
    let mins = Math.floor(timeLeft / 60);
    let secs = timeLeft % 60;
    timerDisplay.innerText = `${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;

    if (--timeLeft < 0) {
        clearInterval(timerInterval);
        clearInterval(checkStatusInterval);
        document.getElementById('status-area').innerHTML = "<b class='text-danger'>QR Code ផុតកំណត់!</b>";
        timerDisplay.innerText = "00:00";
    }
}, 1000);

const checkStatusInterval = setInterval(() => {
    fetch(`check_status_ajax.php?order_id=${orderId}`)
    .then(res => res.json())
    .then(data => {
        if(data.status === 'paid') {
            clearInterval(timerInterval);
            clearInterval(checkStatusInterval);
            window.location.href = `completed.php?order_id=${orderId}`; 
        }
    })
    .catch(err => console.error("Error checking status:", err));
}, 4000); 
<?php endif; ?>
</script>
</body>
</html>