<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// ភ្ជាប់ទៅកាន់ Database ដោយប្រើប្រាស់ផ្លូវរបស់ឯកសារអ្នក (db/db.php)
require_once 'db/db.php'; 

// ហៅបណ្ណាល័យ PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$error = '';
$success = '';

// កំណត់ដំណាក់កាល (Step) នៃលំហូរការងារ
if (!isset($_SESSION['reset_step'])) {
    $_SESSION['reset_step'] = 1; // 1: បញ្ចូលអ៊ីមែល, 2: ផ្ទៀងផ្ទាត់ OTP, 3: ដាក់ Password ថ្មី
}

// --- ដំណាក់កាលទី ១៖ ផ្ញើកូដ OTP ទៅកាន់អ៊ីមែល ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_email'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = "សូមបញ្ចូលអ៊ីមែលរបស់អ្នក។";
    } else {
        // ពិនិត្យមើលថាតើអ៊ីមែលនេះមានក្នុងប្រព័ន្ធដែរឬទេ
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $error = "អ៊ីមែលនេះមិនមាននៅក្នុងប្រព័ន្ធឡើយ!";
        } else {
            // បង្កើតកូដ OTP ៦ ខ្ទង់
            $otp = rand(100000, 999999);
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['reset_email'] = $email;

            // ផ្ញើអ៊ីមែលតាមរយៈ Gmail SMTP
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                
                // ⚠️ បំពេញទិន្នន័យ Gmail របស់អ្នកនៅទីនេះ
                $mail->Username   = 'Webme232024@gmail.com'; 
                $mail->Password   = 'ocxo luqf acbf biwp'; 
                
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('Webme232024@gmail.com', 'C-Shop Computer');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'លេខកូដ OTP សម្រាប់ការប្តូរពាក្យសម្ងាត់ថ្មី';
                $mail->Body    = "
                    <div style='font-family: sans-serif; max-width: 500px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                        <h2 style='color: #dc2626; text-align: center;'>C-Shop Computer</h2>
                        <p>យើងបានទទួលសំណើសុំប្តូរពាក្យសម្ងាត់គណនីរបស់អ្នក។</p>
                        <p>សូមប្រើប្រាស់លេខកូដ OTP ខាងក្រោមនេះដើម្បីបន្ត៖</p>
                        <div style='background: #fef2f2; padding: 15px; text-align: center; font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #dc2626; border-radius: 8px;'>
                            $otp
                        </div>
                        <p style='color: #6b7280; font-size: 13px; margin-top: 15px;'>សម្គាល់៖ ប្រសិនបើអ្នកមិនមែនជាអ្នកស្នើសុំទេ សូមកុំបង្ហាញលេខកូដនេះឱ្យអ្នកដទៃដឹងឡើយ។</p>
                    </div>";

                $mail->send();
                $_SESSION['reset_step'] = 2; // ទៅវគ្គវាយ OTP
                $success = "លេខកូដ OTP ត្រូវបានផ្ញើទៅកាន់អ៊ីមែលរបស់អ្នករួចហើយ។";
            } catch (Exception $e) {
                $error = "មិនអាចផ្ញើអ៊ីមែលបានទេ! Error: {$mail->ErrorInfo}";
            }
        }
        $stmt->close();
    }
}

// --- ដំណាក់កាលទី ២៖ ផ្ទៀងផ្ទាត់លេខកូដ OTP ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_otp'])) {
    $input_otp = trim($_POST['otp_input']);

    if ($input_otp == $_SESSION['reset_otp']) {
        $_SESSION['reset_step'] = 3; // ទៅវគ្គប្តូរ Password
        $success = "លេខកូដត្រឹមត្រូវ! សូមបញ្ចូលពាក្យសម្ងាត់ថ្មីរបស់អ្នក។";
    } else {
        $error = "លេខកូដ OTP មិនត្រឹមត្រូវទេ! សូមពិនិត្យមើលឡើងវិញ។";
    }
}

// --- ដំណាក់កាលទី ៣៖ រក្សាទុកពាក្យសម្ងាត់ថ្មី ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_password'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "ពាក្យសម្ងាត់ទាំងពីរមិនផ្ទៀងផ្ទាត់គ្នាទេ!";
    } elseif (strlen($new_pass) < 6) {
        $error = "ពាក្យសម្ងាត់ត្រូវមានយ៉ាងហោចណាស់ ៦ ខ្ទង់!";
    } else {
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // សម្អាត Session ចោលទាំងអស់
            unset($_SESSION['reset_step']);
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_email']);

            // រុញទៅទំព័រ Login វិញដោយភ្ជាប់ Status
            header("Location: login.php?status=password_reset");
            exit();
        } else {
            $error = "មានបញ្ហាក្នុងការប្តូរពាក្យសម្ងាត់៖ " . $conn->error;
        }
        $stmt->close();
    }
}

