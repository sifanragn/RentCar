@extends('partials.container')

@section('title', 'Home')

@section('styles')
<style>
/* ===== Header ===== */
.header {
  background: linear-gradient(to right, #000, #333);
  color: #fff;
  width: 95%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 14px;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  margin: 10px auto;
}

.btn-register {
  background: transparent;
  border: 1px solid #fff;
  color: #fff;
  padding: 4px 10px;
  border-radius: 8px;
  transition: 0.3s ease;
}

.btn-register:hover {
  background: #fff;
  color: #000;
}

.header h1 {
  font-size: 13px;
  font-weight: 500;
  margin-left: 10px;
  margin-top: 5px; /* tambah jarak ke bawah */
}

/* ===== Tombol Header ===== */
.btn {
  border: none;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  line-height: 1;
    text-decoration: none; /* tambahkan ini */
  display: inline-block; 
}

.header a {
  text-decoration: none; /* hilangkan garis bawah */
  display: inline-block;  /* biar bisa kasih padding & border */
}

.btn-login {
  background: #fff;
  color: #000;
}

.btn-login:hover {
  background: #eaeaea;
}

/* ===== Carousel ===== */
.carousel {
width: 100%;
  margin: 0 auto;
  margin-top: 12px;
}

.carousel-inner img {
  border-radius: 16px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}


.carousel-inner {
  width: 100%;
}

/* ===== Nav Buttons ===== */
.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(100%);
}

/* ===== Bullets indikator ===== */
.carousel-indicators {
  bottom: -30px;
}

.carousel-indicators [data-bs-target] {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #D9D9D9;
  opacity: 0.5;
  border: none;
  transition: all 0.3s ease;
  margin: 0 4px;
}

.carousel-indicators .active {
  opacity: 1;
  background-color: #000;
  transform: scale(1.25);
}

/* Scroll otomatis merek */
.brand-scroll {
  display: flex;
  gap: 0.75rem;
  overflow: hidden; /* sembunyikan area di luar */
  position: relative;
  padding: 8px 0;
}

.brand-marquee {
  display: flex;
  gap: 0.75rem;
  animation: scrollBrands 20s linear infinite;
}

@keyframes scrollBrands {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

/* Efek hover hentikan animasi */
.brand-scroll:hover .brand-marquee {
  animation-play-state: paused;
}

/* Tombol kapsul */
.brand-btn {
  flex: none;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: #fff;
  border: 1px solid #444;
  border-radius: 9999px;
  font-size: 13px;
  color: #222;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 2px rgba(0,0,0,0.08);
  cursor: pointer;
  transition: all 0.25s ease;
}

.brand-btn:hover {
  background: #f7f7f7;
  border-color: #777;
  color: #000;
  transform: translateY(-1px);
  box-shadow: 0 3px 6px rgba(0,0,0,0.08);
}

.brand-logo {
  width: 20px;
  height: 20px;
  object-fit: contain;
  border-radius: 4px;
  flex-shrink: 0;
}


/* Efek klik (pressed) */
.brand-btn:active {
  background: #ededed;
  transform: translateY(0);
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.12);
}


.section-container {
  width: 100%;
  margin: 24px auto;
  padding: 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0 16px 0.75rem 16px;
  margin-left: -3px;
}

.section-header h2 {
  font-size: 20px;
  font-weight: 550;
  color: #000;
  font-family: 'Poppins', sans-serif;
  margin-top: -10px;
}

.section-header a {
  font-size: 13px;
  color: #444;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  margin-right: -20px;
}

.section-header a:hover {
  text-decoration: underline;
}

/* ===== Card Mobil ===== */
.card-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
  padding: 0 5px; /* ✅ kasih jarak kiri kanan biar ga nempel layar */
}

.card {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-start;
  width: 100%; /* ✅ full tapi tidak nabrak karena ada padding container */
  max-width: 600px;
  border: 1px solid #ccc;
  border-radius: 20px;
  overflow: hidden;
  background-color: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  position: relative;
  padding: 8px;
  gap: 10px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.car-image {
  width: 125px;
  height: auto;
  object-fit: contain;
  flex-shrink: 0;
}

.card-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.card-content h3 {
  margin: 0;
  font-size: 12px; 
  font-weight: 600;
}

.card-content p {
  margin: 2px 0;
  color: #555;
  font-size: 11px;
}

.price {
  font-weight: 600;
  font-size: 13px;
  color: black;
  margin-top: 3px;
}

.info-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 3px;
  margin-top: 6px;
}

