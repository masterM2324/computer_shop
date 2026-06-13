<?php
// order_confirmation.php
session_start();

// 1. Check if a success message exists
$confirmation_message = "Your order has been placed."; // Default message
if (isset($_SESSION['message'])) {
    $confirmation_message = $_SESSION['message'];
    // Clear the message immediately after fetching it
    unset($_SESSION['message']); 
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #28a745; /* Green for success */
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 30px;
        }
        .button-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🎉 Order Successful!</h1>
    
    <p><?php echo htmlspecialchars($confirmation_message); ?></p>
    
    <a href="index.php" class="button-link">Continue Shopping</a>
</div>

</body>
</html>