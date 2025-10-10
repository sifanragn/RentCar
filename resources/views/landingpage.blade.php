<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil - Selamat Datang</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #007bff, #00b4d8);
            color: #fff;
            text-align: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        .btn-container {
            display: flex;
            gap: 15px;
        }
        a {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255,255,255,0.4);
            transition: all 0.3s ease;
        }
        a:hover {
            background-color: #fff;
            color: #007bff;
        }
        footer {
            position: absolute;
            bottom: 15px;
            font-size: 0.85rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <h1>Selamat Datang di Rental Mobil</h1>
    <p>Sewa mobil mudah, cepat, dan terpercaya hanya di sini.</p>

    <div class="btn-container">
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
    </div>

    <footer>
        &copy; {{ date('Y') }} Rental Mobil. All rights reserved.
    </footer>

</body>
</html>