.tag {
  border: 1px solid #000;
  border-radius: 12px;
  padding: 2px 6px;
  font-size: 9px;
  display: flex;
  align-items: center;
  gap: 3px;
}

/* tombol love */
.fav-btn {
  position: absolute;
  top: 10px;
  right: 12px;
  background: #fff;
  border-radius: 50%;
  border: 1px solid #ccc;
  width: 26px;
  height: 26px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.fav-btn img {
  width: 13px;
  height: 13px;
}

.choose-scroll {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 8px 4px 18px;
  scroll-snap-type: x mandatory;
}

.choose-scroll::-webkit-scrollbar {
  display: none;
}

.choose-card {
  flex: none;
  width: 160px;
  border-radius: 14px;
  overflow: hidden;
  background: #fff;
  border: 1px solid #e5e5e5;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.25s ease;
  scroll-snap-align: start;
}

.choose-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
}

.choose-card-title {
  font-size: 13px;
  font-weight: 600;
  text-align: center;
  color: #111;
  background: #fff;
  padding: 10px 8px 4px;
  font-family: 'Poppins', sans-serif;
}

.choose-card img {
  width: 100%;
  height: 90px;
  object-fit: cover;
  border-top: 1px solid #eee;
  border-radius: 0 0 14px 14px;
}


