@extends('partials.container')

@section('title', 'Home')

@section('styles')
<style>
/* ===== Header ===== */
.header {
  background: #000;
  color: #fff;
  width: 95%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  margin-top: -10px;
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
}

.btn-login {
  background: #fff;
  color: #000;
}

.btn-register {
  background: #555;
  color: #fff;
}

.btn-login:hover {
  background: #eaeaea;
}

.btn-register:hover {
  background: #444;
}

/* ===== Carousel ===== */
.carousel {
  width: calc(100% + 3rem);
  margin: 20px 0 0 0;
}

.carousel-inner img {
  width: 100%;
  height: auto;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(100%);
}

/* Scroll container */
.brand-scroll {
  display: flex;
  gap: 0.75rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
  scroll-behavior: smooth;
  padding: 0;
  margin: 0;
  scroll-snap-type: x mandatory;
}

.brand-scroll::-webkit-scrollbar {
  display: none;
}

/* Tombol kapsul */
.brand-btn {
  flex: none;
  padding: 0px 5px;
  background: #fff;
  border: 1.5px solid #000;
  border-radius: 9999px;
  font-size: 13px;
  color: #333;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: background 0.2s ease;
}

.brand-btn:hover {
  background: #f5f5f5;
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
  gap: 8px;
}

.card {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-start;
  width: 110%;
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
  transform: translateY(-4px); /* lebih kecil supaya smooth */
  box-shadow: 0 6px 12px rgba(0,0,0,0.15); /* shadow lebih terasa saat hover */
}

.car-image {
  width: 125px;
  height: auto;
  object-fit: contain;
  flex-shrink: 0; /* agar gambar tidak mengecil */
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
  border-radius: 12px; /* lebih kecil */
  padding: 2px 6px;    /* lebih kecil */
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
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 1rem;
  scroll-snap-type: x mandatory;
}

.choose-scroll::-webkit-scrollbar {
  display: none;
}

.choose-card {
  flex: none;
  width: 150; /* Lebar card lebih kecil */
  border-radius: 12px;
  overflow: hidden;
  scroll-snap-align: start;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.choose-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.choose-card-title {
  background-color: #000;
  color: #fff;
  padding: 8px;
  font-weight: 600;
  font-size: 0.85rem;
  text-align: center;
  font-family: 'Poppins', sans-serif;
}

.choose-card img {
  width: 100%;
  height: 100px; /* Samakan tinggi gambar semua card */
  object-fit: cover;
  border-radius: 0 0 12px 12px;
  display: block;
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
}

</style>
@endsection

@section('content')
<header class="header">
  <h1>Daftar Membership</h1>
  <div>
    <button class="btn btn-login">Login</button>
    <button class="btn btn-register">Register</button>
  </div>
</header>

<!-- Carousel -->
<div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
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

  <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- Bagian Merek -->
<div class="section-container">
  <div class="section-header">
    <h2>Merek</h2>
    <a href="#">See All</a>
  </div>

  <div class="brand-scroll">
    <button class="brand-btn">Toyota</button>
    <button class="brand-btn">Mercedes</button>
    <button class="brand-btn">Tesla</button>
    <button class="brand-btn">Honda</button>
    <button class="brand-btn">BMW</button>
    <button class="brand-btn">Audi</button>
    <button class="brand-btn">Hyundai</button>
  </div>
</div>

<!-- Bagian Paling Populer -->
<div class="section-container">
  <div class="section-header">
    <h2>Paling Populer</h2>
    <a href="#">See All</a>
  </div>

  <div class="card-container">
    <div class="card">
      <img src="images/dmobil1.png" class="car-image" alt="Avanza">
      <div class="card-content">
        <h3>2022 Toyota Avanza 1.3 G</h3>
        <p>Edisi Abu-Abu</p>
        <p class="price">Rp 235.000.000</p>
        <div class="info-tags">
          <div class="tag">20.000 km</div>
          <div class="tag">Otomatis</div>
          <div class="tag">7 Orang</div>
          <div class="tag">45 Liter</div>
          <div class="tag">Jakarta</div>
          <div class="tag">Toyota Auto Center</div>
          <div class="tag">6 Oktober - 25 Oktober 2025</div>
        </div>
      </div>
      <div class="fav-btn">
        <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="heart">
      </div>
    </div>

    <div class="card">
      <img src="images/dmobil2.png" class="car-image" alt="Lamborghini">
      <div class="card-content">
        <h3>2022 Lamborghini Aventador</h3>
        <p>Edisi Abu-Abu</p>
        <p class="price">Rp 235.000.000</p>
        <div class="info-tags">
          <div class="tag">23.000 km</div>
          <div class="tag">Otomatis</div>
          <div class="tag">2 Orang</div>
          <div class="tag">45 Liter</div>
          <div class="tag">Jakarta</div>
          <div class="tag">Auto Center</div>
          <div class="tag">1 November - 8 November 2025</div>
        </div>
      </div>

      <div class="fav-btn">
        <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="heart">
      </div>
    </div>

    <div class="section-container">
  <div class="section-header">
    <h2>Kenapa Memilih Kita?</h2>
  </div> 

<div class="choose-scroll">
  <div class="choose-card">
    <div class="choose-card-title">Armada Lengkap & Terawat</div>
    <img src="images/why1.png" alt="Armada Lengkap & Terawat">
  </div>

  <div class="choose-card">
    <div class="choose-card-title">Layanan Cepat & Mudah</div>
    <img src="images/why2.png" alt="Layanan Cepat & Mudah">
  </div>

  <div class="choose-card">
    <div class="choose-card-title">Jangkauan Luas & Antar Jemput</div>
    <img src="images/why4.png" alt="Jangkauan Luas & Antar Jemput">
  </div>

  <div class="choose-card">
    <div class="choose-card-title">Pilihan Mobil Beragam</div>
    <img src="images/why5.png" alt="Pilihan Mobil Beragam">
  </div>
</div>

  </div>
</div>

@include('partials.bottom-navbar')
@endsection

