<?php
// បើកមុខងារបង្ហាញ Error ដើម្បីងាយស្រួលតាមដាន
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../db/db.php'; 

// ពិនិត្យសិទ្ធិ Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$product_id = $_GET['id'] ?? null; 
$error = '';
$success = '';
$upload_dir = "../uploads/";

// --- ផ្នែកទី ១: ដំណើរការ UPDATE (ពេល Submit Form) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    
    $id = $_POST['product_id'];
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $cpu = trim($_POST['cpu'] ?? '');
    $ram = trim($_POST['ram'] ?? '');
    $ssd = trim($_POST['ssd'] ?? '');
    $gpu = trim($_POST['gpu'] ?? '');
    $qty = (int)($_POST['qty'] ?? 0); 
    
    $category = trim($_POST['category'] ?? ''); 
    $description = trim($_POST['description'] ?? ''); 
    $old_image = $_POST['old_image'] ?? '';

    $new_image_name = $old_image; 
    $upload_ok = true;

    // ពិនិត្យការ Upload រូបភាពថ្មី
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_exs = array("jpg", "jpeg", "png", "webp");

        if (in_array($image_extension, $allowed_exs)) {
            $new_image_name = uniqid('prod_', true) . "." . $image_extension;
            $target_file = $upload_dir . $new_image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // លុបរូបចាស់
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            } else {
                $error = "បរាជ័យក្នុងការរក្សារូបភាពថ្មីទុក។";
                $upload_ok = false;
            }
        } else {
            $error = "ប្រភេទរូបភាពមិនត្រឹមត្រូវ (អនុញ្ញាតតែ JPG, PNG, WEBP)។";
            $upload_ok = false;
        }
    }

    if ($upload_ok) {
        $sql = "UPDATE products SET name=?, price=?, cpu=?, ram=?, ssd=?, gpu=?, image_main=?, qty=?, description=?, category=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        
        // ពិនិត្យប្រភេទដេតា៖ s = string, d = double, i = integer
        $stmt->bind_param("sdsssssissi", $name, $price, $cpu, $ram, $ssd, $gpu, $new_image_name, $qty, $description, $category, $id);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?msg=updated");
            exit();
        } else {
            $error = "មានបញ្ហាបច្គេកទេស៖ " . $conn->error;
        }
        $stmt->close();
    }
}

// --- ផ្នែកទី ២: ជំនួសមកបង្ហាញវិញ (GET Request) ---
if ($product_id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close(); 
    if (!$product) die("រកមិនឃើញផលិតផលនេះទេ។");
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>កែប្រែផលិតផល - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', 'Khmer OS Battambang', sans-serif; }
        .edit-container { max-width: 700px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #444; }
        .current-img { width: 120px; height: 120px; object-fit: cover; border-radius: 10px; border: 2px solid #eee; }
        .btn-update { background: #4361ee; color: white; border-radius: 10px; padding: 12px; font-weight: bold; transition: 0.3s; }
        .btn-update:hover { background: #3a56d4; }
    </style>
</head>
<body>

<div class="container">
    <div class="edit-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>កែប្រែផលិតផល</h3>
            <a href="admin_dashboard.php" class="btn btn-light btn-sm"><i class="fas fa-times"></i></a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($product['image_main']); ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">ឈ្មោះផលិតផល</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">តម្លៃ ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price'] ?? 0); ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-primary">ចំនួនស្តុក (Quantity)</label>
                    <input type="number" name="qty" class="form-control fw-bold" value="<?php echo htmlspecialchars($product['qty'] ?? 0); ?>" min="0" required style="border: 2px solid #4361ee;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CPU</label>
                    <input type="text" name="cpu" class="form-control" value="<?php echo htmlspecialchars($product['cpu'] ?? ''); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">RAM</label>
                    <input type="text" name="ram" class="form-control" value="<?php echo htmlspecialchars($product['ram'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SSD</label>
                    <input type="text" name="ssd" class="form-control" value="<?php echo htmlspecialchars($product['ssd'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">GPU</label>
                    <input type="text" name="gpu" class="form-control" value="<?php echo htmlspecialchars($product['gpu'] ?? ''); ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">ប្រភេទផលិតផល (Category)</label>
                    <select name="category" class="form-select" required>
                        <option value="" disabled>-- ជ្រើសរើសប្រភេទផលិតផល --</option>
                        <option value="Laptop" <?php echo (isset($product['category']) && $product['category'] === 'Laptop') ? 'selected' : ''; ?>>Laptop</option>
                        <option value="Desktop" <?php echo (isset($product['category']) && $product['category'] === 'Desktop') ? 'selected' : ''; ?>>Desktop</option>
                        <option value="Accessories" <?php echo (isset($product['category']) && $product['category'] === 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">ការពិពណ៌នាផលិតផល (Description)</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="ព័ត៌មានលម្អិតបន្ថែមពីផលិតផល..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label d-block">រូបភាពបច្ចុប្បន្ន</label>
                    <img src="../uploads/<?php echo htmlspecialchars($product['image_main']); ?>" class="current-img mb-3">
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">ទុកទំនេរ ប្រសិនបើមិនចង់ប្តូររូបភាព</small>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-update w-100">
                        <i class="fas fa-save me-2"></i> រក្សាទុកការផ្លាស់ប្តូរ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>