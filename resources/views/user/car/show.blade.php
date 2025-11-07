@extends('partials.container')

@section('title', 'Detail Mobil')

@section('styles')
<style>
body {
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  background: #fff;
  color: #000;
  overflow-x: hidden;
}

/* ===== CONTAINER ===== */
.detail-container {
  width: 100%;
  position: relative;
  overflow-x: hidden;
}

/* ===== Gambar Mobil ===== */
.carousel {
  position: relative;
  overflow: hidden;
  border-radius: 0 0 20px 20px;
}
.carousel-inner {
  border-radius: 0 0 20px 20px;
  overflow: hidden;
}
.car-slide-img {
  width: 100%;
  height: 250px;
  object-fit: contain;
  transition: transform 0.8s ease, opacity 0.6s ease;
}
.carousel-item.active .car-slide-img {
  transform: scale(1.02);
  opacity: 1;
}
.carousel-item-next .car-slide-img,
.carousel-item-prev .car-slide-img {
  transform: scale(1.05);
  opacity: 0.9;
}

/* Panah kiri-kanan carousel */
.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(100%);
}

/* ===== Tombol kembali ===== */
.back-link {
  position: absolute;
  top: 12px;
  left: 15px;
  background: rgba(255,255,255,0.85);
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.3s;
  z-index: 10;
  text-decoration: none;
}
.back-link:hover {
  background: #000;
}
.back-link:hover svg {
  stroke: #fff;
}
.back-link svg {
  stroke: #000;
  transition: 0.3s;
}

/* ===== INFO MOBIL ===== */
.car-info {
  background: #262625;
  color: #fff;
  border-radius: 18px 18px 0 0;
  margin: -14px auto 0;
  width: 96%; /* 🔹 lebih lebar, tapi masih aman dalam layout */
  padding: 22px 16px 40px;
  animation: fadeSlideUp 0.6s ease forwards;
  box-shadow: 0 -3px 8px rgba(0,0,0,0.25);
  max-width: 600px; /* biar tetep proporsional di desktop */
}

@keyframes fadeSlideUp {
  0% { opacity: 0; transform: translateY(15px); }
  100% { opacity: 1; transform: translateY(0); }
}

.car-info h2 {
  font-size: 15.8px;
  font-weight: 600;
  line-height: 1.4;
  margin: 0 0 6px;
}

.car-info .price {
  background: #fff;
  color: #000;
  display: inline-block;
  padding: 4px 10px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 12.8px;
  margin: 8px 0 14px;
  animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

/* ===== SECTION TITLE ===== */
.section-title {
  font-weight: 600;
  font-size: 13.5px;
  margin: 14px 0 8px;
  border-left: 3px solid #fff;
  padding-left: 8px;
}

/* ===== GRID INFO ===== */
.tag-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 6px;
}

.tag-box {
  background: #f4f4f4;
  color: #000;
  border-radius: 10px;
  padding: 9px 6px;
  font-size: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  transition: all 0.25s ease;
}
.tag-box:hover {
  transform: translateY(-2px);
  background: #ececec;
  box-shadow: 0 3px 8px rgba(255,255,255,0.25);
}
.tag-box img {
  width: 18px;
  height: 18px;
  margin-bottom: 5px;
  opacity: 0.9;
  transition: 0.2s ease;
}
.tag-box:hover img {
  opacity: 1;
  transform: scale(1.06);
}

/* ===== Tombol Sewa Sekarang ===== */
.btn-rent {
  display: block;
  width: 85%;
  max-width: 330px;
  margin: 22px auto 0;
  text-align: center;
  background-color: #555;     /* 🔹 abu tua elegan */
  color: #fff;
  border-radius: 10px;
  padding: 10px 0;
  font-weight: 600;
  font-size: 14px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(0,0,0,0.25);
}

.btn-rent:hover {
  background-color: #666;     /* 🔹 sedikit lebih terang saat hover */
  transform: translateY(-2px);
  box-shadow: 0 5px 14px rgba(0,0,0,0.35);
}


/* ===== Alert (mobil disewa) ===== */
.alert {
  margin-top: 16px;
  padding: 10px;
  border-radius: 10px;
  font-size: 13.5px;
  background: #fff3cd;
  color: #856404;
  animation: fadeIn 0.5s ease;
}


  
</style>
@endsection

@section('content')
<div class="detail-container">

  {{-- 🔙 Tombol Kembali --}}
  <a href="{{ route('user.cars.index') }}" class="back-link" aria-label="Kembali">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
      <line x1="19" y1="12" x2="5" y2="12"/>
      <polyline points="12 19 5 12 12 5"/>
    </svg>
  </a>

  {{-- 🖼️ Gambar Mobil (Carousel Dinamis) --}}
  <div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner rounded-3 shadow-sm">

      {{-- Foto Utama --}}
      <div class="carousel-item active">
        <img src="{{ asset('storage/' . $car->foto) }}" class="d-block w-100 car-slide-img" alt="Foto Mobil Utama">
      </div>

      {{-- Foto Tambahan --}}
      @if($car->photos && $car->photos->count())
        @foreach($car->photos as $photo)
          <div class="carousel-item">
            <img src="{{ asset('storage/' . $photo->path) }}" class="d-block w-100 car-slide-img" alt="Foto Tambahan">
          </div>
        @endforeach
      @endif
    </div>

    {{-- Navigasi Carousel --}}
    @if($car->photos && $car->photos->count() > 0)
      <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    @endif
  </div>

  {{-- 🧾 Informasi Mobil --}}
  <div class="car-info">
    <h2>{{ $car->tahun }} {{ $car->brand->nama_merek ?? '-' }} {{ $car->model }} – {{ ucfirst($car->warna) }}</h2>
    
    {{-- 💰 Harga per jam --}}
    <div class="price">
      Rp {{ number_format($car->harga_sewa_per_jam ?? 0, 0, ',', '.') }}
      <small>/ jam</small>
    </div>

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

    {{-- Tombol Sewa --}}
    @if($car->status === 'tidak_tersedia')
      <div class="alert">
        🚫 Mobil ini sedang disewa oleh pengguna lain.<br>Silakan pilih mobil lain.
      </div>
    @else
      @auth
        <a href="{{ route('user.rentals.create', $car->car_id) }}" class="btn-rent">Sewa Sekarang</a>
      @else
        <a href="{{ route('user.car.guest') }}" class="btn-rent">Sewa Sekarang</a>
      @endauth
    @endif
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const carInfo = document.querySelector(".car-info");
  const carImg = document.querySelectorAll(".car-slide-img");

  // efek parallax halus saat scroll
  window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    carImg.forEach(img => {
      img.style.transform = `translateY(${scrollY * 0.2}px) scale(1.02)`;
    });
  });

  // animasi muncul smooth
  carInfo.style.opacity = 0;
  setTimeout(() => {
    carInfo.style.transition = "opacity 0.6s ease";
    carInfo.style.opacity = 1;
  }, 250);
});
</script>


@include('partials.bottom-navbar')
@endsection
