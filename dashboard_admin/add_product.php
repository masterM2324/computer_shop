<?php
// ភ្ជាប់ទៅ Database
require_once '../db/db.php'; 

// កំណត់ Directory សម្រាប់រក្សាទុករូបភាព
$upload_dir = "../uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ១. ទទួលបានទិន្នន័យ
    $name        = $_POST['name'];
    $price       = $_POST['price'];
    $cpu         = $_POST['cpu'];
    $ram         = $_POST['ram'];
    $ssd         = $_POST['ssd'];
    $gpu         = $_POST['gpu'];
    $qty         = $_POST['qty'];
    $description = $_POST['description']; // <--- កែសម្រួលឈ្មោះអថេរ និងថែមសញ្ញា ; រួចរាល់
    $category    = $_POST['category'];    // <--- ចាប់យកតម្លៃ Laptop, Desktop ឬ Accessories

    // ២. គ្រប់គ្រងការ Upload រូបភាព
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        
        $image_info = $_FILES['image'];
        $image_tmp_name = $image_info['tmp_name'];
        $image_extension = strtolower(pathinfo($image_info['name'], PATHINFO_EXTENSION));
        
        // បង្កើតឈ្មោះរូបភាពថ្មីដើម្បីកុំឱ្យជាន់គ្នា
        $new_image_name = uniqid('prod_', true) . time() . "." . $image_extension;
        $target_file = $upload_dir . $new_image_name;
        
        // ផ្លាស់ទីរូបភាពទៅកាន់ Folder uploads ជាមុនសិន
        if (move_uploaded_file($image_tmp_name, $target_file)) {
            
            // ៣. បញ្ចូលទិន្នន័យទៅក្នុង Database (បន្ថែម column 'description' និង 'category')
            $sql = "INSERT INTO products (name, price, cpu, ram, ssd, gpu, qty, description, category, image_main) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            // s = string, d = double (price), i = integer (qty)
            // ពិនិត្យឡើងវិញ៖ មាន types ទាំងអស់ 10 ត្រូវគ្នានឹងចំនួនអថេរ 10
            $stmt->bind_param("sdssssisss", $name, $price, $cpu, $ram, $ssd, $gpu, $qty, $description, $category, $new_image_name);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: admin_dashboard.php?status=success");
                exit();
            } else {
                // ប្រសិនបើ INSERT មិនចូល ត្រូវលុបរូបភាពដែលបាន Upload មុននេះចេញវិញដើម្បីកុំឱ្យណែន Disk
                if (file_exists($target_file)) {
                    unlink($target_file);
                }
                echo "Error ក្នុង Database: " . $stmt->error;
            }
            $stmt->close();
        } else {
            header("Location: admin_dashboard.php?status=error&msg=UploadFailed");
            exit();
        }
    } else {
        echo "Error: មិនមានឯកសាររូបភាព ឬមានបញ្ហាពេល Upload។";
    }
    $conn->close();
}
?>