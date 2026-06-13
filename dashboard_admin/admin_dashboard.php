<?php
// ១. ចាប់ផ្តើម Session និងពិនិត្យមើលសិទ្ធិ (អនុញ្ញាតតែ Admin ប៉ុណ្ណោះ)
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE || $_SESSION['role'] !== 'admin') {
    header("Location: ./login.php");
    exit;
}

// ២. ភ្ជាប់ទៅ Database
require_once '../db/db.php';

// --- ផ្នែកដោះស្រាយការលុបសារ (Delete Message) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete_msg' && isset($_GET['msg_id'])) {
    $msg_id = intval($_GET['msg_id']);
    
    // កែសម្រួល៖ ប្តូរពី `messages` មកជា `contacts` ឱ្យត្រូវនឹង Database របស់អ្នក
    $delete_sql = "DELETE FROM `contacts` WHERE `id` = ?"; 
    
    $stmt = $conn->prepare($delete_sql);
    
    if ($stmt === false) {
        die("កំហុស SQL (Prepare Failed): " . $conn->error);
    }
    
    $stmt->bind_param("i", $msg_id);
    
    if ($stmt->execute()) {
        echo "<script>window.location.href = '?tab=messages&msg=deleted';</script>";
        exit();
    } else {
        die("កំហុសក្នុងការលុប: " . $stmt->error);
    }
}

// --- ផ្នែកដោះស្រាយការលុបអ្នកប្រើប្រាស់ (Delete User) ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if ($id == $_SESSION['id']) {
        $error = "អ្នកមិនអាចលុបគណនីផ្ទាល់ខ្លួនបានទេ។";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: ?tab=users&msg=deleted");
            exit;
        }
    }
}

// --- ផ្នែកដោះស្រាយការប្តូរ Role អ្នកប្រើប្រាស់ (Promote/Demote User) ---
if (isset($_GET['promote_id']) && isset($_GET['new_role'])) {
    $id = (int)$_GET['promote_id'];
    $role = $_GET['new_role'];
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    $stmt->execute();
    header("Location: ?tab=users&msg=updated");
    exit;
}

// --- ផ្នែកដោះស្រាយការ Update Status របស់ការបញ្ជាទិញ ---
if (isset($_GET['update_status']) && isset($_GET['order_id'])) {
    $new_status = $_GET['update_status'];
    $order_id = $_GET['order_id'];

    $update_sql = "UPDATE `orders` SET `status` = ? WHERE `id` = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: ?tab=orders&msg=updated"); 
        exit();
    }
}

// --- ទាញយកទិន្នន័យផលិតផល និងស្ថិតិស្តុក ---
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
$total_products = $result->num_rows;

$sql_low_stock = "SELECT COUNT(*) as low_count FROM products WHERE qty > 0 AND qty <= 5";
$low_result = $conn->query($sql_low_stock);
$low_stock_count = $low_result->fetch_assoc()['low_count'];

$sql_stats = "SELECT 
                COUNT(*) as total,
                SUM(price) as total_value,
                AVG(price) as avg_price,
                MIN(price) as min_price,
                MAX(price) as max_price
              FROM products";
$stats_result = $conn->query($sql_stats);
$stats = $stats_result->fetch_assoc();

// --- ទាញយកបញ្ជី User ទាំងអស់ ---
$users_result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");

// --- ទាញយកសារពីអ្នកប្រើប្រាស់ ---
$messages_result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");

// ទាញយកព័ត៌មានអ្នកប្រើប្រាស់ (Admin ដែលកំពុង Login)
$user_sql = "SELECT username, email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $_SESSION['id']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// កំណត់ Tab ដើមដែលត្រូវបង្ហាញ
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'products';
?>

