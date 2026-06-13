<?php
session_start();
require_once './db/db.php';

$message_sent = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // បញ្ចូលទិន្នន័យទៅក្នុងតារាង contacts
    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if ($conn->query($sql)) {
        $message_sent = true;
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ទំនាក់ទំនងយើង - Cshop</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f9; /* ពណ៌ផ្ទៃក្រោយដូចទំព័រ About Us */
            font-family: 'Kantumruy Pro', sans-serif;
        }

        .contact-section {
            padding: 60px 10%;
        }

        /* កាតធំរួមបញ្ចូលគ្នា (មិនឱ្យដាច់ពីគ្នា ឬស្រឡះពេក) */
        .contact-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* ផ្នែកគ្រប់គ្រងប៊ូតុងខាងលើ */
        .contact-header {
            display: flex;
            justify-content: flex-end; /* រុញប៊ូតុងត្រឡប់ក្រោយទៅខាងស្តាំ */
            align-items: center;
            width: 100%;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            background: #f8f9fa;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.3s;
            border: 1px solid #eaeaea;
        }

        .btn-back:hover {
            background: #e9ecef;
            color: #000;
        }

        /* ប្លង់ចែកជា ២ ជួរ (ព័ត៌មាន និង Form) */
        .contact-main-layout {
            display: grid;
            grid-template-columns: 1fr 1.8fr;
            gap: 40px;
        }

        /* ផ្នែកព័ត៌មានខាងឆ្វេង (ប្តូរមកប្រើពណ៌រាងក្រម៉ៅស្រទន់) */
        .contact-info {
            background: #1a1a1a;
            color: #ffffff;
            padding: 40px 30px;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .contact-info h2 {
            color: #ff4757;
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .contact-info p {
            color: #ccc;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .info-content-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-item i {
            font-size: 1.2rem;
            color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50px;
        }

        .info-item span {
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* ផ្នែក Form ផ្ញើសារខាងស្តាំ */
        .contact-form {
            padding: 10px 0;
        }

        .contact-form h2 {
            color: #1a1a1a;
            margin: 0 0 25px 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* សម្រាប់ប្រធានបទ និង សារ ឱ្យដើរពេញទំហំ */
        .full-width {
            grid-column: span 2;
        }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            outline: none;
            font-family: 'Kantumruy Pro', sans-serif;
            font-size: 0.9rem;
            background: #f8f9fa;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #ff4757;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
        }

        .btn-send {
            background: #1a1a1a;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Kantumruy Pro', sans-serif;
            transition: 0.3s;
            display: inline-block;
            width: auto;
        }

        .btn-send:hover {
            background: #ff4757;
            transform: translateY(-2px);
        }

        /* សម្រាប់អេក្រង់ទូរស័ព្ទ (Responsive) */
        @media (max-width: 900px) {
            .contact-section {
                padding: 30px 4%;
            }

            .contact-card {
                padding: 25px;
            }

            .contact-main-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }

            .contact-header {
                justify-content: center; /* មកកណ្តាលវិញនៅលើទូរស័ព្ទ */
            }
            
            .btn-send {
                width: 100%; /* ពេញអេក្រង់ទូរស័ព្ទ */
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <section class="contact-section">
        <div class="contact-card">
            
            <div class="contact-header">
                <a href="about.php" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> ត្រឡប់ក្រោយ
                </a>
            </div>

            <div class="contact-main-layout">
                <div class="contact-info">
                    <h2>ព័ត៌មានទំនាក់ទំនង</h2>
                    <p>ប្រសិនបើអ្នកមានចម្ងល់ ឬចង់សួរព័ត៌មានបន្ថែម សូមទាក់ទងមកយើងតាមរយៈ៖</p>
                    
                    <div class="info-content-list">
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+855 12 345 678</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span>support@cshop.com</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ភ្នំពេញ, ប្រទេសកម្ពុជា</span>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h2>ផ្ញើសារមកកាន់យើង</h2>
                    <form action="" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>ឈ្មោះរបស់អ្នក</label>
                                <input type="text" name="name" required placeholder="បញ្ចូលឈ្មោះ...">
                            </div>
                            <div class="form-group">
                                <label>អ៊ីមែល</label>
                                <input type="email" name="email" required placeholder="example@gmail.com">
                            </div>
                            <div class="form-group full-width">
                                <label>ប្រធានបទ</label>
                                <input type="text" name="subject" required placeholder="សួរអំពីផលិតផល...">
                            </div>
                            <div class="form-group full-width">
                                <label>សារ</label>
                                <textarea name="message" rows="5" required placeholder="សរសេរសាររបស់អ្នកនៅទីនេះ..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn-send">ផ្ញើសារឥឡូវនេះ</button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <?php if ($message_sent): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'ផ្ញើសារជោគជ័យ!',
            text: 'យើងនឹងទាក់ទងទៅអ្នកវិញក្នុងពេលឆាប់ៗនេះ។',
            confirmButtonColor: '#ff4757',
            customClass: {
                popup: 'font-family: "Kantumruy Pro", sans-serif;'
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>