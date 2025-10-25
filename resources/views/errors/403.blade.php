<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak | 403</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 40px 60px;
            text-align: center;
        }

        .error-code {
            font-size: 80px;
            font-weight: 700;
            line-height: 1;
        }

        .btn-home {
            background: #fff;
            color: #1e3a8a;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-home:hover {
            background: #1e3a8a;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-code">403</div>
        <h3 class="mt-3 fw-bold">Akses Ditolak</h3>
        <p class="mb-4">Maaf, kamu tidak memiliki hak untuk mengakses halaman ini.</p>
        <a href="{{ route('frontend.welcome') }}" class="btn btn-home">
            <i class="bi bi-house-door"></i> Kembali ke Beranda
        </a>
    </div>
</body>

</html>