@media (max-width: 480px) {
  .card {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .car-image {
    width: 80%;
  }

  .card-content {
    padding: 8px 0 0 0;
  }

    .carousel {
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
  }

}

.alert-verif {
  background: #fff3f3;
  border: 1px solid #f5c2c7;
  color: #b52d3a;
  font-size: 13px;
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 8px 10px 0;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.alert-verif a {
  color: #b52d3a;
  font-weight: 600;
  text-decoration: underline;
}

/* ===== RESPONSIVE OPTIMIZATION ===== */
@media (max-width: 768px) {
  /* Judul Section */
  .section-header h2 {
    font-size: 18px;
    margin-top: 0;
  }

  /* Card Mobil */
  .card-container {
    padding: 0 2vw; /* ✅ rapetin jarak kanan kiri */
    gap: 12px;      /* sedikit lebih rapat antar card */
  }

  .card {
    flex-direction: row;
    align-items: center;
    text-align: left;
    width: 100%;
    max-width: none;
    margin: 0 auto;
    border-radius: 16px;
    padding: 10px 8px; /* sedikit lebih kecil padding-nya */
  }

  .car-image {
    width: 110px;
    height: auto;
    margin-right: 8px;
    object-fit: contain;
  }

  .card-content {
    flex: 1;
    padding: 0;
  }

  .card-content h3 {
    font-size: 12px;
    margin-bottom: 2px;
  }

  .card-content p {
    font-size: 11px;
  }

  .price {
    font-size: 12px;
  }

  .info-tags {
    gap: 3px;
  }

  .tag {
    font-size: 8.8px;
    padding: 1.5px 5px;
  }

  /* Heart button */
  .fav-btn {
    width: 24px;
    height: 24px;
    top: 8px;
    right: 10px;
  }

  .fav-btn img {
    width: 12px;
    height: 12px;
  }

  /* Carousel full width dengan radius halus */
  .carousel {
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
  }

  .carousel-inner img {
    border-radius: 0;
  }

  /* Brand scroll */
  .brand-scroll {
    overflow-x: auto;
    padding: 6px 0;
  }

  /* Section spacing */
  .section-container {
    margin-top: 18px;
  }

  /* Kenapa Memilih Kita */
  .choose-card {
    width: 140px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .choose-card-title {
    font-size: 12px;
  }

  .choose-card img {
    height: 80px;
  }
}


/* Tambahan agar tampilan tetap proporsional di layar sangat kecil */
@media (max-width: 380px) {
  .car-image {
    width: 95px;
  }
  .card-content h3 {
    font-size: 11px;
  }
  .price {
    font-size: 11px;
  }
  .tag {
    font-size: 8px;
  }
}



</style>
@endsection

@section('content')
@include('partials.verification-alert')

<header class="header">
  <h1>Daftar Membership</h1>

  @guest
    <div>
      <a href="{{ route('login') }}">
        <button class="btn btn-login">Login</button>
      </a>
      <a href="{{ route('register') }}">
        <button class="btn btn-register">Register</button>
      </a>
    </div>
  @else
    <div>
      <a href="{{ route('logout') }}"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <button class="btn btn-register">Logout</button>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </div>
  @endguest
</header>

<!-- Carousel -->
<div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
  <!-- 🔘 Indikator bulat di bawah -->
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>

  <div class="carousel-inner rounded-3 shadow-sm">
    <div class="carousel-item active">
      <img src="/images/slide1.png" class="d-block w-100" alt="Slide 1">
    </div>
    <div class="carousel-item">
      <img src="/images/slide2.png" class="d-block w-100" alt="Slide 2">
    </div>
    <div class="carousel-item">
      <img src="/images/slide3.png" class="d-block w-100" alt="Slide 3">
    </div>
  </div>
</div>


<!-- Bagian Merek -->
<div class="section-container">
  <div class="section-header">
    <h2>Merek</h2>
  </div>

  <div class="brand-scroll">
  <div class="brand-marquee">
    @foreach($brands as $brand)
      <button class="brand-btn">
        @if($brand->logo)
          <img src="{{ asset('img/brand_logos/' . $brand->logo) }}" alt="{{ $brand->nama_merek }}" class="brand-logo">
        @endif
        <span>{{ $brand->nama_merek }}</span>
      </button>
    @endforeach

    {{-- duplikasi untuk looping seamless --}}
    @foreach($brands as $brand)
      <button class="brand-btn">
        @if($brand->logo)
          <img src="{{ asset('img/brand_logos/' . $brand->logo) }}" alt="{{ $brand->nama_merek }}" class="brand-logo">
        @endif
        <span>{{ $brand->nama_merek }}</span>
      </button>
    @endforeach
  </div>
</div>

</div>

<!-- Bagian Paling Populer -->
<div class="section-container">
  <div class="section-header">
    <h2>Paling Populer</h2>
    <a href="{{ route('user.cars.index') }}">See All</a>
  </div>

  <div class="card-container">
    @foreach($popularCars as $car)
    {{-- Link ke detail mobil --}}
    <a href="{{ route('user.cars.show', $car->car_id) }}" style="text-decoration: none; color: inherit;">
      <div class="card">
        <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->nama }}" class="car-image">
        <div class="card-content">
          <h3>{{ $car->tahun }} {{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
          <p>Edisi {{ ucfirst($car->warna) ?? '-' }}</p>
          <p class="price">Rp {{ number_format($car->harga_sewa_per_jam, 0, ',', '.') }}</p>
          <div class="info-tags">
            <div class="tag">{{ number_format($car->kilometer ?? 0) }} km</div>
            <div class="tag">{{ ucfirst($car->tipe_transmisi) }}</div>
            <div class="tag">{{ $car->capacity->jumlah_orang ?? '-' }} Orang</div>
            <div class="tag">{{ $car->liter_tangki ?? 0 }} Liter</div>
            <div class="tag">{{ ucfirst($car->lokasi ?? '-') }}</div>
            <div class="tag">{{ $car->dealer ?? 'Auto Center' }}</div>
            <div class="tag">
              {{ \Carbon\Carbon::parse($car->tanggal_mulai)->format('j M') }} -
              {{ \Carbon\Carbon::parse($car->tanggal_selesai)->format('j M Y') }}
            </div>
          </div>
        </div>
        <div class="fav-btn">
          <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="heart">
        </div>
      </div>
    </a>
    @endforeach
  </div>
</div>

<div class="section-container">
  <div class="section-header">
    <h2>Kenapa Memilih Kita?</h2>
  </div>

  <div class="choose-scroll">
    <div class="choose-card">
      <div class="choose-card-title">Armada Lengkap & Terawat</div>
      <img src="{{ asset('images/why1.png') }}" alt="Armada Lengkap & Terawat">
    </div>

    <div class="choose-card">
      <div class="choose-card-title">Layanan Cepat & Mudah</div>
      <img src="{{ asset('images/why2.png') }}" alt="Layanan Cepat & Mudah">
    </div>

    <div class="choose-card">
      <div class="choose-card-title">Jangkauan Luas & Antar Jemput</div>
      <img src="{{ asset('images/why4.png') }}" alt="Jangkauan Luas & Antar Jemput">
    </div>

    <div class="choose-card">
      <div class="choose-card-title">Pilihan Mobil Beragam</div>
      <img src="{{ asset('images/why5.png') }}" alt="Pilihan Mobil Beragam">
    </div>
  </div>
</div>
@include('partials.bottom-navbar')
@endsection

