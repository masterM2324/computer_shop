<?php
session_start();
require_once '../db/db.php';

// ១. ពិនិត្យសិទ្ធិចូលប្រើ (Admin Authentication)
// ត្រូវប្រាកដថា User បាន Login និងមាន Role = 'admin'
if (!isset($_SESSION['id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ២. ទាញទិន្នន័យផលិតផលពី Database
$sql = "SELECT * FROM Products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel - CShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .admin-container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .btn-add {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-add:hover {
            background: #219150;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            color: #333;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
        }

        .edit {
            color: #3498db;
        }

        .delete {
            color: #e74c3c;
        }

        .status-msg {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>

<body>

    <div class="admin-container">
        <div class="header">
            <h2><i class="fas fa-tasks"></i> គ្រប់គ្រងផលិតផល</h2>
            <div>
                <a href="add_product.php" class="btn-add"><i class="fas fa-plus"></i> បន្ថែមផលិតផល</a>
                <a href="index.php" style="margin-left:15px; text-decoration:none; color:#666;">ទៅកាន់ទំព័រដើម</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>រូបភាព</th>
                    <th>ឈ្មោះផលិតផល</th>
                    <th>ព័ត៌មានបច្ចេកទេស (Specs)</th>
                    <th>តម្លៃ</th>
                    <th>សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="images/<?php echo htmlspecialchars($row['image_main']); ?>" class="product-img"></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td style="font-size: 12px; color: #666;">
                                CPU: <?php echo $row['cpu']; ?> | RAM: <?php echo $row['ram']; ?>
                            </td>
                            <td style="color: #e67e22; font-weight: bold;">$<?php echo number_format($row['price'], 2); ?></td>
                            <td class="actions">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit" title="កែប្រែ"><i class="fas fa-edit"></i></a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="delete" title="លុប" onclick="return confirm('តើអ្នកពិតជាចង់លុបផលិតផលនេះមែនទេ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">មិនទាន់មានផលិតផលឡើយ។</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>