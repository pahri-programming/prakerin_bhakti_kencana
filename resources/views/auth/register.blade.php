<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Bhakti Kencana</title>
    <link rel="shortcut icon" href="{{ asset('assets/backend/images/logos/ubk.jpg') }}" type="image/jpg" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background: linear-gradient(120deg, #ffcc00, #ff8800, #ff5722);
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: #fff;
            width: 950px;
            max-width: 95%;
            height: 600px;
            border-radius: 25px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .left-side {
            flex: 1;
            background: linear-gradient(160deg, #ff9800, #ff5722);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .left-side img {
            width: 120px;
            margin-bottom: 1rem;
        }

        .left-side h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .left-side p {
            font-size: 14px;
            max-width: 300px;
            text-align: center;
            opacity: 0.9;
        }

        .right-side {
            flex: 1.3;
            background-color: #fff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h3 {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
        }

        p.subtitle {
            color: #777;
            font-size: 14px;
            margin-bottom: 2rem;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 1.2rem;
            transition: 0.3s;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #ff9800;
            box-shadow: 0 0 5px rgba(255, 152, 0, 0.4);
        }

        .btn-register {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(to right, #ff9800, #ff5722);
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            background: linear-gradient(to right, #ffb300, #e65100);
        }

        .bottom-text {
            text-align: center;
            margin-top: 1rem;
            font-size: 14px;
            color: #555;
        }

        .bottom-text a {
            color: #ff5722;
            font-weight: 600;
            text-decoration: none;
        }

        .bottom-text a:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            color: red;
            font-size: 13px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        @media (max-width: 850px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .left-side {
                display: none;
            }

            .right-side {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Bagian kiri -->
        <div class="left-side">
            <img src="{{ asset('assets/backend/images/logos/ubk2.png') }}" alt="Logo">
            <h2>Universitas Bhakti Kencana</h2>
            <p>Gabung bersama kami dan mulai lakukan booking ruangan dengan mudah dan cepat.</p>
        </div>

        <!-- Bagian kanan -->
        <div class="right-side">
            <h3>Buat Akun Baru</h3>
            <p class="subtitle">Daftar untuk mulai melakukan booking ruangan</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <label for="name">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="password-confirm">Konfirmasi Password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required>

                <button type="submit" class="btn-register">Daftar</button>
            </form>

            <p class="bottom-text">
                Sudah punya akun?
                <a href="{{ route('login') }}">Login di sini</a>
            </p>
        </div>
    </div>
</body>

</html>
