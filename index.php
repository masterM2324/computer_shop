<?php  
// ត្រូវតែជាបន្ទាត់ដំបូងគេបង្អស់
session_start();

?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CShop - ASUS Laptops</title>
    <script src="js/script.js" defer></script>
    <script src="js/Card.js" defer></script>
    <link rel="stylesheet" href="CSS/banner.css">
    <link rel="stylesheet" href="CSS/indexstyle.css">
    <script src="https://kit.fontawesome.com/a5f77d1fc9.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/引入/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;700&family=Freehand&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="CSS/product-container.css">
</head>

<body>
    <?php include './Component/header.php'; ?>
    <?php include './Component/main.php'; ?>
    <?php include './Component/footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // បោះតម្លៃ Session id ពី PHP ទៅឲ្យ JavaScript ឆែកមើលការ Login
    const isLoggedIn = <?php echo isset($_SESSION['id']) ? 'true' : 'false'; ?>;
</script>
</html>