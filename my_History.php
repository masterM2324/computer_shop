<?php
session_start();
require_once './db/db.php';

// ១. បញ្ជាក់ថា User បាន Login រួចរាល់
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$current_user_id = $_SESSION['id'];
$current_user_role = $_SESSION['role'] ?? 'user';

// បង្កើតលក្ខខណ្ឌសម្រាប់ Link ត្រឡប់ក្រោយ (Back Link) ផ្អែកលើ Role
$back_url = ($current_user_role === 'admin') ? 'admin_dashboard.php' : 'index.php';

/**
 * ២. រៀបចំ SQL Query ដោយប្រើ JOIN ជាមួយតារាង users ដើម្បីទាញយក username
 */
if ($current_user_role === 'admin') {
    // សម្រាប់ Admin: ទាញយកការបញ្ជាទិញទាំងអស់ និង username របស់អ្នកទិញម្នាក់ៗ
    $sql = "SELECT 
                o.*, 
                u.username, 
                p.name AS product_name, p.image_main,
                oi.quantity, oi.price AS unit_price
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    // សម្រាប់ User: ទាញយកតែរបស់ខ្លួនឯង និងបង្ហាញ username ខ្លួនឯង
    $sql = "SELECT 
                o.*, 
                u.username, 
                p.name AS product_name, p.image_main,
                oi.quantity, oi.price AS unit_price
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE o.user_id = ? 
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
}

$stmt->execute();
$result = $stmt->get_result();

// ៣. រៀបចំទិន្នន័យ (Grouping by Order ID)
$orders = [];
while ($row = $result->fetch_assoc()) {
    $oid = $row['id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'info' => $row,
            'items' => []
        ];
    }
    if ($row['product_name']) {
        $orders[$oid]['items'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>ប្រវត្តិការបញ្ជាទិញ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Siemreap&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Khmer OS Siemreap', sans-serif; background-color: #f0f2f5; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; display: inline-block; }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        .table-img { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; }
        .username-tag { color: #0d6efd; font-weight: bold; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold mb-0">
            <i class="fas fa-user-clock me-2"></i>
            <?php echo ($current_user_role === 'admin') ? "ការបញ្ជាទិញទាំងអស់ក្នុងប្រព័ន្ធ" : "ប្រវត្តិបញ្ជាទិញរបស់ខ្ញុំ"; ?>
        </h3>
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>ត្រឡប់ក្រោយ
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>ផលិតផល</th>
                    <th>គណនី (Username)</th>
                    <th>សរុប</th>
                    <th>ស្ថានភាព</th>
                    <th>កាលបរិច្ឆេទ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="text-center py-5">មិនទាន់មានទិន្នន័យបញ្ជាទិញទេ។</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $oid => $data): ?>
                        <tr>
                            <td class="ps-3 fw-bold">#<?php echo $oid; ?></td>
                            <td>
                                <?php foreach ($data['items'] as $item): ?>
                                    <div class="d-flex align-items-center mb-1">
                                        <img src="img/<?php echo $item['image_main']; ?>" class="table-img me-2">
                                        <small><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)</small>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <div class="username-tag">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <?php echo htmlspecialchars($data['info']['username'] ?? 'មិនស្គាល់'); ?>
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">
                                    ឈ្មោះពិត: <?php echo htmlspecialchars($data['info']['full_name'] ?? 'មិនទាន់បំពេញ'); ?>
                                </small>
                            </td>
                            <td class="text-danger fw-bold">$<?php echo number_format($data['info']['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($data['info']['status']); ?>">
                                    <?php echo strtoupper($data['info']['status']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d-m-Y H:i', strtotime($data['info']['order_date'])); ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>