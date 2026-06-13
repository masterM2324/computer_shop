<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// បើមិនទាន់បាន Login ទេ ឱ្យរុញទៅទំព័រ Login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("Location: login.php");
    exit;
}
?>