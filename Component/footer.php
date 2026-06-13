<mian>
    <style>
        /* Footer Styles */
        .main-footer {
            background: #e3e2e2;
            color: #000000;
            padding: 40px 0 10px;
            margin-top: 50px;
            font-family: 'Kantumruy Pro', sans-serif;
            /* ឬ Font ដែលអ្នកប្រើ */
            border-bottom: chocolate 1px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 0 20px;
        }

        .footer-section h3 {
            color: #df2c3b;
            /* ពណ៌ក្រហមដូច Logo */
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .footer-logo {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .footer-logo span {
            color: #f10f22;
        }

        .footer-section p {
            line-height: 1.6;
            color: #000000;
            font-family: 'Kantumruy Pro', sans-serif;

        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
            font-family: 'Kantumruy Pro', sans-serif;
        }

        .footer-section ul li a {
            color: #000000;
            text-decoration: none;
            transition: 0.3s;
            font-family: 'Kantumruy Pro', sans-serif;
        }

        .footer-section ul li a:hover {
            color: #ff4757;
            padding-left: 5px;
        }

        .socials a {
            color: #000000;
            font-size: 1.5rem;
            margin-right: 15px;
            transition: 0.3s;
        }

        .socials a:hover {
            color: #ff4757;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid #444;
            color: #000000;
            font-size: 0.9rem;
              font-family: 'Kantumruy Pro', sans-serif;
        }

        /* សម្រាប់ទូរស័ព្ទ */
        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
    </style>

    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section about">
                <h2 class="footer-logo"><span>Cshop</span></h2>
                <p>យើងគឺជាហាងលក់គ្រឿងអេឡិចត្រូនិចឈានមុខគេ ដែលផ្ដល់ជូននូវ Laptop, Desktops និង Accessories
                    ដែលមានគុណភាពខ្ពស់ និងតម្លៃសមរម្យ។</p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">ទំព័រដើម</a></li>
                    <li><a href="favorites.php">បញ្ជីចូលចិត្ត</a></li>
                    <li><a href="view_cart.php">រទេះទិញទំនិញ</a></li>
                    <li><a href="about.php">អំពីយើង</a></li>
                </ul>
            </div>

            <div class="footer-section contact">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> ខេត្ដ កំពង់ធំ, ប្រទេសកម្ពុជា</li>
                    <li><i class="fas fa-phone"></i> +855 087935529</li>
                    <li><i class="fas fa-envelope"></i> Webme232024@gmail.com</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2026 Cshop - រក្សាសិទ្ធិគ្រប់យ៉ាងដោយ ComputerShop
        </div>
    </footer>
</mian>