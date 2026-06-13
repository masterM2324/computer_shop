<?php
session_start();
require_once './db/db.php'; 

// ពិនិត្យ Authentication
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$message = '';
$message_type = ''; 

// --- ដំណើរការ UPDATE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $new_password = $_POST['password'];

    // ១. រៀបចំ SQL Update ជាមូលដ្ឋាន (ឈ្មោះ និង អ៊ីមែល)
    if (!empty($new_password)) {
        // បើមានបញ្ចូល Password ថ្មី ត្រូវ Hash វាសិន
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE users SET username = ?, email = ?, full_name = ?, password = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $username, $email, $full_name, $hashed_password, $user_id);
    } else {
        // បើអត់បញ្ចូល Password ទេ Update តែព័ត៌មានទូទៅ
        $sql_update = "UPDATE users SET username = ?, email = ?, full_name = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $username, $email, $full_name, $user_id);
    }
    
    if ($stmt_update) {
        if ($stmt_update->execute()) {
            $_SESSION['msg'] = "✅ ព័ត៌មានត្រូវបានកែប្រែដោយជោគជ័យ!";
            $_SESSION['msg_type'] = 'success';
            header("Location: profile.php");
            exit();
        } else {
            $message = "❌ មិនអាចកែប្រែបានទេ៖ អ៊ីមែល ឬ Username នេះមានគេប្រើរួចហើយ!";
            $message_type = 'danger';
        }
        $stmt_update->close();
    } 
}

// ទាញយក Message ពី Session
if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    $message_type = $_SESSION['msg_type'];
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
}

// --- ទាញយកព័ត៌មានសម្រាប់បង្ហាញ ---
$sql_fetch = "SELECT username, email, full_name FROM users WHERE id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$user_data = $result->fetch_assoc();

$stmt_fetch->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>កែប្រែប្រវត្តិរូប</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Siemreap&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root { --primary-color: #4e73df; --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body { font-family: 'Khmer OS Siemreap', sans-serif; background: var(--bg-gradient); min-height: 100vh; display: flex; align-items: center; }
        .main-card { border-radius: 20px; background: #fff; box-shadow: 0 15px 35px rgba(0,0,0,0.2); overflow: hidden; border:none; }
        .header-section { padding: 30px; text-align: center; border-bottom: 1px solid #eee; }
        .btn-update { background: var(--bg-gradient); border: none; color: white; font-weight: 600; }
    </style>
</head>
<body>

<div class="container" style="max-width: 500px;">
    <div class="main-card">
        <div class="header-section">
            <div class="mb-3 text-primary"><i class="fas fa-user-circle fa-4x"></i></div>
            <h4 class="fw-bold">គ្រប់គ្រងគណនី</h4>
        </div>

        <div class="card-body p-4">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="POST">
                <input type="hidden" name="update_profile" value="1">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="ឈ្មោះពេញ" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" required>
                    <label for="full_name">ឈ្មោះពេញ</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                    <label for="username">ឈ្មោះអ្នកប្រើប្រាស់ (Username)</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    <label for="email">អ៊ីមែល (Email)</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    <label for="password">លេខសម្ងាត់ថ្មី (ទុកទំនេរ បើមិនចង់ប្តូរ)</label>
                    <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> បញ្ចូលតែក្នុងករណីអ្នកចង់ផ្លាស់ប្តូរលេខសម្ងាត់ប៉ុណ្ណោះ</small>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="index.php" class="btn btn-outline-secondary w-100 py-2"><i class="fas fa-arrow-left"></i> ថយក្រោយ</a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-update w-100 py-2"><i class="fas fa-save"></i> រក្សាទុក</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="text-center pb-4">
            <a href="logout.php" class="text-danger text-decoration-none small fw-bold">ចាកចេញពីគណនី</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>