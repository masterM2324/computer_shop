<?php
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cpu = $_POST['cpu'];
    $ram = $_POST['ram'];
    $ssd = $_POST['ssd'];
    $gpu = $_POST['gpu'];
    $qty = $_POST['qty'];
    $category = $_POST['category']; // ចាប់យកតម្លៃពី Select Dropdown

    // ការរៀបចំរូបភាព
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);

    // SQL Query ថ្មីដែលបញ្ចូលទាំង Category និង Qty
    $sql = "INSERT INTO products (name, price, cpu, ram, ssd, gpu, qty, category, image_main) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssssiss", $name, $price, $cpu, $ram, $ssd, $gpu, $qty, $category, $image);

    if ($stmt->execute()) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        header("Location: admin_dashboard.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>