<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Bhakti Kencana</title>
    <link rel="shortcut icon" href="{{ asset('assets/backend/images/logos/ubk.jpg') }}" type="image/jpg" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #ffcc00 0%, #ff8800 50%, #ff5722 100%);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            width: 1000px;
            max-width: 100%;
            min-height: 600px;
            border-radius: 30px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .left-side {
            flex: 1;
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .left-side::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .left-side::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }

        .logo-container {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .logo {
            width: 140px;
            height: auto;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.2));
        }

        .left-side h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 0.8rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .left-side p {
            font-size: 15px;
            max-width: 320px;
            text-align: center;
            opacity: 0.95;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .right-side {
            flex: 1.2;
            background-color: #fff;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h3 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            color: #718096;
            font-size: 15px;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fee;
            color: #c53030;
            border: 1px solid #fc8181;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        input {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 15px;
            color: #2d3748;
        }

        input:focus {
            outline: none;
            border-color: #ff9800;
            box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.15);
        }

        .invalid-feedback {
            color: #e53e3e;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 152, 0, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #a0aec0;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider span {
            padding: 0 1rem;
        }

        .btn-google {
            width: 100%;
            padding: 13px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: #2d3748;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-google:hover {
            border-color: #667eea;
            background: #f7fafc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .google-icon {
            width: 20px;
            height: 20px;
        }

        .bottom-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 15px;
            color: #4a5568;
        }

        .bottom-text a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .bottom-text a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        @media (max-width: 850px) {
            .container {
                flex-direction: column;
                min-height: auto;
            }

            .left-side {
                padding: 2rem;
                min-height: 250px;
            }

            .right-side {
                padding: 2.5rem 2rem;
            }

            h3 {
                font-size: 24px;
            }

            .left-side h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Bagian kiri -->
        <div class="left-side">
            <div class="logo-container">
                <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="Logo UBK" class="logo">
                <h2>Universitas Bhakti Kencana</h2>
                <p> Kelola booking ruangan dan peminjaman fasilitas kampus dengan mudah</p>
            </div>
        </div>

        <!-- Bagian kanan -->
        <div class="right-side">
            <h3>Selamat Datang! 👋</h3>
            <p class="subtitle">Silakan login untuk mengakses sistem booking</p>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="nama@email.com" required autofocus>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Masukkan password" required>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="divider">
                <span>atau lanjutkan dengan</span>
            </div>

            <a href="{{ route('google.login') }}" class="btn-google">
                <svg class="google-icon" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                    <path fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                Masuk dengan Google
            </a>

            <p class="bottom-text">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar sekarang</a>
            </p>
        </div>
    </div>
</body>

</html>
