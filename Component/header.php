<?php
// ១. ត្រូវមាន session_start() បើមិនទាន់មានក្នុង index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ២. ត្រូវ require ឯកសារ db.php មុនគេបង្អស់ ដើម្បីឱ្យវាស្គាល់ $conn
// ប្រសិនបើ file db.php នៅក្រៅ folder Component អ្នកត្រូវថែម ../ 
if (file_exists(__DIR__ . '/../db/db.php')) {
    require_once __DIR__ . '/../db/db.php';
}

// ចាប់យកឈ្មោះ File បច្ចុប្បន្ន
$current_page = basename($_SERVER['PHP_SELF']);

$cart_count = 0;
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    // ឥឡូវនេះវានឹងស្គាល់ $conn ហើយ
    $sql_count = "SELECT SUM(quantity) as total FROM cart WHERE cart_id = ? AND status = 'active'";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $res_count = $stmt_count->get_result();
    $row_count = $res_count->fetch_assoc();
    $cart_count = $row_count['total'] ? $row_count['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cshop Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ពណ៌សម្រាប់ Light Mode (ធម្មតា) */
        :root {
            --bg-color: #ffffff;
            --text-color: #333333;
            --header-bg: rgba(172, 172, 172, 0.9);
            --border-color: #ddd;
            --primary-color: #ff5722;
        }

        /* ពណ៌សម្រាប់ Dark Mode (នៅពេល body មាន class "dark-mode") */
        body.dark-mode {
            --bg-color: #1a1a1a;
            --text-color: #f1f1f1;
            --header-bg: rgba(50, 50, 50, 0.95);
            --border-color: #444;
        }

        /* ប្រើប្រាស់អថេរពណ៌ទៅលើ Element ផ្សេងៗ */
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: 0.3s;
            margin: 0;
            font-family: sans-serif;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* រៀបចំ Header ឱ្យប្រើ Flexbox ដើម្បីងាយស្រួលធ្វើ Responsive */
        .main-header {
            background-color: var(--header-bg);
            display: flex;
            justify-content: space-between;
            align-items: center; /* <--- ជួយឱ្យធាតុទាំងអស់នៅក្នុង Header ស្មើគ្នាចំកណ្ដាល */
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s;
        }
        .header-left, .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left a {
            margin-top: -8px;
        }

        /* ឡូហ្គោ Cshop ដើមរបស់អ្នក */
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-right: 30px;
            margin-top: -8px;
            color: red;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-shadow: #666 2px 2px 4px;
        }

        /* ស្ទីល Menu ដើមរបស់អ្នក */
        .main-nav {
            margin-top: 0; /* <--- លុបចោល! ដើម្បីកុំឱ្យវាលោតទៅលើហួសហេតុ */
            display: flex;
            align-items: center; /* <--- បង្ខំឱ្យអក្សរ Menu ធ្លាក់មកចំកណ្ដាលស្មើនឹងឡូហ្គោ */
            gap: 5px;
        }

        .main-nav a {
            margin-right: 20px;
            font-weight: 500;
            font-size: 18px;
            padding-bottom: 5px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            display: inline-block;
            /* បន្ថែម Transition សម្រាប់ Scale ឱ្យ Smooth បែប Elastic */
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.3s ease;
        }

        /* ស្ទីល Active Link ដើមរបស់អ្នក (រក្សាបន្ទាត់ក្រោមដដែល) */
        .main-nav a.active {
            color: #000 !important;
            border-bottom: 3px solid #000 !important;
            padding-bottom: 8px !important;
            font-weight: bold !important;
        }
        
        body.dark-mode .main-nav a.active {
            color: #fff !important;
            border-bottom: 3px solid #fff !important;
        }

        /* ស្ទីល Hover បែប Scale ថ្មី (បាត់កន្ត្រាក់ បាត់លោតខ្ពស់) */
        .main-nav a:hover {
            color: var(--text-color);
            transform: scale(1.1); /* រីកធំឡើងមកមុខ ១០% បែប Elastic */
            border-bottom: 3px solid black;
        }

        /* ប៊ូតុង Menu សម្រាប់ចុចលើទូរស័ព្ទ (លាក់នៅលើកុំព្យូទ័រ) */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: var(--text-color);
            cursor: pointer;
        }

        /* Search Bar */
        .search-bar {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
            background-color: var(--bg-color);
            margin-right: 20px;
        }

        .search-bar input {
            border: none;
            padding: 8px 15px;
            outline: none;
            background-color: transparent;
            width: 250px;
            color: var(--text-color);
        }

        .search-bar button {
            background-color: transparent;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            color: var(--text-color);
        }

        .user-actions {
            position: relative;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-actions i {
            font-size: 18px;
            margin-left: 15px;
            cursor: pointer;
        }

        /* Dropdown CSS */
        .user-actions .user-dropdown {
            display: inline-block;
        }

        .user-actions .user-summary {
            cursor: pointer;
            list-style: none;
            display: flex;
            align-items: center;
        }

        .user-actions .user-summary::-webkit-details-marker,
        .user-actions .user-summary::marker {
            display: none;
            content: "";
        }

        .user-menu-list {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 100;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-width: 160px;
            padding: 8px 0;
            margin-top: 5px;
            list-style: none;
        }

        .user-menu-list li a {
            display: block;
            padding: 10px 15px;
            font-size: 0.95em;
            color: var(--text-color);
            white-space: nowrap;
            transition: background-color 0.2s;
        }

        .user-menu-list li a:hover {
            background-color: rgba(0,0,0,0.05);
            color: var(--primary-color);
        }

        .user-menu-list .separator {
            height: 1px;
            background-color: var(--border-color);
            margin: 5px 0;
        }

        /* =======================================================
           ផ្នែក Responsive (Media Queries) សម្រាប់ទូរស័ព្ទ និងថេប្លេត
           ======================================================= */
        @media (max-width: 992px) {
            .main-header {
                flex-wrap: wrap; /* ឱ្យធ្លាក់ចុះក្រោមបើអស់កន្លែង */
                padding: 15px 20px;
            }

            .header-left {
                width: 100%;
                justify-content: space-between; /* រុញឡូហ្គោទៅឆ្វេង ប៊ូតុងទៅស្តាំ */
            }

            .menu-toggle {
                display: block; /* បង្ហាញប៊ូតុង Menu លើទូរស័ព្ទ */
                order: 2;
            }
            
            .logo {
                order: 1;
                margin-right: 0;
            }

            /* បំប្លែងរាងរន្ធ Menu ឱ្យទៅជាបញ្ឈរធ្លាក់ចុះពីលើ */
            .main-nav {
                display: none; /* លាក់វាសិន ពេលមិនទាន់ចុចបើក */
                flex-direction: column;
                width: 100%;
                position: absolute;
                top: 100%;
                left: 0;
                background-color: var(--header-bg);
                padding: 15px;
                box-sizing: border-box;
                gap: 15px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                border-bottom: 1px solid var(--border-color);
            }

            /* បង្ហាញ Menu មកវិញនៅពេលមាន Class .active (បញ្ជាដោយ JS) */
            .main-nav.active {
                display: flex;
            }

            .main-nav a {
                margin-right: 0;
                padding-bottom: 8px;
                width: 100%;
            }
            
            .main-nav a:hover {
                transform: scale(1.03); /* បន្ថយការរីកលើទូរស័ព្ទបន្តិចដើម្បីកុំឱ្យធ្លាយប្រអប់ */
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
                margin-top: 10px;
                gap: 10px;
            }

            .search-bar {
                flex-grow: 1; /* ឱ្យប្រឡោះស្វែងរកពង្រីកពេញកន្លែងទំនេរ */
                margin-right: 0;
            }

            .search-bar input {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            /* សម្រាប់ទូរស័ព្ទតូចៗ */
            .header-right {
                flex-direction: column;
                align-items: stretch;
            }
            .user-actions {
                justify-content: space-around;
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <a href="index.php"><span class="logo">Cshop</span></a>
            
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="main-nav" id="mainNav">
                <?php
                // ចាប់យកឈ្មោះ file បច្ចុប្បន្នឱ្យច្បាស់
                $current_uri = $_SERVER['REQUEST_URI'];

                function checkActive($current_page, $target)
                {
                    // ពិនិត្យមើលឈ្មោះឯកសារបច្ចុប្បន្នដើម្បីផ្តល់ Class active
                    return (basename($current_page) === $target) ? 'active' : '';
                }
                ?>
                <a href="Laptop.php" class="<?php echo checkActive($current_page, 'Laptop.php'); ?>">Laptop</a>
                <a href="Desktop.php" class="<?php echo checkActive($current_page, 'Desktop.php'); ?>">Desktop</a>
                <a href="Accessories.php" class="<?php echo checkActive($current_page, 'Accessories.php'); ?>">Accessories</a>
            </nav>
        </div>
        <div class="header-right">
            <form action="index.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="ស្វែងរកផលិតផល..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="./dashboard_admin/admin_dashboard.php" class="btn-admin"><i class="fas fa-user-shield"></i></a>
            <?php endif; ?>
            <div class="user-actions">
                <a id="modeToggle"><i class="fas fa-sun"></i></a>
                <a href="favorites.php"><i class="far fa-heart"></i></a>
                <a href="view_cart.php" style="position: relative;">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count"
                        style="position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;">
                        <?php echo $cart_count; ?>
                    </span>
                </a>
                <details class="user-dropdown">
                    <summary class="user-summary">
                       <i class="fas fa-user-circle me-1 text-secondary" style="margin-right: 5px;"></i>
                       <span><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'មិនមានគណនី'; ?></span>
                    </summary>
                    <ul class="user-menu-list">
                        <?php if (isset($_SESSION['username'])): ?>
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="my_History.php">History</a></li>
                            <li class="separator"></li>
                            <li><a href="logout.php">ចាកចេញ (Logout)</a></li>
                        <?php else: ?>
                            <li><a href="login.php">ចូលប្រើ (Login)</a></li>
                            <li><a href="signup.php">ចុះឈ្មោះ (Sign Up)</a></li>
                        <?php endif; ?>
                    </ul>
                </details>
            </div>
        </div>
    </header>

    <script>
        // --- លេខកូដ JavaScript ដើមសម្រាប់ Dark Mode របស់អ្នក ---
        const modeToggle = document.getElementById('modeToggle');
        const body = document.body;
        const icon = modeToggle.querySelector('i');

        modeToggle.addEventListener('click', (e) => {
            e.preventDefault(); 

            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'light');
            }
        });

        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            icon.classList.replace('fa-sun', 'fa-moon');
        }

        // --- បន្ថែម JavaScript សម្រាប់បញ្ជាប៊ូតុង Menu លើទូរស័ព្ទ ---
        const menuToggle = document.getElementById('menuToggle');
        const mainNav = document.getElementById('mainNav');

        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
        });
    </script>
</body>
</html>