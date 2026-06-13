<?php
session_start();

// ប្រសិនបើគ្មានការផ្ញើ OTP ពីទំព័រចុះឈ្មោះទេ ឱ្យរុញទៅទំព័រ Sign-up វិញ
if (!isset($_SESSION['otp_code']) || !isset($_SESSION['pending_user'])) {
    header("Location: signup.php");
    exit();
}

$error_message = "";
$success_message = "";

// ដំណើរការនៅពេលដែល User ចុចប៊ូតុង Verify OTP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_otp = trim($_POST['otp_input']);

    // ផ្ទៀងផ្ទាត់លេខកូដ OTP
    if ($input_otp == $_SESSION['otp_code']) {
        
        // ភ្ជាប់ទៅកាន់ Database
        $conn = new mysqli("sql207.infinityfree.com", "if0_42052196", "5iKusSdQ81WOFJ", "if0_42052196_cshop");

        if ($conn->connect_error) {
            die("Database Connection Failed: " . $conn->connect_error);
        }

        // ទាញយកទិន្នន័យ User ពី Session
        $user = $_SESSION['pending_user'];
        $username = trim($user['username']);
        $email    = trim($user['email']);
        $password = $user['password']; 

        // ប្រើប្រាស់ Prepared Statement ការពារ SQL Injection និងកុំឱ្យខូច Font អក្សរខ្មែរ
        $sql = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $success_message = "ការផ្ទៀងផ្ទាត់ជោគជ័យ! គណនីរបស់អ្នកត្រូវបានបង្កើត។";
            
            // លុប Session ចោលដើម្បីសុវត្ថិភាព
            unset($_SESSION['otp_code']);
            unset($_SESSION['pending_user']);
            
            // រង់ចាំ 2 វិនាទី រួចរុញទៅទំព័រ Login
            header("refresh:2; url=login.php");
        } else {
            $error_message = "មានបញ្ហាក្នុងការរក្សាទុកទិន្នន័យ៖ " . $conn->error;
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $error_message = "លេខកូដ OTP មិនត្រឹមត្រូវទេ! សូមពិនិត្យមើលសារក្នុងអ៊ីមែលរបស់អ្នកឡើងវិញ។";
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ផ្ទៀងផ្ទាត់ OTP - C-Shop</title>
    <style>
        body { font-family: 'Segoe UI', 'Kantumruy', 'Khmer OS Battambang', sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .title { color: #4f46e5; margin-bottom: 10px; font-size: 24px; font-weight: bold; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 20px; }
        .input-group { margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 18px; text-align: center; letter-spacing: 6px; font-weight: bold; }
        .btn { background: linear-gradient(45deg, #4f46e5, #3b82f6); color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn:hover { opacity: 0.9; }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: left; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    </style>
</head>
<body>

<div class="card">
    <div class="title">ផ្ទៀងផ្ទាត់លេខកូដ OTP</div>
    <div class="subtitle">ប្រព័ន្ធបានផ្ញើលេខកូដ OTP ទៅកាន់អ៊ីមែល <b><?php echo htmlspecialchars($_SESSION['pending_user']['email'] ?? ''); ?></b> រួចរាល់ហើយ។</div>

    <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger">⚠️ <?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success_message)): ?>
        <div class="alert alert-success">✅ <?php echo $success_message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="input-group">
            <input type="text" name="otp_input" placeholder="------" required maxlength="6" autocomplete="off">
        </div>
        <button type="submit" class="btn">បញ្ជាក់លេខកូដ</button>
    </form>
</div>

</body>
</html>