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
  margin-top: -25px;
}

/* Title */
.title {
  font-size: 1.55rem;
  font-weight: 700;
  margin-top: -25px;
  margin-bottom: 2px;
  text-align: center;
}
.subtitle {
  font-size: .9rem;
  color: #666;
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
.input-group input {
  width: 100%;
  padding: 11px 12px;
  border: 1.6px solid #d4d4d4;
  border-radius: 10px;
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

/* Bottom text */
.register-text {
  text-align: center;
  font-size: .88rem;
  color: #555;
  margin-top: 8px;
}
.register-text a {
  color: #000 !important;
  font-weight: 600;
  text-decoration: underline;
}

/* Remove card frame */
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

{{-- ALERTS --}}
@if(session('error'))
  <div style="background:#ffdddd;color:#b80000;padding:10px;border-radius:8px;margin-bottom:12px;font-weight:500;text-align:center;">
      ⚠️ {{ session('error') }}
  </div>
@endif

@if(session('success'))
  <div style="background:#ddffdd;color:#0d8500;padding:10px;border-radius:8px;margin-bottom:12px;font-weight:500;text-align:center;">
      ✅ {{ session('success') }}
  </div>
@endif

{{-- Logo --}}
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

  {{-- Error login --}}
  @if ($errors->has('login_error'))
    <div style="background:#ffeaea;color:#b30000;padding:8px;border-radius:8px;font-size:.85rem;margin-bottom:6px;">
      {{ $errors->first('login_error') }}
    </div>
  @endif

  {{-- Email / No HP --}}
  <div class="input-group">
    <label for="login_id">Email / No HP</label>
    <input 
      type="text" 
      id="login_id" 
      name="login_id" 
      placeholder="Email atau No HP"
      value="{{ old('login_id') }}" 
      required 
    />
  </div>

  {{-- Password --}}
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
