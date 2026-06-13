<main>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .about-section {
            padding: 60px 10%;
            background: #f4f6f9;
            font-family: 'Kantumruy Pro', sans-serif;
        }

        .about-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .about-main-layout {
            display: flex;
            align-items: center;
            gap: 50px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .about-image {
            flex: 1;
            min-width: 320px;
        }

        .about-image img {
            width: 100%;
            display: block;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .about-content {
            flex: 1.2;
            min-width: 320px;
            display: flex;
            flex-direction: column;
        }

        .about-content .btn-back {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            background: #f8f9fa;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 15px;
            transition: 0.3s;
            border: 1px solid #eaeaea;
            
            /* រុញប៊ូតុងទៅខាងស្តាំដៃ */
            align-self: flex-end; 
        }

        .about-content .btn-back:hover {
            background: #e9ecef;
            color: #000;
        }

        .sub-title {
            color: #ff4757;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
        }

        .about-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 15px 0;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .about-content h2 span {
            color: #ff4757;
        }

        .about-content p {
            font-size: 0.95rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 25px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 25px;
        }

        .feature-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #edf0f5;
        }

        .feature-box i {
            color: #2ed573;
            font-size: 1.1rem;
            margin-top: 3px;
        }

        .feature-box span {
            font-size: 0.88rem;
            font-weight: 600;
            color: #333;
            line-height: 1.4;
        }

        .btn-contact {
            display: inline-block;
            padding: 12px 35px;
            background: #1a1a1a;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: 0.3s;
            align-self: flex-start; /* រក្សាប៊ូតុងទំនាក់ទំនងនៅខាងឆ្វេងដដែល */
        }

        .btn-contact:hover {
            background: #ff4757;
            transform: translateY(-2px);
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            padding-top: 35px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #ff4757;
            margin: 0 0 5px 0;
        }

        .stat-item p {
            color: #666;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .about-section {
                padding: 30px 4%;
            }

            .about-card {
                padding: 25px;
            }

            .about-main-layout {
                gap: 30px;
                margin-bottom: 30px;
            }

            .about-content h2 {
                font-size: 1.8rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .about-stats {
                grid-template-columns: 1fr;
                gap: 25px;
                padding-top: 25px;
            }
            
            /* នៅលើទូរស័ព្ទ ឱ្យប៊ូតុងត្រឡប់ក្រោយរត់មកកណ្តាលវិញដើម្បីស្អាត */
            .about-content .btn-back {
                align-self: center; 
            }
            .btn-contact {
                align-self: center;
            }
        }
    </style>

    <section class="about-section">
        <div class="about-card">
            
            <div class="about-main-layout">
                <div class="about-image">
                    <img src="https://img.freepik.com/free-photo/view-computer-hardware-components_23-2149410396.jpg" alt="Cshop Office">
                </div>

                <div class="about-content">
                    <a href="index.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i> ត្រឡប់ក្រោយ
                    </a>
                    <span class="sub-title">ស្វែងយល់ពីយើង</span>
                    <h2>ស្វាគមន៍មកកាន់ <span>Cshop</span></h2>
                    <p>
                        Cshop គឺជាហាងលក់គ្រឿងអេឡិចត្រូនិចឈានមុខគេនៅក្នុងប្រទេសកម្ពុជា
                        ដែលត្រូវបានបង្កើតឡើងក្នុងគោលបំណងផ្ដល់ជូននូវបច្ចេកវិទ្យាចុងក្រោយបង្អស់ដល់អតិថិជន។
                        យើងមានលក់គ្រប់ប្រភេទ Laptop, Desktop, និង Accessories ពីម៉ាកល្បីៗជុំវិញពិភពលោក។
                    </p>

                    <div class="features-grid">
                        <div class="feature-box">
                            <i class="fas fa-check-circle"></i>
                            <span>ផលិតផលសុទ្ធ ១០០% និងមានការធានាត្រឹមត្រូវ</span>
                        </div>
                        <div class="feature-box">
                            <i class="fas fa-check-circle"></i>
                            <span>តម្លៃសមរម្យបំផុតក្នុងទីផ្សារ</span>
                        </div>
                        <div class="feature-box">
                            <i class="fas fa-check-circle"></i>
                            <span>សេវាកម្មដឹកជញ្ជូនរហ័សទូទាំង ២៥ ខេត្ត-ក្រុង</span>
                        </div>
                    </div>

                    <a href="contact.php" class="btn-contact">ទំនាក់ទំនងយើង</a>
                </div>
            </div>

            <div class="about-stats">
                <div class="stat-item">
                    <h3>10K+</h3>
                    <p>អតិថិជនសរុប</p>
                </div>
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>ផលិតផលក្នុងស្តុក</p>
                </div>
                <div class="stat-item">
                    <h3>5+</h3>
                    <p>ឆ្នាំបទពិសោធន៍</p>
                </div>
            </div>

        </div>
    </section>
</main>