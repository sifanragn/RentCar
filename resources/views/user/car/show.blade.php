@extends('partials.container')

@section('title', 'Detail Mobil')

@section('styles')
<style>
/* ===== Container Utama ===== */
.detail-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 8px 12px; /* kanan kiri kecil */
  font-family: 'Poppins', sans-serif;
  color: #000;
}

/* ===== Tombol Kembali ===== */
.back-link {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  font-weight: 500;
  color: #000;
  text-decoration: none;
  transition: color 0.2s;
  width: 100%;
  justify-content: flex-start;
  margin-bottom: 10px;
}
.back-link:hover {
  color: #0077b6;
}
.back-link svg {
  width: 18px;
  height: 18px;
}

/* ===== Gambar Mobil ===== */
.car-image-container {
  width: 100%;
  border-radius: 16px;
  overflow: hidden;
  background: linear-gradient(to bottom, #f3f3f3, #d9d9d9);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 16px 0;
}
.car-image {
  width: 90%;
  height: auto;
  object-fit: contain;
}

/* ===== Informasi Mobil ===== */
.car-info {
  background-color: #222;
  color: #fff;
  border-radius: 24px;
  padding: 16px;
  margin-top: -20px;
  position: relative;
  z-index: 2;
}

.car-info h2 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 4px;
}

.car-info .price {
  background: #fff;
  color: #000;
  display: inline-block;
  padding: 4px 10px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 13px;
  margin-bottom: 10px;
}

.car-info p {
  font-size: 12px;
  line-height: 1.5;
  color: #ddd;
}

/* ===== Section Judul ===== */
.section-title {
  font-size: 14px;
  font-weight: 600;
  margin: 16px 0 8px;
}

/* ===== Tag Grid ===== */
.tag-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 8px;
}

.tag-box {
  background-color: #e5e5e5;
  color: #000;
  border-radius: 12px;
  padding: 10px;
  font-size: 12px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-weight: 500;
}

/* Icon Placeholder */
.tag-box img {
  width: 20px;
  height: 20px;
  margin-bottom: 6px;
}
</style>
@endsection

@section('content')
<div class="detail-container">

  {{-- Tombol Kembali --}}
  <a href="{{ url('/car') }}" class="back-link">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Detail Mobil
  </a>

  {{-- Gambar Mobil --}}
  <div class="car-image-container">
    <img src="/images/dmobil1.png" alt="Avanza" class="car-image">
  </div>

  {{-- Informasi Mobil --}}
  <div class="car-info">
    <h2>2022 Toyota Avanza 1.3 G – Abu Abu</h2>
    <div class="price">Rp 235.000.000</div>
    <p>2022 Toyota Avanza 1.3 G – Abu-Abu adalah MPV yang nyaman, irit, dan cocok untuk perjalanan keluarga atau aktivitas harian dengan tampilan elegan dan modern.</p>

    {{-- Ringkasan Mobil --}}
    <div class="section-title">Ringkasan Mobil</div>
    <div class="tag-grid">
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/1059/1059262.png" alt="">
        20.000 km
      </div>
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/906/906175.png" alt="">
        Otomatis
      </div>
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/883/883746.png" alt="">
        12 Orang
      </div>
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" alt="">
        45 Liter
      </div>
    </div>

    {{-- Spesifikasi Mobil --}}
    <div class="section-title">Spesifikasi Mobil</div>
    <div class="tag-grid">
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/106/106830.png" alt="">
        Mesin 1.3L DOHC Dual VVT-i
      </div>
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/764/764564.png" alt="">
        95 HP
      </div>
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/107/107794.png" alt="">
        Kapasitas bagasi ± 200L
      </div>
      <div class="tag-box">
        <img src="https://cdn-icons-png.flaticon.com/512/61/61456.png" alt="">
        Tersedia Sistem Audio
      </div>
    </div>
  </div>
</div>

@include('partials.bottom-navbar')
@endsection
