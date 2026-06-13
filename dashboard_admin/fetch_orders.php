<?php
// ចាប់ផ្តើម Session ដើម្បីពិនិត្យសិទ្ធិសុវត្ថិភាព
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ពិនិត្យមើលសិទ្ធិ Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE || $_SESSION['role'] !== 'admin') {
    exit("Access Denied");
}

/// ដាក់នៅផ្នែកខាងលើ បន្ទាប់ពី require_once '../db/db.php';
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    // បង្កើត SQL លុបទិន្នន័យតាម ID
    $sql_delete = "DELETE FROM `orders` WHERE id = $order_id";
    
    if ($conn->query($sql_delete)) {
        // លុបជោគជ័យ រួច Redirect ដោយប្រើ JavaScript ជំនួស PHP Header
        echo "<script>window.location.href = '?tab=orders&msg=deleted';</script>";
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// ទាញយកទិន្នន័យការបញ្ជាទិញសារជាថ្មី
$sql_orders = "SELECT * FROM `orders` ORDER BY order_date DESC";
$orders_result = $conn->query($sql_orders);

// បង្ហាញទិន្នន័យតារាង (Tbody Content)
if ($orders_result && $orders_result->num_rows > 0): 
    while($row = $orders_result->fetch_assoc()): 
        $status = strtolower($row['status']);
        $status_class = "status-pending";
        if($status == 'paid') $status_class = "status-paid";
        if($status == 'cancelled') $status_class = "status-cancelled";
    ?>
    <tr id="order-row-<?php echo $row['id']; ?>">
        <td class="text-primary fw-bold">Cshop-<?php echo $row['id']; ?></td>
        <td><i class="fas fa-user-circle me-1 text-secondary"></i> <?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['phone']); ?></td>
        <td style="max-width: 180px;" class="text-truncate" title="<?php echo htmlspecialchars($row['address']); ?>"><?php echo htmlspecialchars($row['address']); ?></td>
        <td class="fw-bold text-success">$<?php echo number_format($row['total_amount'], 2); ?></td>
        <td><small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($row['order_date'])); ?></small></td>
        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo ucfirst($row['status']); ?></span></td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
                <?php if($status != 'paid' && $status != 'cancelled'): ?>
                    <a href="?tab=orders&update_status=paid&order_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm text-white" onclick="return confirm('បញ្ជាក់ការបង់ប្រាក់?')">
                        <i class="fas fa-check"></i> Accept
                    </a>
                <?php endif; ?>
                
                <?php if($status != 'cancelled'): ?>
                    <a href="?tab=orders&update_status=cancelled&order_id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('តើអ្នកចង់បោះបង់ការបញ្ជាទិញនេះ?')">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php endif; ?>

                <a href="?tab=orders&action=delete&order_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm text-white" onclick="return confirm('តើអ្នកពិតជាចង់លុបទិន្នន័យបញ្ជាទិញនេះមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយវិញបានឡើយ!')">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </div>
        </td>
    </tr>
    <?php 
    endwhile; 
else: 
?>
    <tr>
        <td colspan="8" class="text-center py-5 text-muted">មិនទាន់មានទិន្នន័យបញ្ជាទិញនៅឡើយទេ។</td>
    </tr>
<?php 
endif; 
?>