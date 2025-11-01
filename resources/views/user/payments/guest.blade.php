@extends('partials.container')

@section('title', 'Daftar / Login Dulu')

@section('content')

<!-- ===== Konten Utama ===== -->
<div class="guest-page">

  <!-- Logo -->
  <div class="logo-wrapper fade-in">
    <img src="{{ asset('images/logo.png') }}" alt="Logo Rental">
  </div>

  <!-- Ilustrasi -->
  <div class="illustration-wrapper fade-in-delay">
    <img src="{{ asset('images/mobilguest.png') }}" alt="Ilustrasi Login">
  </div>

  <!-- Teks -->
  <div class="text-section fade-in-delay2">
    <h1>Daftar / Login Terlebih Dahulu</h1>
    <p>Yuk cek profil kamu dan nikmati kemudahan rental mobil bersama kami secara gratis!</p>
  </div>

  <!-- Tombol -->
  <div class="button-row fade-in-delay3">
    <a href="{{ route('login') }}" class="btn btn-solid">Login</a>
    <a href="{{ route('register') }}" class="btn btn-outline">Daftar</a>
  </div>

</div>

@endsection

@section('styles')
<style>
/* ===== Layout Umum ===== */
body {
  background: linear-gradient(180deg, #f9fafb 0%, #ffffff 80%);
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
}

/* ===== Tampilan Halaman ===== */
.guest-page {
  width: 100%;
  max-width: 430px;
  margin: 0 auto;
  padding: 10px 24px 90px; /* dikurangi dari 60px biar naik ke atas */
  min-height: 90vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

/* ===== Logo ===== */
.logo-wrapper {
  margin-bottom: 32px;
  margin-left: -15px;
}

.logo-wrapper img {
  width: 150px;
  height: auto;
  filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.1));
}

/* ===== Ilustrasi ===== */
.illustration-wrapper {
  margin-bottom: 32px;
}

.illustration-wrapper img {
  width: 270px;
  max-width: 90%;
  height: auto;
  transition: transform 0.3s ease;
}

.illustration-wrapper img:hover {
  transform: translateY(-4px);
}

/* ===== Teks ===== */
.text-section {
  margin-bottom: 36px;
  padding: 0 8px;
}

.text-section h1 {
  font-size: 20px;
  font-weight: 700;
  color: #222;
  margin-bottom: 10px;
}

.text-section p {
  font-size: 14px;
  font-weight: 500;
  color: #666;
  line-height: 1.6;
  max-width: 330px;
  margin: 0 auto;
}

/* ===== Tombol Sejajar ===== */
.button-row {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  z-index: 1;
  width: 100%;
  max-width: 300px;
  margin-bottom: -40px;
}

.btn {
  flex: 1;
  border-radius: 22px;
  padding: 12px 0;
  font-size: 15px;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  transition: all 0.25s ease;
  display: inline-block;
}

/* Tombol Login (Solid) */
.btn-solid {
  background: #111;
  color: #fff;
}

.btn-solid:hover {
  background: #000;
  color: #fff;
  transform: translateY(-2px);
}

/* Saat diklik di HP tetap putih */
.btn-solid:active,
.btn-solid:focus {
  background: #000;
  color: #fff !important;
  opacity: 0.9;
  transform: scale(0.98);
}

/* Tombol Daftar (Outline) */
.btn-outline {
  border: 2px solid #111;
  color: #111;
  background: transparent;
}

.btn-outline:hover {
  background: #111;
  color: #fff;
  transform: translateY(-2px);
}

/* Saat diklik di HP */
.btn-outline:active,
.btn-outline:focus {
  background: #111;
  color: #fff !important;
  opacity: 0.95;
  transform: scale(0.98);
}

/* ===== Responsif ===== */
@media (max-width: 480px) {
  .guest-page {
    padding: 50px 16px 100px;
  }

  .logo-wrapper img { width: 130px; }
  .illustration-wrapper img { width: 230px; }
  .text-section h1 { font-size: 18px; }
  .text-section p { font-size: 13px; }
  .button-row { max-width: 260px; gap: 14px; }
  .btn { font-size: 13.5px; padding: 10px 0; }
}
</style>
@endsection
