<?php
session_start();
require_once './db/db.php';

// ១. ឆែកមើលថា តើអ្នកប្រើបាន Login ឬនៅ?
if (!isset($_SESSION['id'])) {
    // បើមិនទាន់ Login ទេ រុញទៅទំព័រ Login
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $product_id = $_POST['product_id'];
    $quantity = 1; // លោកអ្នកអាចថែម Input Quantity ក្នុង HTML បើចង់បានច្រើន

    // ២. បញ្ចូលទិន្នន័យទៅក្នុងតារាង Orders
    // ធ្វើឲ្យប្រាកដថាកូឡុំនៅត្រឹមត្រូវ (user_id ទេ id)
    $sql = "INSERT INTO Orders (user_id, product_id, quantity) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);

        if ($stmt->execute()) {
            echo "<script>
                alert('ការបញ្ជាទិញរបស់លោកអ្នកទទួលបានជោគជ័យ!');
                window.location.href = 'index.php';
            </script>";
        } else {
            // Execution failed
            die('Order insert failed: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        // Prepare failed
        die('SQL prepare failed: ' . $conn->error . ' (Query: ' . $sql . ')');
    }

    $conn->close();
}

