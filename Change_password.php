<?php 
include './db/db.php'; 

// ១. ពិនិត្យមើល Login
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$message = "";
$current_user_id = $_SESSION['id'];

// ២. នៅពេលអ្នកប្រើចុចប៊ូតុងផ្លាស់ប្តូរ
if (isset($_POST['change_pw'])) {
    $old_pw = $_POST['old_password'];
    $new_pw = $_POST['new_password'];
    $conf_pw = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($old_pw, $user['password'])) {
        if ($new_pw === $conf_pw) {
            $hashed_pw = password_hash($new_pw, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_pw, $current_user_id);
            
            if ($update_stmt->execute()) {
                $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <i class='bi bi-check-circle-fill me-2'></i>លេខសម្ងាត់ត្រូវបានផ្លាស់ប្តូរដោយជោគជ័យ!
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
            }
        } else {
            $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='bi bi-exclamation-triangle-fill me-2'></i>លេខសម្ងាត់ថ្មីមិនដូចគ្នាឡើយ!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
        }
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='bi bi-x-circle-fill me-2'></i>លេខសម្ងាត់ចាស់មិនត្រឹមត្រូវទេ!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ប្តូរលេខសម្ងាត់ - SmartWallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #4f46e5;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body { 
            background: var(--bg-gradient);
            font-family: 'Kantumruy Pro', sans-serif; 
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .card { 
            border: none; 
            border-radius: 20px; 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25 margin-left: rgba(99, 102, 241, 0.25);
            border-color: var(--primary-color);
        }

        .input-group-text {
            background-color: transparent;
            border-radius: 10px 0 0 10px;
            border-right: none;
            color: var(--primary-color);
        }

        .form-control {
            border-left: none;
        }

        .btn-update { 
            background: var(--primary-color);
            border: none;
            color: white; 
            border-radius: 12px;
            padding: 12px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-update:hover { 
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }

        .logo-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 px-4">
            <div class="card p-4 p-md-5">
                <div class="text-center">
                    <div class="logo-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">ប្តូរលេខសម្ងាត់</h3>
                    <p class="text-muted small mb-4">សូមបញ្ចូលលេខសម្ងាត់ចាស់ និងថ្មីរបស់អ្នក</p>
                </div>
                
                <?= $message; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">លេខសម្ងាត់ចាស់</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="old_password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">លេខសម្ងាត់ថ្មី</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="new_password" class="form-control" placeholder="យ៉ាងតិច ៦ ខ្ទង់" minlength="6" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">បញ្ជាក់លេខសម្ងាត់ថ្មី</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <input type="password" name="confirm_password" class="form-control" placeholder="បញ្ចូលម្តងទៀត" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="change_pw" class="btn btn-update">
                            <i class="bi bi-arrow-repeat me-2"></i>ផ្លាស់ប្តូរឥឡូវនេះ
                        </button>
                        <a href="view_profile.php" class="btn btn-link text-decoration-none text-muted small mt-2">
                            <i class="bi bi-arrow-left me-1"></i>ត្រឡប់ក្រោយ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>