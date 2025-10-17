@extends('partials.container')

@section('title', 'Daftar / Login Dulu')

@section('content')
<div class="guest-container">
  <!-- Background Bulatan -->
  <div class="bg-circles">
    <div class="circle circle-left"></div>
    <div class="circle-gradient circle-left-gradient"></div>
    <div class="circle circle-right"></div>
    <div class="circle-gradient circle-right-gradient"></div>
  </div>

  <!-- Logo -->
  <div class="logo-container">
    <img src="{{ asset('images/logo-rental.png') }}" alt="Rental Logo">
  </div>

  <!-- Ilustrasi -->
  <div class="illustration">
    <img src="{{ asset('images/login-illustration.png') }}" alt="Illustration">
  </div>

  <!-- Teks -->
  <h1 class="title">Daftar/Login Terlebih Dahulu</h1>
  <p class="subtitle">yuk cek profile kamu dengan bergabung bersama kami secara gratis!</p>

  <!-- Tombol -->
  <div class="button-container">
    <a href="{{ route('login') }}" class="btn">Login</a>
    <a href="{{ route('register') }}" class="btn">Daftar</a>
  </div>
</div>

@include('partials.bottom-navbar')
@endsection

@section('styles')
<style>
/* ===== Container Utama ===== */
.guest-container {
  width: 100%;
  min-height: 100vh;
  background: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 32px 16px;
  position: relative;
  overflow: hidden;
}

/* ===== Background Bulatan ===== */
.bg-circles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.circle {
  width: 180px;
  height: 165px;
  background: #3E3E3E;
  opacity: 0.5;
  border-radius: 50%;
  position: absolute;
}

.circle-gradient {
  width: 266px;
  height: 259px;
  background: linear-gradient(180deg, black 0%, white 100%);
  opacity: 0.5;
  border-radius: 50%;
  position: absolute;
}

/* Posisi kiri */
.circle-left {
  left: 0;
  top: 35px;
}

.circle-left-gradient {
  left: 87.55px;
  top: 300.73px;
  transform: rotate(-98deg);
  transform-origin: top left;
}

/* Posisi kanan */
.circle-right {
  left: 344.68px;
  top: 275.54px;
  transform: rotate(-178deg);
  transform-origin: top left;
}

.circle-right-gradient {
  left: 264.84px;
  top: 7.4px;
  transform: rotate(-277deg);
  transform-origin: top left;
}

/* ===== Logo ===== */
.logo-container img {
  width: 160px;
  margin-bottom: 24px;
}

/* ===== Ilustrasi ===== */
.illustration img {
  width: 260px;
  max-width: 80%;
  height: auto;
  margin: 16px 0 32px;
}

/* ===== Teks ===== */
.title {
  color: #000;
  font-family: 'Poppins', sans-serif;
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 8px;
}

.subtitle {
  color: #000;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 24px;
  max-width: 320px;
  margin-left: auto;
  margin-right: auto;
}

/* ===== Tombol ===== */
.button-container {
  display: flex;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
}

.btn {
  width: 140px;
  background: #151515;
  color: white;
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 16px;
  border-radius: 20px;
  padding: 12px 0;
  text-decoration: none;
  display: inline-block;
  transition: background 0.3s ease;
}

.btn:hover {
  background: #000;
}
</style>
@endsection
