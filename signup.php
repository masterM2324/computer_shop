<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once 'db/db.php';

$error = '';
$username = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // 1. Validation 
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "សូមបំពេញគ្រប់វាលទាំងអស់។";
    } elseif ($password !== $confirm_password) {
        $error = "Password ទាំងពីរមិនដូចគ្នាទេ។";
    } elseif (strlen($password) < 6) {
        $error = "Password ត្រូវមានយ៉ាងហោចណាស់ ៦ តួ។";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "សូមបញ្ចូល Email ត្រឹមត្រូវ។";
    } else {
        
        // 2. ការ Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 3. កំណត់តួនាទី (Admin ឬ User)
        $role = 'user'; 
        
        // 4. បញ្ចូលទៅ Database
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
        
        // ប្រើ try...catch ដើម្បីចាប់ mysqli_sql_exception
        try {
            if ($stmt->execute()) {
                // បញ្ជូនទៅទំព័រ Login បន្ទាប់ពីចុះឈ្មោះជោគជ័យ
                header("Location: login.php?status=success_reg");
                exit();
            } else {
                $error = "ការចុះឈ្មោះបរាជ័យ។";
            }
        } catch (mysqli_sql_exception $e) {
            // លេខកូដកំហុស 1062 គឺបញ្ជាក់ថាមានទិន្នន័យជាន់គ្នា (Duplicate Entry)
            if ($e->getCode() === 1062) {
                if (strpos($e->getMessage(), 'username') !== false) {
                    $error = "Username នេះមានគេប្រើរួចហើយ។";
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    $error = "Email នេះមានគេប្រើរួចហើយ។";
                } else {
                    $error = "Username ឬ Email នេះមានគេប្រើរួចហើយ។";
                }
            } else {
                $error = "មានបញ្ហាប្រព័ន្ធ៖ " . $e->getMessage();
            }
        }
        
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ចុះឈ្មោះគណនី</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Khmer OS Battambang', Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }
        
        .signup-container {
            width: 100%;
            max-width: 440px; /* បង្រួមទទឹងបន្តិចដើម្បីឱ្យសមាមាត្រនឹងកម្ពស់ថ្មី */
            background-color: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-15px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .signup-header {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            padding: 22px 20px; /* បន្ថយពី 35px */
            text-align: center;
        }
        
        .signup-header h2 {
            font-size: 22px; /* បន្ថយពី 26px */
            margin-bottom: 4px;
            font-weight: 700;
        }
        
        .signup-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .signup-form {
            padding: 22px 25px; /* បន្ថយពី 35px 30px */
        }
        
        .form-group {
            margin-bottom: 14px; /* បន្ថយគម្លាតចន្លោះប្រអប់ពី 22px មក 14px */
            animation: slideIn 0.4s ease-out both;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }
        
        .form-control {
            width: 100%;
            padding: 11px 40px; /* បន្ថយពី 14px ដើម្បីឱ្យប្រអប់រៀតជាងមុន */
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #f9f9f9;
        }
        
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.1);
            outline: none;
            background-color: white;
        }
        
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a11cb;
            font-size: 16px;
            pointer-events: none;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            z-index: 10;
            padding: 3px;
        }

        .password-toggle:hover {
            color: #6a11cb;
        }
        
        .btn-signup {
            width: 100%;
            padding: 12px; /* បន្ថយពី 15px */
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 5px;
            animation: slideIn 0.5s ease-out both;
        }
        
        .btn-signup:hover {
            background: linear-gradient(90deg, #5a0db5, #1c68f0);
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(106, 17, 203, 0.2);
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            animation: fadeIn 0.4s ease-out;
            font-size: 13px;
        }
        
        .alert-error {
            background-color: #ffeaea;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
        }
        
        .alert-icon {
            margin-right: 8px;
            font-size: 16px;
        }
        
        .signup-footer {
            text-align: center;
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        
        .signup-footer a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-footer a:hover {
            color: #2575fc;
            text-decoration: underline;
        }
        
        .language-note {
            font-size: 12px;
            color: #999;
            text-align: center;
            margin-top: 5px;
            font-style: italic;
        }
        
        .password-strength {
            height: 4px; /* បន្ថយពី 5px */
            width: 100%;
            background-color: #eee;
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background-color: #ff4757; width: 30%; }
        .strength-medium { background-color: #ffa502; width: 60%; }
        .strength-strong { background-color: #2ed573; width: 100%; }
        
        .password-requirements {
            font-size: 12px;
            color: #777;
            margin-top: 4px;
        }
        
        .requirement i {
            margin-right: 4px;
            font-size: 10px;
        }
        
        .requirement.valid { color: #2ed573; }
        .requirement.invalid { color: #ff4757; }
        
        /* សម្រាប់ Mobile អេក្រង់តូចបំផុត */
        @media (max-width: 480px) {
            .signup-form {
                padding: 18px 20px;
            }
            .signup-header {
                padding: 18px 15px;
            }
            .form-control {
                padding: 10px 38px;
                font-size: 14px;
            }
            .btn-signup {
                padding: 11px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h2>ចុះឈ្មោះគណនី</h2>
            <p>សូមបំពេញព័ត៌មានខាងក្រោមដើម្បីបង្កើតគណនី</p>
        </div>
        
        <form class="signup-form" action="signup.php" method="POST">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="បញ្ចូលឈ្មោះគណនី" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="បញ្ចូលអាសយដ្ឋានអ៊ីមែល" 
                           value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="បញ្ចូលពាក្យសម្ងាត់ (យ៉ាងហោច ៦ តួ)" required>
                    <span class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="password-requirements">
                    <div class="requirement" id="lengthReq">
                        <i class="fas fa-circle"></i> យ៉ាងហោចណាស់ ៦ តួអក្សរ
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-control" placeholder="បញ្ចូលពាក្យសម្ងាត់ម្តងទៀត" required>
                    <span class="password-toggle" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div id="passwordMatch" class="password-requirements" style="display: none;">
                    <div class="requirement">
                        <i class="fas fa-check-circle" id="matchIcon"></i> 
                        <span id="matchText"></span>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn-signup">
                <i class="fas fa-user-plus"></i> ចុះឈ្មោះគណនី
            </button>
            
            <div class="signup-footer">
                <p>មានគណនីរួចហើយ? <a href="login.php">ចូលប្រើគណនី</a></p>
                <p class="language-note">ប្រព័ន្ធគ្រប់គ្រងជាភាសាខ្មែរ</p>
            </div>
        </form>
    </div>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                confirmPasswordInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const lengthReq = document.getElementById('lengthReq');
            const lengthIcon = lengthReq.querySelector('i');
            
            if (password.length >= 6) {
                lengthReq.classList.add('valid');
                lengthReq.classList.remove('invalid');
                lengthIcon.className = 'fas fa-check-circle';
                lengthIcon.style.color = '#2ed573';
            } else {
                lengthReq.classList.add('invalid');
                lengthReq.classList.remove('valid');
                lengthIcon.className = 'fas fa-times-circle';
                lengthIcon.style.color = '#ff4757';
            }
            
            let strength = 0;
            if (password.length >= 6) strength += 30;
            if (password.length >= 8) strength += 10;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[a-z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            strengthBar.style.width = Math.min(strength, 100) + '%';
            
            if (strength < 40) {
                strengthBar.className = 'strength-bar strength-weak';
            } else if (strength < 70) {
                strengthBar.className = 'strength-bar strength-medium';
            } else {
                strengthBar.className = 'strength-bar strength-strong';
            }
            
            checkPasswordMatch();
        });
        
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            const matchIcon = document.getElementById('matchIcon');
            const matchText = document.getElementById('matchText');
            
            if (confirmPassword.length > 0) {
                matchDiv.style.display = 'block';
                
                if (password === confirmPassword) {
                    matchIcon.className = 'fas fa-check-circle';
                    matchIcon.style.color = '#2ed573';
                    matchText.textContent = 'ពាក្យសម្ងាត់ត្រូវគ្នា';
                    matchText.style.color = '#2ed573';
                } else {
                    matchIcon.className = 'fas fa-times-circle';
                    matchIcon.style.color = '#ff4757';
                    matchText.textContent = 'ពាក្យសម្ងាត់មិនត្រូវគ្នា';
                    matchText.style.color = '#ff4757';
                }
            } else {
                matchDiv.style.display = 'none';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
            
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.animationDelay = (index * 0.05) + 's';
            });
        });
        
        document.querySelector('.signup-form').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 6 || password !== confirmPassword) {
                event.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>