<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Bhakti Kencana</title>
    <link rel="shortcut icon" href="{{ asset('assets/backend/images/logos/ubk.jpg') }}" type="image/jpg" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffcc00, #ff8800, #ff5722);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box {
            background-color: #fff;
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.7s ease-out;
            text-align: center;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 120px;
            margin-bottom: 1rem;
        }

        .login-title {
            font-size: 26px;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.3rem;
        }

        .login-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 2rem;
        }

        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            color: #333;
            text-align: left;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 12px;
            transition: 0.3s ease;
            font-size: 14px;
            margin-bottom: 1rem;
        }

        input:focus {
            border-color: #ff8800;
            box-shadow: 0 0 10px rgba(255, 136, 0, 0.3);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(to right, #ff9800, #ff5722);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s ease, background 0.3s ease;
            cursor: pointer;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: linear-gradient(to right, #ffb300, #e65100);
        }

        .invalid-feedback {
            color: red;
            font-size: 13px;
            margin-top: -0.5rem;
            margin-bottom: 0.5rem;
            text-align: left;
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 2rem 1.5rem;
                border-radius: 15px;
            }

            .login-title {
                font-size: 22px;
            }

            .logo {
                width: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <!-- Logo UBK -->
        <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="Logo" class="logo">

        <h2 class="login-title">Selamat Datang</h2>
        <p class="login-subtitle">Di Website Booking UBK silahkan login terlebih dahulu</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email">Email</label>
                <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password">Password</label>
                <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password"
                    required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>

</html>
