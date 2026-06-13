<?php
// ចាប់ផ្តើម Session នៅលើគ្រប់ទំព័រដែលប្រើ Session
session_start();
require_once 'db/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "សូមបំពេញ Username និង Password។";
    } else {

        // 1. ស្វែងរក User នៅក្នុង Database
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('SQL prepare failed: ' . $conn->error . ' (Query: ' . $sql . ')');
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // 2. ផ្ទៀងផ្ទាត់ Password ដែលបាន Hash
            if (password_verify($password, $user['password'])) {

                // Login ជោគជ័យ!

                // 3. កំណត់ Session Variables
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // នេះជាចំណុចសំខាន់សម្រាប់ Role-Based

                // 4. Redirect ទៅតាមតួនាទី (Role)
                if ($user['role'] === 'admin') {
                    header("Location: dashboard_admin/admin_dashboard.php");
                    exit();
                } elseif ($user['role'] === 'user') {
                    header("Location: index.php");
                    exit();
                } else {
                    // Redirect User ទូទៅទៅកាន់ទំព័រដើម
                    header("Location: index.php");
                    exit();
                }

            } else {
                $error = "Password មិនត្រឹមត្រូវ។";
            }
        } else {
            // កែសម្រួលអក្ខរាវិរុទ្ធត្រង់ពាក្យ "ប្រព័ន្ធ"
            $error = "Username មិនមាននៅក្នុងប្រព័ន្ធទេ។";
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
    <title>Login - ចូលប្រើគណនី</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Moul&display=swap" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Google Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-variation-settings:"GRAD" 0;
        }

        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            font-size: 16px; /* កំណត់ Base Font Size នៅទីនេះជំនួសវិញ */
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }

        .login-header h2 {
            font-size: 26px;
            margin-bottom: 10px;
            font-family: "Moul", serif;
            font-weight: 400;
            font-style: normal;
        }

        .login-header p {
            font-size: 15px;
            opacity: 0.9;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #FFD700;
            border-radius: 2px;
        }

        .login-form {
            padding: 35px 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-family: "Google Sans", sans-serif;
            font-optical-sizing: auto;
            font-size: 21px;
            font-weight: 400;
            font-style: normal;
            font-variation-settings:"GRAD" 0;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 14px 20px;
            padding-left: 48px;
            padding-right: 48px;
            border: 2px solid #ddd;
            border-radius: 10px;
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
            left: 18px;
            color: #6a11cb;
            font-size: 16px;
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            cursor: pointer;
            color: #777;
            font-size: 16px;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #6a11cb;
        }

        .forgot-password-box {
            text-align: right;
            margin-top: 8px;
        }

        .forgot-password-link {
            font-size: 14px;
            color: #6a11cb;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }
        .forgot-password-link:hover {
            color: #2575fc;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #5a0db5, #1c68f0);
            transform: translateY(-2px);
            box-shadow: 0 7px 15px rgba(106, 17, 203, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #888;
            font-size: 14px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        .divider:not(:empty)::before { margin-right: .5em; }
        .divider:not(:empty)::after { margin-left: .5em; }

        .google-btn-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .alert-error {
            background-color: #ffeaea;
            color: #d32f2f;
            border-left: 5px solid #d32f2f;
        }

        .alert-success {
            background-color: #e8f7ef;
            color: #2e7d32;
            border-left: 5px solid #2e7d32;
        }

        .alert-icon {
            margin-right: 10px;
            font-size: 18px;
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }

        .login-footer p {
            margin-bottom: 10px;
        }

        .login-footer a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-footer a:hover {
            color: #2575fc;
            text-decoration: underline;
        }

        .back-home {
            display: inline-block;
            margin-top: 5px;
        }

        .back-home i {
            margin-right: 6px;
            transition: transform 0.3s ease;
        }

        .back-home:hover i {
            transform: translateX(-4px);
        }

        /* 📱 RESPONSIVE MEDIA QUERIES */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .login-container {
                border-radius: 15px;
            }

            .login-header {
                padding: 25px 15px;
            }

            .login-header h2 {
                font-size: 22px;
            }

            .login-form {
                padding: 25px 20px;
            }

            .form-control {
                padding: 12px 15px;
                padding-left: 42px;
                padding-right: 42px;
                font-size: 14px;
            }

            .input-icon, .password-toggle {
                font-size: 15px;
            }

            .btn-login {
                padding: 12px;
                font-size: 15px;
            }

            .g_id_signin {
                max-width: 100% !important;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-header">
            <h2>ចូលប្រើគណនី</h2>
            <p>សូមបំពេញព័ត៌មានរបស់អ្នកដើម្បីចូលប្រើប្រព័ន្ធ</p>
        </div>

        <form class="login-form" action="" method="POST">
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success_reg'): ?>
                <div class="alert alert-success">
                    <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                    <span>ការចុះឈ្មោះជោគជ័យ។ សូម Login!</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'password_reset'): ?>
                <div class="alert alert-success">
                    <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                    <span>ពាក្យសម្ងាត់ត្រូវបានប្តូរជោគជ័យ! សូម Login ឡើងវិញ។</span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username:</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div style="width: 100%;">
                        <input type="text" id="username" name="username" class="form-control"
                            placeholder="បញ្ចូលឈ្មោះគណនីរបស់អ្នក" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div style="width: 100%;">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="បញ្ចូលពាក្យសម្ងាត់" required>
                    </div>
                    <span class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="forgot-password-box">
                    <a href="forgot_password.php" class="forgot-password-link">ភ្លេចពាក្យសម្ងាត់?</a>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> ចូលប្រើគណនី
            </button>

            <div class="divider">ឬចូលប្រើជាមួយ</div>

            <div class="google-btn-wrapper">
                <div id="g_id_onload"
                     data-client_id="882547606508-cigq2k4oirff1ro3onq6mvdm1265154q.apps.googleusercontent.com"
                     data-context="signin"
                     data-ux_mode="popup"
                     data-login_uri="http://localhost/computershopcshop/callback.php"
                     data-auto_prompt="false">
                </div>

                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="pill"
                     data-theme="outline"
                     data-text="signin_with"
                     data-size="large"
                     data-width="100%" 
                     data-logo_alignment="left">
                </div>
            </div>

            <div class="login-footer">
                <p>មិនទាន់មានគណនី? <a href="signup.php">Sign Up</a></p>
                <a href="index.php" class="back-home">
                    <i class="fas fa-arrow-left"></i> ត្រឡប់ទៅទំព័រដើម
                </a>
            </div>
        </form>
    </div>

    <script>
        // មុខងារ បើក/បិទ មើល Password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // មុខងារ Focus លើប្រអប់ Username ពេល Load ទំព័រដំបូង
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('username').focus();

            // បន្ថែម Animation ទៅកាន់ Form Elements ទាំងអស់
            const formGroups = document.querySelectorAll('.form-group, .btn-login, .divider, .google-btn-wrapper, .login-footer');
            formGroups.forEach((group, index) => {
                group.style.animation = `fadeIn 0.5s ease-out ${index * 0.08}s both`;
            });
        });
    </script>
</body>

</html>