@extends('partials.container')

@section('title', 'Login')

@section('styles')
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

/* Wrapper */
.login-wrap {
  width: 100%;
  max-width: 390px;
  min-height: 100vh;
  background: #fff;
  padding: 24px 22px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start; /* ❗ kita pakai atas biar ga terlalu di tengah */
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

/* Hero section */
.hero-box {
  width: 100%;
  display: flex;
  justify-content: center;
  margin: 4px auto 10px; /* ✅ jarak atas bawah diperkecil */
}

.hero {
  width: 100%;
  max-width: 330px; /* ✅ ukuran pas, tidak terlalu besar */
  object-fit: contain;
  display: block;
  margin-top: -25px;
}

/* Title section */
.title {
  font-size: 1.55rem;
  font-weight: 700;
  margin-top: 6px;
  margin-bottom: 2px; /* ✅ rapetin */
  text-align: center;
  margin-top: -25px;
}

.subtitle {
  font-size: .9rem;
  color: #666;
  margin-bottom: 14px; /* ✅ lebih rapat */
  text-align: center;
  margin-bottom: 20px;
}

/* Form */
form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 6px;
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

/* Fields sementara simple */
.input-group input {
  width: 100%;
  padding: 11px 12px;
  border: 1.6px solid #d4d4d4;
  border-radius: 10px; /* ✅ bikin lebih smooth */
  background: #fafafa;
}

/* Button */
.btn-login {
  background: #000;
  color: #fff;
  padding: 12px;
  border: none;
  border-radius: 30px;
  font-size: 1rem;
  font-weight: 600;
  margin-top: 10px;
  cursor: pointer;
  transition: .25s;
  margin-bottom: 20px;
}

.btn-login:hover {
  background: #111;
}

/* Bottom link */
.register-text {
  text-align: center;
  font-size: .88rem;
  color: #555;
  margin-top: 8px;
  margin-bottom: 6px; /* ✅ buang space bawah */
}

.register-text a {
  color: #000 !important; /* ✅ jadi hitam */
  font-weight: 600;
  text-decoration: underline;
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
<div class="login-wrap">

  <div class="logo">
    <img src="/images/logo.png" alt="Logo" />
    <h3>Selamat Datang!</h3>
  </div>

  <div class="hero-box">
    <img src="/images/loreg.png" class="hero" alt="">
  </div>

  <h1 class="title">Welcome Back!</h1>
  <p class="subtitle">Silakan login kembali ke akun Anda</p>

  <form action="{{ route('login.submit') }}" method="POST">
    @csrf

    {{-- Error --}}
    @if ($errors->has('login_error'))
      <div style="background:#ffeaea;color:#b30000;padding:8px;border-radius:8px;font-size:.85rem;margin-bottom:6px;">
        {{ $errors->first('login_error') }}
      </div>
    @endif

    <div class="input-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="Masukkan email Anda" required>
    </div>

    <div class="input-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Masukkan password Anda" required>
    </div>

    <button type="submit" class="btn-login">Login</button>
  </form>

  <p class="register-text">Belum punya akun?
    <a href="{{ route('register') }}">Daftar di sini</a>
  </p>

</div>
@endsection
