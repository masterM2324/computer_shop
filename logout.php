<?php
session_start();

// លុប Session ទាំងអស់
$_SESSION = array();

// បំផ្លាញ Session ចោល
session_destroy();

// Redirect ទៅទំព័រ Login ឬ Homepage
header("Location: login.php"); 
exit;
?>