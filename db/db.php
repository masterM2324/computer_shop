<?php
$host = "localhost"; 
$user = "root";
$pass = ""; // ជាទូទៅ XAMPP គ្មានលេខសម្ងាត់ទេ
$db   = "Cshop"; // ឈ្មោះ Database ក្នុង phpMyAdmin ម៉ាស៊ីនអ្នក

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>