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

/* Wrapper */
.register-wrap {
  width: 100%;
  max-width: 390px;
  min-height: 100vh;
  background: #fff;
  padding: 24px 22px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: 6px;
  justify-content: center;
  margin-bottom: 4px;
}

.logo img {
  width: 40px;
}

.logo h3 {
  font-size: 1.05rem;
  font-weight: 600;
}

/* Hero */
.hero-box {
  width: 100%;
  display: flex;
  justify-content: center;
  margin: 4px auto 10px;
}

.hero {
  width: 100%;
  max-width: 330px;
  object-fit: contain;
  display: block;
  margin-top: -18px;
}

/* Titles */
.title {
  font-size: 1.55rem;
  font-weight: 700;
  margin-top: -10px;
  text-align: center;
  margin-bottom: 4px;
}

.subtitle {
  font-size: .9rem;
  color: #666;
  text-align: center;
  margin-bottom: 16px;
}

/* Form */
form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 5px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.input-group label {
  font-size: .88rem;
  font-weight: 600;
  color: #222;
}

.input-group input {
  width: 100%;
  padding: 11px 12px;
  border: 1.6px solid #d4d4d4;
  border-radius: 10px;
  background: #fafafa;
  font-size: .92rem;
}

/* Button */
.btn-register {
  background: #000;
  color: #fff;
  padding: 12px;
  border: none;
  border-radius: 30px;
  font-size: 1rem;
  font-weight: 600;
  margin-top: 6px;
  cursor: pointer;
  transition: .25s;
  margin-bottom: 20px;
}

.btn-register:hover {
  background: #111;
}

/* Bottom text */
.login-text {
  text-align: center;
  font-size: .88rem;
  color: #555;
  margin-top: 8px;
  margin-bottom: 6px;
}

.login-text a {
  color: #000 !important;
  font-weight: 600;
  text-decoration: underline;
}

/* Notification box */
.alert-custom {
  background: #ffecec;
  color: #a40000;
  border: 1px solid #ffb3b3;
  border-radius: 10px;
  padding: 10px 12px;
  margin-bottom: 12px;
  font-size: .85rem;
}

/* ✅ remove card layout feel */
.app-container {
  background: #fff !important;
  padding-top: 0 !important;
  border-left: none !important;
  border-right: none !important;
}
</style>
@endsection

@section('content')
<div class="register-wrap">

  <div class="logo">
    <img src="/images/logo.png" alt="Logo">
    <h3>Selamat Datang!</h3>
  </div>

  <div class="hero-box">
    <img src="/images/loreg.png" class="hero" alt="Hero">
  </div>

  <h1 class="title">Get Started Free!</h1>
  <p class="subtitle">Ayo Buat Akun Dan Cari Mobilmu</p>

  @if ($errors->any())
    <div class="alert-custom">
      @foreach ($errors->all() as $error)
        ⚠️ {{ $error }}<br>
      @endforeach
    </div>
  @endif

  <form action="{{ route('register.store') }}" method="POST">
    @csrf

    <div class="input-group">
      <label>Nama Lengkap</label>
      <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap Anda" required>
    </div>

    <div class="input-group">
      <label>Username</label>
      <input type="text" name="username" placeholder="Masukkan username Anda" required>
    </div>

    <div class="input-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="Masukkan email Anda" required>
    </div>

    <div class="input-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Masukkan password Anda" required>
    </div>

    <div class="input-group">
      <label>Konfirmasi Password</label>
      <input type="password" name="password_confirmation" placeholder="Ulangi password Anda" required>
    </div>

    <button type="submit" class="btn-register">Daftar</button>
  </form>

  <p class="login-text">
    Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
  </p>

</div>
@endsection
