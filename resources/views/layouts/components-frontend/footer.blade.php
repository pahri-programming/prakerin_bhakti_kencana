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
            <div class="row g-4">
                <div class="col-md-4">
                    <h5>Bhakti Kencana</h5>
                    <p>Sistem Penjadwalan ruangan Labkom dan Peminjaman Barang secara digital. Bebas bentrok dan
                        efisien.</p>
                </div>
                <div class="col-md-4">
                    <h5>Menu</h5>
                    <a href="{{ route('user.booking.create') }}" class="footer-link">Booking Ruangan</a>
                    <a href="{{ url('/#ruangan') }}" class="footer-link">Daftar Ruangan</a>
                    <a href="{{ route('user.peminjaman.create') }}" class="footer-link">Peminjaman Barang</a>
                    <a href="{{ url('/#barang') }}" class="footer-link">Daftar Barang</a>
                    <a href="{{ url('/#kontak') }}" class="footer-link">Kontak Kami</a>
                </div>
                <div class="col-md-4">
                    <h5>Ikuti Kami</h5>
                    <div class="d-flex gap-3 mb-4">
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
                    <h5>Lokasi</h5>
                    <p style="color:#bdc3c7; font-size:0.85rem; margin-bottom:0.75rem;">
                        Jl. Soekarno-Hatta No.754, Cipadung Kidul, Panyileukan, Bandung 40614
                    </p>
                    <div class="rounded overflow-hidden" style="border: 1.5px solid rgba(255,255,255,0.15);">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.5!2d107.7073995!3d-6.9365228!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68c2da266bc415%3A0xe59322528f76d912!2sBhakti%20Kencana%20University!5e0!3m2!1sid!2sid!4v1"
                            width="100%" height="160" style="border:0;display:block;" allowfullscreen=""
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.1);">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small style="color:#bdc3c7;">&copy; 2025 Bhakti Kencana. All rights reserved.</small>
                    <small style="color:#bdc3c7;">Dibuat oleh Team IT BKU</small>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
