<?php
session_start();
require_once '../db/db.php';

// ១. ពិនិត្យសិទ្ធិ (អនុញ្ញាតតែ Admin ប៉ុណ្ណោះ)
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ២. មុខងារលុប User (Delete)
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    // ការពារកុំឱ្យ Admin លុបខ្លួនឯង
    if ($id == $_SESSION['id']) {
        $error = "អ្នកមិនអាចលុបគណនីផ្ទាល់ខ្លួនបានទេ។";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: manage_users.php?msg=deleted");
            exit;
        }
    }
}

// ៣. មុខងារប្តូរ Role (Admin <-> User)
if (isset($_GET['promote_id']) && isset($_GET['new_role'])) {
    $id = (int)$_GET['promote_id'];
    $role = $_GET['new_role'];
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    $stmt->execute();
    header("Location: manage_users.php?msg=updated");
    exit;
}

// ៤. ទាញយកបញ្ជី User ទាំងអស់
$result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>គ្រប់គ្រងអ្នកប្រើប្រាស់</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Khmer OS Battambang', sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .table thead { background: #4361ee; color: white; }
        .role-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-users-cog me-2"></i>គ្រប់គ្រងអ្នកប្រើប្រាស់</h2>
        <a href="admin_dashboard.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ</a>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>ឈ្មោះអ្នកប្រើ</th>
                        <th>អ៊ីមែល</th>
                        <th>តួនាទី</th>
                        <th>ថ្ងៃបង្កើត</th>
                        <th class="text-center">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-4"><?php echo $user['id']; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if($user['role'] == 'admin'): ?>
                                <span class="role-badge bg-primary text-white">Admin</span>
                            <?php else: ?>
                                <span class="role-badge bg-secondary text-white">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d-M-Y', strtotime($user['created_at'])); ?></td>
                        <td class="text-center">
                            <?php if($user['role'] == 'user'): ?>
                                <a href="?promote_id=<?php echo $user['id']; ?>&new_role=admin" class="btn btn-sm btn-success" onclick="return confirm('តើអ្នកចង់តម្លើង User នេះជា Admin ឬ?')"><i class="fas fa-user-shield"></i></a>
                            <?php else: ?>
                                <a href="?promote_id=<?php echo $user['id']; ?>&new_role=user" class="btn btn-sm btn-warning" onclick="return confirm('តើអ្នកចង់ដាក់ Admin នេះជា User ធម្មតាវិញឬ?')"><i class="fas fa-user"></i></a>
                            <?php endif; ?>

                            <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('តើអ្នកប្រាកដថាចង់លុបអ្នកប្រើប្រាស់នេះទេ?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>