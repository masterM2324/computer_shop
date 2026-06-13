<?php
session_start();
// ពិនិត្យសិទ្ធិ Admin (ដូចក្នុង dashboard របស់អ្នក)
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ./login.php");
    exit;
}

require_once '../db/db.php';

// --- ផ្នែកដោះស្រាយការ Update Status ---
if (isset($_GET['update_status']) && isset($_GET['order_id'])) {
    $new_status = $_GET['update_status'];
    $order_id = $_GET['order_id'];

    $update_sql = "UPDATE `orders` SET `status` = ? WHERE `id` = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: report.php?msg=updated"); 
        exit();
    }
}

// ទាញយកទិន្នន័យ
$sql = "SELECT * FROM `orders` ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>របាយការណ៍ការបញ្ជាទិញ - CShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kantumruy Pro', sans-serif;
            background-color: #f8f9fa;
        }
        .report-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .table thead {
            background-color: #34495e;
            color: white;
        }
        .status-paid { background-color: #d4edda; color: #155724; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; }
        .status-pending { background-color: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; }
        .btn-back {
            border-radius: 10px;
            transition: 0.3s;
        }
        .action-btns .btn {
            border-radius: 8px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>របាយការណ៍ការបញ្ជាទិញ</h2>
            <p class="text-muted">គ្រប់គ្រង និងពិនិត្យមើលរាល់ប្រតិបត្តិការលក់</p>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-back">
            <i class="fas fa-arrow-left me-2"></i>ត្រឡប់ទៅ Dashboard
        </a>
    </div>

    <div class="card report-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">លេខកូដ</th>
                            <th>អតិថិជន</th>
                            <th>លេខទូរស័ព្ទ</th>
                            <th>អាសយដ្ឋាន</th>
                            <th>សរុបទឹកប្រាក់</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $status = strtolower($row['status']);
                                $status_class = "status-pending";
                                if($status == 'paid') $status_class = "status-paid";
                                if($status == 'cancelled') $status_class = "status-cancelled";
                            ?>
                            <tr>
                                <td class="ps-4 text-primary fw-bold">#ORD-<?php echo $row['id']; ?></td>
                                <td><i class="fas fa-user-circle me-1"></i> <?php echo $row['full_name']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td style="max-width: 200px;" class="text-truncate"><?php echo $row['address']; ?></td>
                                <td class="fw-bold text-success">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($row['order_date'])); ?></small></td>
                                <td><span class="<?php echo $status_class; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td class="text-center action-btns">
                                    <?php if($status != 'paid'): ?>
                                        <a href="?update_status=paid&order_id=<?php echo $row['id']; ?>" 
                                           class="btn btn-success btn-sm me-1" 
                                           onclick="return confirm('បញ្ជាក់ការបង់ប្រាក់?')">
                                           <i class="fas fa-check"></i> Accept
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if($status != 'cancelled'): ?>
                                        <a href="?update_status=cancelled&order_id=<?php echo $row['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('តើអ្នកចង់លុបចោលការបញ្ជាទិញនេះ?')">
                                           <i class="fas fa-times"></i> Cancel
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">មិនទាន់មានទិន្នន័យបញ្ជាទិញនៅឡើយទេ។</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>