<?php
// បើកមុខងារបង្ហាញ Error ដើម្បីងាយស្រួលតាមដាន
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ១. ចាប់ផ្តើម Session ដើម្បីកត់ត្រាការឡូកអ៊ីន
session_start();

// ២. ភ្ជាប់ទៅកាន់ Database
require_once 'db/db.php';

// កំណត់ Google Client ID របស់អ្នកនៅទីនេះដើម្បីផ្ទៀងផ្ទាត់សុវត្ថិភាព (សំខាន់ណាស់!)
define('GOOGLE_CLIENT_ID', '882547606508-cigq2k4oirff1ro3onq6mvdm1265154q.apps.googleusercontent.com');

if (isset($_POST['credential'])) {
    $id_token = $_POST['credential'];
    
    // ៣. ប្រើប្រាស់ cURL ដើម្បីផ្ទៀងផ្ទាត់ Token ជាមួយ Google API ផ្ទាល់
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($id_token);
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // បិទការឆែក SSL លើ Free Hosting (បើអាច បើកវិញជា true ពេលឡើង Premium Host)
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // បន្ថែម Timeout ១០វិនាទី ការពារកុំឱ្យគាំង Server ពេល Google API យឺត
    
    $response = curl_exec($ch);
    
    // បន្ថែមការឆែកកំហុស cURL ក្រែងលោ Server មិនអាចភ្ជាប់ទៅកាន់ Google បាន
    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }
    curl_close($ch);
    
    $payload = json_decode($response, true);
    
    // ៤. ឆែកមើលលក្ខខណ្ឌសុវត្ថិភាព (Token ត្រឹមត្រូវ និងជារបស់ Client ID របស់វេបសាយយើង)
    if ($payload && !isset($payload['error_description'])) {
        
        // បន្ថែមការផ្ទៀងផ្ទាត់ថា Token នេះពិតជាផ្ញើមកកាន់វេបសាយយើងមែន (ការពារ App ផ្សេងមកបន្លំ)
        if ($payload['aud'] !== GOOGLE_CLIENT_ID) {
            die("ការផ្ទៀងផ្ទាត់សុវត្ថិភាពបរាជ័យ៖ Client ID មិនត្រឹមត្រូវ!");
        }
        
        // ទាញយកព័ត៌មានពី Google Account
        $google_id       = $payload['sub']; 
        $full_name       = $payload['name'];
        $email           = $payload['email'];
        $profile_picture = isset($payload['picture']) ? $payload['picture'] : 'default_avatar.png'; // ការពារក្រែងលោគណនីខ្លះគ្មានរូបភាព

        // កាត់យកអក្សរខាងមុខ @ នៃ Email ធ្វើជា Username បណ្ដោះអាសន្ន
        $username = explode('@', $email)[0]; 

        // ៥. ឆែកមើលក្នុង Database ថាតើ Google ID នេះធ្លាប់មានហើយឬនៅ?
        $sql = "SELECT id, username, role, full_name, profile_picture FROM users WHERE google_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // [កែប្រែចំណុចទី១] ចាប់យកទិន្នន័យមកដាក់ក្នុង $user ជាមុនសិន
            $user = $result->fetch_assoc();
            
            // ធ្វើការ Update ឈ្មោះ និង រូបភាព Profile ក្រែងលោគាត់ផ្លាស់ប្តូរនៅលើ Google Account របស់គាត់
            $update_sql = "UPDATE users SET full_name = ?, profile_picture = ? WHERE google_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sss", $full_name, $profile_picture, $google_id);
            $update_stmt->execute();
            $update_stmt->close();
            
        } else {
            // កាលណាបើមិនទាន់មាន Google ID ទេ៖ ឆែកមើលក្រែងលោគាត់ធ្លាប់ចុះឈ្មោះធម្មតាដោយប្រើ Email នេះ
            $email_sql = "SELECT id, username, role FROM users WHERE email = ?";
            $email_stmt = $conn->prepare($email_sql);
            $email_stmt->bind_param("s", $email);
            $email_stmt->execute();
            $email_result = $email_stmt->get_result();

            if ($email_result->num_rows == 1) {
                // បើមាន Email ហ្នឹងហើយ៖ ធ្វើការភ្ជាប់ Google ID និង រូបភាព Profile ចូលគណនីចាស់នោះតែម្តង
                $user = $email_result->fetch_assoc();
                
                $link_sql = "UPDATE users SET google_id = ?, profile_picture = ? WHERE email = ?";
                $link_stmt = $conn->prepare($link_sql);
                $link_stmt->bind_param("sss", $google_id, $profile_picture, $email);
                $link_stmt->execute();
                $link_stmt->close();
                
            } else {
                // បើថ្មីស្រឡាង (មិនទាន់មានទាំង Email ទាំង Google ID)៖ ធ្វើការចុះឈ្មោះ (Register) ជូនគាត់ភ្លាមៗ
                
                // ការពារក្រែងលោ Username ជាន់ជាមួយអ្នកដទៃ (ព្រោះក្នុង DB កំណត់ជា UNIQUE)
                $check_user_sql = "SELECT id FROM users WHERE username = ?";
                $check_user_stmt = $conn->prepare($check_user_sql);
                $check_user_stmt->bind_param("s", $username);
                $check_user_stmt->execute();
                $check_user_result = $check_user_stmt->get_result();
                
                if ($check_user_result->num_rows > 0) {
                    $username = $username . rand(100, 999); // ប្ដូរជា ៣ខ្ទង់ ដើម្បីកាត់បន្ថយការជាន់គ្នាឱ្យកាន់តែខ្ពស់
                }
                $check_user_stmt->close();

                // រៀបចំបញ្ចូលទិន្នន័យថ្មី (ត្រង់ password ទុកចោលជា NULL ព្រោះគាត់ចូលតាម Google)
                $insert_sql = "INSERT INTO users (google_id, username, email, full_name, profile_picture, role) VALUES (?, ?, ?, ?, ?, 'user')";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sssss", $google_id, $username, $email, $full_name, $profile_picture);
                $insert_stmt->execute();
                $insert_stmt->close();

                // ទាញយកព័ត៌មានដែលទើបតែបញ្ចូលមិញនេះ ដើម្បីយកទៅបង្កើត Session
                $get_user_sql = "SELECT id, username, role FROM users WHERE google_id = ?";
                $get_user_stmt = $conn->prepare($get_user_sql);
                $get_user_stmt->bind_param("s", $google_id);
                $get_user_stmt->execute();
                $user = $get_user_stmt->get_result()->fetch_assoc();
                $get_user_stmt->close();
            }
            $email_stmt->close();
        }
        $stmt->close();

        // ៦. កំណត់ Session Variables ឱ្យដូចប្រព័ន្ធ Login ធម្មតារបស់អ្នកទាំងស្រុង
        $_SESSION['loggedin']        = TRUE;
        $_SESSION['id']              = $user['id'];
        $_SESSION['username']        = $user['username'];
        $_SESSION['role']            = $user['role']; // រក្សាសិទ្ធិ admin ឬ user
        
        // បន្ថែម Session ថ្មី ២ ទៀត (សម្រាប់យកទៅបង្ហាញរូបថត និងឈ្មោះពេញលើ Header វេបសាយ)
        $_SESSION['full_name']       = $full_name;
        $_SESSION['profile_picture'] = $profile_picture;

        // បិទការភ្ជាប់ Database មុនពេលរត់ទៅទំព័រផ្សេង
        $conn->close();

        // ៧. បែងចែកផ្លូវរត់ (Redirect) ទៅតាមតួនាទី (Role)
        if ($_SESSION['role'] === 'admin') {
            header("Location: dashboard_admin/admin_dashboard.php");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }

    } else {
        die("ការផ្ទៀងផ្ទាត់លក្ខខណ្ឌសុវត្ថិភាពពី Google បរាជ័យ!");
    }
} else {
    // បើគ្មានទិន្នន័យផ្ញើមកទេ ឱ្យត្រឡប់ទៅទំព័រ login.php វិញ
    header("Location: login.php");
    exit();
}
?>