<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - គ្រប់គ្រងប្រព័ន្ធ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #2ec4b6;
            --danger: #f72585;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kantumruy Pro', 'Segoe UI', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark);
            overflow-x: hidden;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1040;
            left: 0;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 0 25px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
            position: relative;
        }

        .close-sidebar-btn {
            display: none;
            position: absolute;
            right: 20px;
            top: 5px;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .nav-menu {
            list-style: none;
            padding: 0 20px;
        }

        .nav-menu .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            margin-bottom: 5px;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }

        .nav-menu .nav-link-custom:hover,
        .nav-menu .nav-link-custom.active {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
            transition: var(--transition);
            min-width: 0; /* ការពារកុំឲ្យធ្លាយ layout ពេលមានតារាងវែង */
        }

        /* Top Mobile Bar */
        .mobile-header {
            display: none;
            background: white;
            padding: 15px 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            border-radius: var(--border-radius);
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1030;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-left: 5px solid var(--primary);
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }

        /* Table & Sections */
        .content-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }
        .status-in_stock { background: #e0f7fa; color: #00838f; }
        .status-out_stock { background: #ffe0b2; color: #e65100; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .role-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .sidebar {
                left: -260px; /* លាក់ទៅខាងឆ្វេងលើ Mobile */
            }
            .sidebar.show {
                left: 0; /* បង្ហាញមកវិញពេលចុច Toggle */
            }
            .close-sidebar-btn {
                display: block; /* បង្ហាញប៊ូតុងខ្វែងដើម្បីបិទលើ Mobile */
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .mobile-header {
                display: flex; /* បង្ហាញរបារខាងលើលើ Mobile */
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-container">
        
        <aside class="sidebar" id="sidebarMenu">
            <div class="sidebar-header">
                <h2 style="font-size: 20px; font-weight: bold; margin: 0;">
                    <i class="fas fa-desktop" style="font-size: 18px;"></i> 
                    <span>CShop Admin</span>
                </h2>
                <button class="close-sidebar-btn" id="closeSidebar"><i class="fas fa-times"></i></button>

                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['username'] ?? 'A', 0, 1)); ?></div>
                    <div class="user-details">
                        <h6 class="m-0"><?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?></h6>
                        <small class="opacity-75">Administrator</small>
                    </div>
                </div>
            </div>
            <ul class="nav-menu nav tabs" id="adminTabs" role="tablist">
                <li>
                    <button class="nav-link-custom <?php echo $active_tab == 'products' ? 'active' : ''; ?>" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-pane" type="button" role="tab">
                        <i class="fas fa-box"></i> <span>គ្រប់គ្រងផលិតផល</span>
                    </button>
                </li>
                <li>
                    <button class="nav-link-custom <?php echo $active_tab == 'orders' ? 'active' : ''; ?>" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders-pane" type="button" role="tab">
                        <i class="fas fa-file-invoice-dollar"></i> <span>របាយការណ៍បញ្ជាទិញ</span>
                    </button>
                </li>
                <li>
                    <button class="nav-link-custom <?php echo $active_tab == 'users' ? 'active' : ''; ?>" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" type="button" role="tab">
                        <i class="fas fa-users"></i> <span>គ្រប់គ្រងអ្នកប្រើប្រាស់</span>
                    </button>
                </li>
                <li>
                    <button class="nav-link-custom <?php echo $active_tab == 'messages' ? 'active' : ''; ?>" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-pane" type="button" role="tab">
                        <i class="fas fa-envelope"></i> <span>សារពីអ្នកប្រើប្រាស់</span>
                    </button>
                </li>
                <li><a href="../index.php" class="nav-link-custom"><i class="fas fa-external-link-alt"></i> <span>មើលវេបសាយ</span></a></li>
                <li><a href="../logout.php" class="nav-link-custom"><i class="fas fa-power-off text-danger"></i> <span>ចាកចេញ</span></a></li>
            </ul>
        </aside>

        <main class="main-content">
            
            <div class="mobile-header">
                <button class="btn btn-primary" id="toggleSidebar">
                    <i class="fas fa-bars"></i> មីនុយ
                </button>
                <h5 class="m-0 fw-bold text-primary">CShop Admin</h5>
            </div>

            <div class="tab-content" id="adminTabsContent">
                
                <div class="tab-pane fade <?php echo $active_tab == 'products' ? 'show active' : ''; ?>" id="products-pane" role="tabpanel" aria-labelledby="products-tab">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                        <div>
                            <h2 class="fw-bold m-0 fs-3">គ្រប់គ្រងផលិតផល</h2>
                            <p class="text-muted m-0 small">ស្វាគមន៍មកកាន់ផ្ទាំងគ្រប់គ្រងទិន្នន័យផលិតផល</p>
                        </div>
                        <button class="btn btn-primary px-4 shadow-sm w-100 w-sm-auto" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                            <i class="fas fa-plus me-2"></i> បន្ថែមផលិតផលថ្មី
                        </button>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <small class="text-muted">ផលិតផលសរុប</small>
                            <div class="stat-value text-primary"><?php echo $total_products; ?></div>
                        </div>
                        <div class="stat-card" style="border-left-color: var(--success);">
                            <small class="text-muted">តម្លៃសរុបក្នុងស្តុក</small>
                            <div class="stat-value text-success">$<?php echo number_format($stats['total_value'] ?? 0, 2); ?></div>
                        </div>
                        <div class="stat-card" style="border-left-color: var(--warning);">
                            <small class="text-muted">ផលិតផលជិតអស់ពីស្តុក</small>
                            <div class="stat-value text-warning"><?php echo $low_stock_count; ?></div>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>រូបភាព</th>
                                        <th>ឈ្មោះផលិតផល</th>
                                        <th>តម្លៃ</th>
                                        <th>ប្រភេទ</th>
                                        <th>ស្ថានភាព</th>
                                        <th>សកម្មភាព</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><img src="../uploads/<?php echo $row['image_main']; ?>" class="product-img" onerror="this.src='https://via.placeholder.com/50'"></td>
                                                <td>
                                                    <div class="fw-bold text-nowrap"><?php echo htmlspecialchars($row['name']); ?></div>
                                                    <small class="text-muted">ID: <?php echo $row['id']; ?></small>
                                                </td>
                                                <td class="text-primary fw-bold">$<?php echo number_format($row['price'], 2); ?></td>
                                                <td><span class="badge bg-light text-dark"><?php echo $row['category']; ?></span></td>
                                                <td>
                                                    <?php
                                                    $qty = $row['qty'] ?? 0;
                                                    if ($qty > 0) {
                                                        $badge_class = 'status-in_stock';
                                                        $label = 'មានក្នុងស្តុក (' . $qty . ')';
                                                    } else {
                                                        $badge_class = 'status-out_stock';
                                                        $label = 'អស់ពីស្តុក';
                                                    }
                                                    ?>
                                                    <span class="status-badge <?php echo $badge_class; ?>"><?php echo $label; ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-warning"><i class="fas fa-edit"></i></a>
                                                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('តើអ្នកពិតជាចង់លុបផលិតផលនេះមែនទេ?')"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">មិនទាន់មានផលិតផលឡើយទេ។</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade <?php echo $active_tab == 'orders' ? 'show active' : ''; ?>" id="orders-pane" role="tabpanel" aria-labelledby="orders-tab">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark fs-3"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>របាយការណ៍ការបញ្ជាទិញ</h2>
                        <p class="text-muted m-0 small">គ្រប់គ្រង និងពិនិត្យមើលរាល់ប្រតិបត្តិការលក់របស់អតិថិជន</p>
                    </div>

                    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated' && $active_tab == 'orders'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            បានធ្វើបច្ចុប្បន្នភាពស្ថានភាពការបញ្ជាទិញដោយជោគជ័យ!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="content-section">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>លេខកូដ</th>
                                        <th>អតិថិជន</th>
                                        <th>លេខទូរស័ព្ទ</th>
                                        <th>អាសយដ្ឋាន</th>
                                        <th>សរុបទឹកប្រាក់</th>
                                        <th>កាលបរិច្ឆេទ</th>
                                        <th>ស្ថានភាព</th>
                                        <th class="text-center">សកម្មភាព</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-table-body">
                                    <?php include 'fetch_orders.php'; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade <?php echo $active_tab == 'users' ? 'show active' : ''; ?>" id="users-pane" role="tabpanel" aria-labelledby="users-tab">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark fs-3"><i class="fas fa-users-cog me-2 text-primary"></i>គ្រប់គ្រងអ្នកប្រើប្រាស់</h2>
                        <p class="text-muted m-0 small">គ្រប់គ្រងសិទ្ធិ និងគណនីរបស់អ្នកប្រើប្រាស់ក្នុងប្រព័ន្ធ</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated' && $active_tab == 'users'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            បានប្តូរតួនាទីអ្នកប្រើប្រាស់ដោយជោគជ័យ!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted' && $active_tab == 'users'): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            បានលុបអ្នកប្រើប្រាស់ដោយជោគជ័យ!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="content-section">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>ឈ្មោះអ្នកប្រើ</th>
                                        <th>អ៊ីមែល</th>
                                        <th>តួនាទី</th>
                                        <th>ថ្ងៃបង្កើត</th>
                                        <th class="text-center">សកម្មភាព</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($user_row = $users_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user_row['id']; ?></td>
                                        <td class="fw-bold text-nowrap"><?php echo htmlspecialchars($user_row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user_row['email']); ?></td>
                                        <td>
                                            <?php if($user_row['role'] == 'admin'): ?>
                                                <span class="role-badge bg-primary text-white">Admin</span>
                                            <?php else: ?>
                                                <span class="role-badge bg-secondary text-white">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap"><?php echo date('d-M-Y', strtotime($user_row['created_at'])); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <?php if($user_row['role'] == 'user'): ?>
                                                    <a href="?tab=users&promote_id=<?php echo $user_row['id']; ?>&new_role=admin" class="btn btn-sm btn-success" onclick="return confirm('តើអ្នកចង់តម្លើង User នេះជា Admin ឬ?')"><i class="fas fa-user-shield"></i></a>
                                                <?php else: ?>
                                                    <a href="?tab=users&promote_id=<?php echo $user_row['id']; ?>&new_role=user" class="btn btn-sm btn-warning" onclick="return confirm('តើអ្នកចង់ដាក់ Admin នេះជា User ធម្មតាវិញឬ?')"><i class="fas fa-user"></i></a>
                                                <?php endif; ?>

                                                <a href="?tab=users&delete_id=<?php echo $user_row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('តើអ្នកប្រាកដថាចង់លុបអ្នកប្រើប្រាស់នេះទេ?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

               <div class="tab-pane fade <?php echo $active_tab == 'messages' ? 'show active' : ''; ?>" id="messages-pane" role="tabpanel" aria-labelledby="messages-tab">
    <div class="mb-4">
        <h2 class="fw-bold text-dark fs-3"><i class="fas fa-envelope me-2 text-primary"></i>សារពីអ្នកប្រើប្រាស់</h2>
        <p class="text-muted m-0 small">ពិនិត្យមើលរាល់មតិយោបល់ ឬសារទំនាក់ទំនងពីអតិថិជន</p>
    </div>

    <div class="content-section">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ឈ្មោះ</th>
                        <th>អ៊ីមែល</th>
                        <th>ប្រធានបទ</th>
                        <th>សារ</th>
                        <th>កាលបរិច្ឆេទ</th>
                        <th class="text-center">សកម្មភាព</th> </tr>
                </thead>
                <tbody>
                    <?php if ($messages_result->num_rows > 0): ?>
                        <?php while($msg = $messages_result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-nowrap"><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($msg['subject']); ?></span></td>
                            <td style="min-width: 200px;"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                            <td class="text-nowrap"><small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($msg['created_at'])); ?></small></td>
                            <td class="text-center">
                                <a href="?tab=messages&action=delete_msg&msg_id=<?php echo $msg['id']; ?>" 
                                   class="btn btn-danger btn-sm text-white" 
                                   onclick="return confirm('តើអ្នកពិតជាចង់លុបសារនេះមែនទេ?')">
                                    <i class="fas fa-trash-alt"></i> លុប
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">មិនទាន់មានសារទំនាក់ទំនងឡើយទេ។</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

            </div>
        </main>
    </div>

    <div class="modal fade" id="quickAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold m-0"><i class="fas fa-plus-circle text-primary me-2"></i> បន្ថែមផលិតផលថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ប្រភេទផលិតផល</label>
                                <select name="category" id="categorySelect" class="form-control" required>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Desktop">Desktop</option>
                                    <option value="Accessories">Accessories</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ឈ្មោះផលិតផល</label>
                                <input type="text" name="name" class="form-control" placeholder="ឧ. Dell XPS 15" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">តម្លៃ ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ចំនួនក្នុងស្តុក (Qty)</label>
                                <input type="number" name="qty" class="form-control" value="1" min="0" max="1000" required>
                            </div>
                            
                            <div id="specsArea" class="row g-3 px-0 m-0">
                                <div class="col-md-6">
                                    <label class="form-label">CPU</label>
                                    <input type="text" name="cpu" class="form-control" placeholder="Core i7 / M2">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">RAM</label>
                                    <input type="text" name="ram" class="form-control" placeholder="16GB">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Storage</label>
                                    <input type="text" name="ssd" class="form-control" placeholder="512GB SSD">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">GPU</label>
                                    <input type="text" name="gpu" class="form-control" placeholder="RTX 3050 / Integrated">
                                </div>
                            </div>

                            <div id="descriptionArea" class="col-12" style="display: none;">
                                <label class="form-label">ពិពណ៌នាផលិតផល (Description)</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="បញ្ជាក់ព័ត៌មានលម្អិតពី Accessories..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">រូបភាពផលិតផល</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0" style="border-radius: 0 0 20px 20px;">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">បោះបង់</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm" style="border-radius: 10px;">រក្សាទុកផលិតផល</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toggleSidebarBtn = document.getElementById('toggleSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const categorySelect = document.getElementById('categorySelect');
        const specsArea = document.getElementById('specsArea');
        const descriptionArea = document.getElementById('descriptionArea');

        // បើក Sidebar លើ Mobile
        if(toggleSidebarBtn) {
            toggleSidebarBtn.addEventListener('click', () => {
                sidebarMenu.classList.add('show');
                sidebarOverlay.classList.add('show');
            });
        }

        // មុខងារបិទ Sidebar វិញ
        const closeSidebar = () => {
            sidebarMenu.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        };

        if(closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
        if(sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

        // បិទ Sidebar ស្វ័យប្រវត្តពេលប្តូរ Tab លើទូរស័ព្ទដៃ
        const navButtons = document.querySelectorAll('.nav-link-custom[data-bs-toggle="tab"]');
        navButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });

        // លាក់/បង្ហាញ Specs តាមប្រភេទផលិតផលដែលជ្រើសរើសក្នុង Modal
        if(categorySelect) {
            categorySelect.addEventListener('change', function() {
                if (this.value === 'Accessories') {
                    specsArea.style.setProperty('display', 'none', 'important');
                    descriptionArea.style.display = 'block';
                } else {
                    specsArea.style.setProperty('display', 'flex', 'important');
                    descriptionArea.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>