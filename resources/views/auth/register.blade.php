@extends('partials.container')

@section('title', 'Register')

@section('styles')
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
  }

  body {
    background: #000;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .container {
    width: 100%;
    max-width: 360px;
    background: #fff;
    padding: 2rem;
    text-align: center;
    border-radius: 10px;
  }

  /* Header */
  .logo {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.2rem;
  }

  .logo img {
    width: 70px;
    height: auto;
    margin-top: 4px;
  }

  .logo h3 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #000;
  }

  .hero {
    width: 100%;
    max-width: 300px;
    margin-left: -70px;
    display: block;
  }

  .title {
    font-size: 25px;
    font-weight: 800;
    color: #000;
    margin-top: 0.5rem;
    margin-bottom: 0.4rem;
  }

  .subtitle {
    font-size: 15px;
    color: #444;
    margin-bottom: 1.8rem;
  }

  /* Form */
  form {
    text-align: left;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
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
    padding: 10px 10px;
    border: 2px solid #ccc;
    font-size: 1rem;
    background: #f9f9f9;
    border-radius: 10px;
    transition: 0.3s;
  }

  .input-group input:focus {
    border-color: #0077b6;
    background: #fff;
    outline: none;
  }

  /* Tombol */
  .btn-register {
    background: #000;
    color: #fff;
    padding: 8px;
    border: none;
    border-radius: 20px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 0.8rem;
  }

  .btn-register:hover {
    background: #222;
  }

.login-text {
  text-align: center;
  margin-top: 20px;
  margin-bottom: 70px; /* tambah ini supaya nggak tertutup navbar */
  font-size: 1rem;
  color: #333;
}

  .login-text a {
    color: #0077b6;
    text-decoration: none;
    font-weight: 600;
  }
</style>
@endsection

@section('content')
  <div class="logo">
    <img src="/images/logo.png" alt="Logo" />
    <h3>Selamat Datang!</h3>
  </div>

  <img src="/images/car1.png" alt="Mobil" class="hero" />

  <h1 class="title">Get Started Free!</h1>
  <p class="subtitle">Ayo Buat Akun Dan Cari Mobilmu</p>
  
<form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="input-group">
      <label for="nama_lengkap">Nama Lengkap</label>
      <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap Anda" required>
    </div>

    <div class="input-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Masukkan username Anda" required>
    </div>

    <div class="input-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
    </div>

    <div class="input-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
    </div>

    <div class="input-group">
      <label for="password_confirmation">Konfirmasi Password</label>
      <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password Anda" required>
    </div>

    <button type="submit" class="btn-register">Daftar</button>

    <p class="login-text">
      Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
    </p>
  </form>
  @include('partials.bottom-navbar')

@endsection
