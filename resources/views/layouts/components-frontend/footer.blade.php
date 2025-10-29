<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BKU Footer - Simple</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .footer {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
        }

        .footer h5 {
            color: #ecf0f1;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer p {
            color: #bdc3c7;
            line-height: 1.5;
        }

        .footer-link {
            color: #bdc3c7;
            text-decoration: none;
            display: block;
            padding: 5px 0;
            transition: color 0.3s;
        }

        .footer-link:hover {
            color: #3498db;
        }

        .social-link {
            color: #bdc3c7;
            font-size: 1.2rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-link:hover {
            color: #3498db;
            transform: translateY(-2px);
        }

        .footer-bottom {
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body>
    <!-- Demo content -->


    <!-- Simple Footer -->
    <footer class="footer mt-5 pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Bhakti Kencana</h5>
                    <p>Sistem Penjadwalan ruangan Labkom dan Peminjaman Barang secara digital. Bebas bentrok dan efisien.</p>
                </div>

                <div class="col-md-4">
                    <h5>Menu</h5>
                    <a href="#" class="footer-link">Booking Ruangan</a>
                    <a href="#" class="footer-link">Daftar Ruangan</a>
                    <a href="#" class="footer-link">Login Admin</a>
                </div>

                <div class="col-md-4">
                    <h5>Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/universitas.kencana" class="social-link" target="_blank">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/BhaktiKencanaU" class="social-link" target="_blank">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com/bku.campus" class="social-link" target="_blank">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3">
                <div class="d-flex flex-md-row justify-content-between align-items-center">
                    <small>&copy; 2025 Bhakti Kencana. All rights reserved.</small>
                    <small>Dibuat oleh Team IT BKU</small>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