// ករណីចង់ Cancel ឬត្រឡប់ទៅចាប់ផ្តើមឡើងវិញ
if (isset($_GET['action']) && $_GET['action'] == 'restart') {
    unset($_SESSION['reset_step']);
    unset($_SESSION['reset_otp']);
    unset($_SESSION['reset_email']);
    header("Location: forgot_password.php");
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ភ្លេចពាក្យសម្ងាត់ - C-Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', 'Khmer OS Battambang', sans-serif; }
        body { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .card { width: 100%; max-width: 420px; background: white; padding: 35px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); text-align: center; }
        .title { color: #4f46e5; font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 25px; line-height: 1.5; }
        .form-group { margin-bottom: 20px; text-align: left; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .form-control { width: 100%; padding: 12px 15px; padding-left: 45px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.3s; }
        .form-control:focus { border-color: #4f46e5; outline: none; }
        .input-icon { position: absolute; left: 15px; bottom: 12px; color: #4f46e5; font-size: 18px; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(45deg, #4f46e5, #3b82f6); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: left; font-weight: 600; }
        .alert-danger { background: #fee2e2; color: #991b1b; border-left: 4px solid #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid #166534; }
        .otp-input { text-align: center; letter-spacing: 5px; font-size: 20px; font-weight: bold; padding-left: 15px !important; }
        .links { margin-top: 20px; font-size: 14px; }
        .links a { color: #4f46e5; text-decoration: none; font-weight: bold; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    
    <?php if($error): ?>
        <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success">✅ <?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($_SESSION['reset_step'] == 1): ?>
        <div class="title">ភ្លេចពាក្យសម្ងាត់?</div>
        <div class="subtitle">សូមបញ្ចូលអ៊ីមែលគណនីរបស់អ្នក ដើម្បីទទួលលេខកូដ OTP សម្រាប់ការកំណត់ពាក្យសម្ងាត់ឡើងវិញ។</div>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>អ៊ីមែលរបស់អ្នក៖</label>
                <div class="input-icon"><i class="fas fa-envelope"></i></div>
                <input type="email" name="email" class="form-control" placeholder="ឧទាហរណ៍៖ name@gmail.com" required autocomplete="off">
            </div>
            <button type="submit" name="submit_email" class="btn">ផ្ញើលេខកូដ OTP</button>
        </form>

    <?php elseif ($_SESSION['reset_step'] == 2): ?>
        <div class="title">ផ្ទៀងផ្ទាត់ OTP</div>
        <div class="subtitle">សូមពិនិត្យប្រអប់សារ Gmail របស់អ្នក និងយកលេខកូដ ៦ ខ្ទង់មកបំពេញខាងក្រោម។</div>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>បញ្ចូលលេខកូដ OTP ៦ ខ្ទង់៖</label>
                <input type="text" name="otp_input" class="form-control otp-input" placeholder="------" maxlength="6" required autocomplete="off">
            </div>
            <button type="submit" name="submit_otp" class="btn">ផ្ទៀងផ្ទាត់កូដ</button>
        </form>
        <div class="links"><a href="forgot_password.php?action=restart">ផ្ញើកូដម្តងទៀត</a></div>

    <?php elseif ($_SESSION['reset_step'] == 3): ?>
        <div class="title">ពាក្យសម្ងាត់ថ្មី</div>
        <div class="subtitle">សូមបង្កើតពាក្យសម្ងាត់ថ្មីដែលមានសុវត្ថិភាពខ្ពស់ និងងាយចាំសម្រាប់អ្នក។</div>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>ពាក្យសម្ងាត់ថ្មី៖</label>
                <div class="input-icon"><i class="fas fa-lock"></i></div>
                <input type="password" name="new_password" class="form-control" placeholder="យ៉ាងហោចណាស់ ៦ ខ្ទង់" minlength="6" required>
            </div>
            <div class="form-group">
                <label>បញ្ជាក់ពាក្យសម្ងាត់ថ្មី៖</label>
                <div class="input-icon"><i class="fas fa-lock"></i></div>
                <input type="password" name="confirm_password" class="form-control" placeholder="បញ្ចូលពាក្យសម្ងាត់ម្តងទៀត" minlength="6" required>
            </div>
            <button type="submit" name="submit_password" class="btn">រក្សាទុកពាក្យសម្ងាត់ថ្មី</button>
        </form>
    <?php endif; ?>

    <div class="links">
        <a href="login.php"><i class="fas fa-arrow-left me-1"></i> ត្រឡប់ទៅទំព័រ Login</a>
    </div>
</div>

</body>
</html>