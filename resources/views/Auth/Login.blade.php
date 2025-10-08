<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #000000;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      width: 100%;
      max-width: 360px;
      background: #fff;
      padding: 1.5rem;
      text-align: center;
    }

    /* Logo + Judul atas */
    .logo {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.2rem;
    }

    .logo img {
      width: 55px;
      height: auto;
    }

    .logo h2 {
      font-size: 1.3rem;
      font-weight: 600;
      color: #004d40;
    }

    /* Gambar mobil */
    .hero {
      width: 100%;
      max-width: 300px;
      margin: 0 auto 1.8rem;
      display: block;
    }

    /* Welcome text */
    .title {
      font-size: 2rem;
      font-weight: 800;
      color: #013220;
      margin-bottom: 0.4rem;
    }

    .subtitle {
      font-size: 1rem;
      color: #444;
      margin-bottom: 2rem;
    }

    /* Form */
    form {
      text-align: left;
      display: flex;
      flex-direction: column;
      gap: 1.3rem;
    }

    .input-group label {
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 0.4rem;
      color: #222;
      display: block;
    }

    .input-group input {
      width: 100%;
      padding: 0.9rem 1rem;
      border-radius: 14px;
      border: 2px solid #ccc;
      font-size: 1rem;
      background: #f9f9f9;
      transition: 0.3s;
    }

    .input-group input:focus {
      border-color: #0077b6;
      background: #fff;
      outline: none;
    }

    /* Tombol */
    .btn-login {
      background: #000;
      color: #fff;
      padding: 1rem;
      border: none;
      border-radius: 20px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 0.5rem;
    }

    .btn-login:hover {
      background: #222;
    }

    /* Link daftar */
    .register-text {
      text-align: center;
      margin-top: 1.8rem;
      font-size: 1rem;
      color: #333;
    }

    .register-text a {
      color: #0077b6;
      text-decoration: none;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Logo dan teks atas -->
    <div class="logo">
      <img src="/images/logo.png" alt="Logo" />
      <h2>Selamat Datang!</h2>
    </div>

    <!-- Gambar mobil -->
    <img src="/images/car1.png" alt="Mobil" class="hero" />

    <!-- Welcome text -->
    <h1 class="title">Welcome Back!</h1>
    <p class="subtitle">Silakan login kembali ke akun Anda</p>

    <!-- Form -->
    <form action="{{ route('login') }}" method="POST">
      @csrf
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required />
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required />
      </div>

      <button type="submit" class="btn-login">Login</button>

      <p class="register-text">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
      </p>
    </form>
  </div>
</body>
</html>
