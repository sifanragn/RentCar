@extends('partials.container')

@section('title', 'Detail Mobil')

@section('styles')
<style>
  body { margin:0; padding:0; }
  .detail-container {
    width: 100%;
    font-family: 'Poppins', sans-serif;
    color: #000;
  }
  .car-image-container {
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    background: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 12px 0;
  }
  .car-image { width:100%; height:auto; object-fit:contain; }
  .car-info {
    background-color:#262625; color:#fff;
    border-radius:20px; padding:16px;
    margin-top:-30px; margin-left:-10px; margin-right:-10px;
    padding-bottom:60px;
  }
  .car-info h2 { font-size:16px; font-weight:600; }
  .car-info .price {
    background:#fff; color:#000;
    display:inline-block;
    padding:4px 10px;
    border-radius:10px;
    font-weight:600;
    font-size:13px;
    margin:8px 0;
  }
  .tag-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(100px,1fr));
    gap:8px; margin-bottom:12px;
  }
  .tag-box {
    background:#e5e5e5; color:#000;
    border-radius:12px; padding:10px;
    font-size:12px; text-align:center;
    display:flex; flex-direction:column; align-items:center;
  }
  .tag-box img { width:20px; height:20px; margin-bottom:6px; }
  .btn-rent {
    display:block; text-align:center;
    background-color:#555;
    color:#fff; border-radius:12px;
    padding:6px 0; font-weight:600;
    margin-top:16px; text-decoration:none;
    transition:background-color 0.3s ease;
  }
  .btn-rent:hover { background-color:#444; }
  .back-link {
    color:#000; text-decoration:none;
    font-weight:250; font-size:15px;
  }
  .back-link:hover { text-decoration:underline; }
  .carousel { width:100%; margin-top:15px; }
  .carousel-inner { border-radius:16px; overflow:hidden; }
  .car-slide-img { width:100%; height:200px; object-fit:contain; }
  .carousel-control-prev-icon,
  .carousel-control-next-icon { filter:invert(100%); }
  .alert {
    margin-top:16px; padding:12px;
    border-radius:10px; font-size:14px;
    background:#fff3cd; color:#856404;
  }
</style>
@endsection

@section('content')
<div class="detail-container">
  {{-- Link Kembali --}}
  <a href="{{ route('user.cars.index') }}" class="back-link">← Kembali ke Daftar Mobil</a>

  {{-- Gambar Mobil (Carousel) --}}
  <div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner rounded-3 shadow-sm">
      <div class="carousel-item active">
        <img src="{{ asset('images/detail1.png') }}" class="d-block w-100 car-slide-img" alt="Mobil 1">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('images/detail2.png') }}" class="d-block w-100 car-slide-img" alt="Mobil 2">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('images/detail3.png') }}" class="d-block w-100 car-slide-img" alt="Mobil 3">
      </div>
      <div class="carousel-item">
        <img src="{{ asset('images/detail4.png') }}" class="d-block w-100 car-slide-img" alt="Mobil 4">
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  {{-- Informasi Mobil --}}
  <div class="car-info">
    <h2>{{ $car->tahun }} {{ $car->brand->nama_merek ?? '-' }} {{ $car->model }} – {{ ucfirst($car->warna) }}</h2>
    <div class="price">Rp {{ number_format($car->harga_sewa_per_hari,0,',','.') }}</div>
    <p>{{ $car->deskripsi ?? '-' }}</p>

    {{-- Spesifikasi --}}
    <div class="section-title">Spesifikasi Mobil</div>
    <div class="tag-grid">
      <div class="tag-box"><img src="{{ asset('images/km.png') }}" alt=""> {{ number_format($car->kilometer ?? 0) }} km</div>
      <div class="tag-box"><img src="{{ asset('images/transmisi.png') }}" alt=""> {{ ucfirst($car->tipe_transmisi) }}</div>
      <div class="tag-box"><img src="{{ asset('images/kapasitas.png') }}" alt=""> {{ $car->capacity->jumlah_orang ?? '-' }} Orang</div>
      <div class="tag-box"><img src="{{ asset('images/tangki.png') }}" alt=""> {{ $car->liter_tangki ?? 0 }} Liter</div>
    </div>

    {{-- Detail Teknis --}}
    <div class="section-title">Detail Teknis</div>
    <div class="tag-grid">
      <div class="tag-box"><img src="{{ asset('images/mesin.png') }}" alt=""> Mesin {{ $car->mesin ?? '-' }}</div>
      <div class="tag-box"><img src="{{ asset('images/power.png') }}" alt=""> {{ $car->horse_power ?? '-' }} HP</div>
      <div class="tag-box"><img src="{{ asset('images/bagasi.png') }}" alt=""> Bagasi ± {{ $car->bagasi ?? '-' }} L</div>
      <div class="tag-box"><img src="{{ asset('images/audio.png') }}" alt=""> {{ $car->sistem_audio ? 'Ada Audio' : 'Tanpa Audio' }}</div>
    </div>

    {{-- Tombol Sewa dengan Logika Status --}}
@php
  $rental = \App\Models\Rental::where('car_id', $car->car_id)
      ->whereIn('status_rental', ['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
      ->latest()
      ->first();
@endphp

@if($car->status_mobil === 'tidak_tersedia')
  @if($rental && $rental->status_rental === 'menunggu_pembayaran')
    <div class="alert">
      💰 Mobil ini sedang <b>menunggu pembayaran penyewa</b>.  
      Silakan pilih mobil lain terlebih dahulu.
    </div>
  @else
    <div class="alert">
      🚫 Mobil ini sedang disewa oleh pengguna lain.  
      Silakan pilih mobil lain.
    </div>
  @endif
@else
  @auth
    <a href="{{ route('user.rentals.create', $car->car_id) }}" class="btn-rent">Sewa Sekarang</a>
  @else
    <a href="{{ route('login') }}" class="btn-rent"
      onclick="return confirm('Kamu perlu login dulu untuk menyewa mobil. Mau login sekarang?')">
      Sewa Sekarang
    </a>
  @endauth
@endif


  </div>
</div>

@include('partials.bottom-navbar')
@endsection
