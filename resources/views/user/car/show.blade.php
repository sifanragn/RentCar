@extends('partials.container')

@section('title', 'Detail Mobil')

@section('styles')
<style>
  body {
  margin: 0;
  padding: 0;
}

.detail-container {
  width: 100%;
  max-width: 100%;
  margin: 0;
  padding: 0; /* biar full kiri-kanan */
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

.car-image {
  width: 100%;
  height: auto;
  object-fit: contain;
}

.car-info {
  background-color: #262625;
  color: #fff;
  border-radius: 20px;
  padding: 16px;
  margin-top: -30px;
  margin-left: -10px;
  margin-right: -10px;
  padding-bottom: 60px;
}

.car-info h2 {
  font-size: 16px;
  font-weight: 600;
}

.car-info .price {
  background: #fff;
  color: #000;
  display: inline-block;
  padding: 4px 10px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 13px;
  margin: 8px 0;
}

.tag-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 8px;
  margin-bottom: 12px;
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
}

.tag-box img {
  width: 20px;
  height: 20px;
  margin-bottom: 6px;
}

  .btn-rent {
    display: block;
    text-align: center;
    background-color: #555; /* abu-abu elegan */
    color: #fff;
    border-radius: 12px;
    padding: 6px 0;
    font-weight: 600;
    margin-top: 16px;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }

  .btn-rent:hover {
    background-color: #444; /* sedikit lebih gelap saat hover */
  }

.back-link {
  color: #000; /* warna hitam */
  text-decoration: none; /* hilangkan garis bawah */
  font-weight: 250; /* biar sedikit tegas */
  font-size: 15px;
}

.back-link:hover {
  text-decoration: underline; /* efek hover halus */
}

.carousel {
  width: 100%;
  margin-top: 15px;
}

.carousel-inner {
  border-radius: 16px;
  overflow: hidden;
}

.car-slide-img {
  width: 100%;
  height: 200px; /* naikkan biar proporsional */
  object-fit: contain; /* biar gambar nggak kepotong */
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(100%);
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

    {{-- Spesifikasi Mobil --}}
    <div class="section-title">Spesifikasi Mobil</div>
    <div class="tag-grid">
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/1059/1059262.png" alt=""> {{ number_format($car->kilometer ?? 0) }} km</div>
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/906/906175.png" alt=""> {{ ucfirst($car->tipe_transmisi) }}</div>
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/883/883746.png" alt=""> {{ $car->capacity->jumlah_orang ?? '-' }} Orang</div>
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" alt=""> {{ $car->liter_tangki ?? 0 }} Liter</div>
    </div>

    {{-- Detail Teknis --}}
    <div class="section-title">Detail Teknis</div>
    <div class="tag-grid">
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/106/106830.png" alt=""> Mesin {{ $car->mesin ?? '-' }}</div>
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/764/764564.png" alt=""> {{ $car->horse_power ?? '-' }} HP</div>
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/107/107794.png" alt=""> Kapasitas bagasi ± {{ $car->bagasi ?? '-' }} L</div>
      <div class="tag-box"><img src="https://cdn-icons-png.flaticon.com/512/61/61456.png" alt=""> {{ $car->sistem_audio ? 'Tersedia Sistem Audio' : 'Tidak Ada' }}</div>
    </div>

    {{-- Tombol Sewa --}}
@auth
<a href="{{ route('user.rentals.create', $car->car_id) }}" class="btn-rent">Sewa Sekarang</a>
@else
  <a href="{{ route('login') }}" class="btn-rent"
     onclick="return confirm('Kamu perlu login dulu untuk menyewa mobil. Mau login sekarang?')">
    Sewa Sekarang
  </a>
@endauth  
</div>
</div>

@include('partials.bottom-navbar')
@endsection
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $car->brand->nama_merek ?? 'Detail Mobil' }}</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;margin:0;padding:20px; }
    .container { max-width:800px;margin:auto;background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    img { width:100%;border-radius:12px;margin-bottom:20px;object-fit:cover; }
    h2 { margin-bottom:10px;color:#222; }
    p { margin:5px 0;color:#555; }
    .price { font-size:22px;color:#000;font-weight:bold;margin-top:10px; }
    .btn {
      display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;
      border-radius:8px;margin-top:20px;font-weight:600;
    }
    .btn:hover { background:#0056b3; }
    .alert {
      margin-top:20px;padding:15px;border-radius:8px;
    }
    .alert-warning {
      background:#fff3cd;border:1px solid #ffeeba;color:#856404;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="{{ asset('storage/'.$car->foto) }}" alt="{{ $car->model }}">
    <h2>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h2>
    <p>Tahun: {{ $car->tahun }}</p>
    <p>Warna: {{ ucfirst($car->warna ?? '-') }}</p>
    <p>Transmisi: {{ ucfirst($car->tipe_transmisi ?? '-') }}</p>
    <p>Kapasitas: {{ $car->capacity->jumlah_orang ?? '-' }} orang</p>
    <p>Lokasi: {{ ucfirst($car->lokasi ?? '-') }}</p>
    <p class="price">Rp {{ number_format($car->harga_sewa_per_hari,0,',','.') }} / hari</p>

    @php
      $rental = \App\Models\Rental::where('car_id', $car->car_id)
          ->whereIn('status_rental', ['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
          ->latest()
          ->first();
    @endphp

    @if($rental && $rental->status_rental === 'menunggu_pembayaran')
      <div class="alert alert-warning">
        ⚠️ Mobil ini sedang <b>menunggu pembayaran penyewa</b>.  
        Silakan pilih mobil lain terlebih dahulu.
      </div>
    @elseif($rental)
      <div class="alert alert-warning">
        🚫 Mobil ini sedang disewa oleh pengguna lain.  
        Silakan pilih mobil lain.
      </div>
    @else
      <a href="{{ route('user.rentals.create', $car->car_id) }}" class="btn">🚗 Sewa Mobil Ini</a>
    @endif
  </div>
</body>
</html>
