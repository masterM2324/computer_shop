<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
require_once '../db/db.php'; 

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $upload_dir = "../uploads/";

    // ១. ស្វែងរកឈ្មោះរូបភាពចាស់ដើម្បីលុបចេញពី Server
    $stmt = $conn->prepare("SELECT image_main FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($product) {
        $image_to_delete = $product['image_main'];

        // ២. លុបទិន្នន័យផលិតផលចេញពី Database
        $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt_delete->bind_param("i", $product_id);

        if ($stmt_delete->execute()) {
            
            // ៣. លុបរូបភាពចេញពី Folder uploads/
            if (file_exists($upload_dir . $image_to_delete) && $image_to_delete != '') {
                unlink($upload_dir . $image_to_delete);
            }
            
            $stmt_delete->close();
            $conn->close();
            // Redirect ទៅ Dashboard វិញ
            header("Location: admin_dashboard.php?status=success");
            exit();
        } else {
            $stmt_delete->close();
            $conn->close();
            header("Location: admin_dashboard.php?status=error&msg=" . urlencode("Error deleting record from DB."));
            exit();
        }
    } else {
        $conn->close();
        header("Location: admin_dashboard.php?status=error&msg=" . urlencode("Product not found."));
        exit();
    }
} else {
    header("Location: admin_dashboard.php?status=error&msg=" . urlencode("No ID specified."));
    exit();
}
?>