<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pengguna | Rental Mobil</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 20px;
    }

    h2 {
      margin-bottom: 10px;
      color: #333;
    }

    /* ====================== ALERT STATUS ====================== */
    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 25px;
      font-size: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .alert svg { flex-shrink: 0; width: 20px; height: 20px; }

    .alert.info {
      background: #e7f1ff;
      color: #084298;
      border: 1px solid #b6d4fe;
    }
    .alert.warning {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }
    .alert.success {
      background: #d1e7dd;
      color: #0f5132;
      border: 1px solid #badbcc;
    }
    .alert.danger {
      background: #f8d7da;
      color: #842029;
      border: 1px solid #f5c2c7;
    }

    /* ====================== BRAND FILTER ====================== */
    .brand-filter {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 25px;
    }

    .brand-filter button {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 20px;
      padding: 8px 16px;
      cursor: pointer;
      transition: 0.2s;
    }

    .brand-filter button:hover {
      background: #0d6efd;
      color: #fff;
    }

    /* ====================== CAR LIST ====================== */
    .car-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .car-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 12px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 20px;
      padding: 16px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s;
    }

    .car-card:hover {
      transform: translateY(-3px);
    }

    .car-image {
      flex: 0 0 220px;
    }

    .car-image img {
      width: 100%;
      height: auto;
      border-radius: 10px;
      object-fit: cover;
    }

    .car-info {
      flex: 1;
      min-width: 250px;
    }

    .car-info h4 {
      margin: 0;
      font-size: 20px;
      color: #222;
      font-weight: 600;
    }

    .car-info p {
      margin: 4px 0;
      color: #666;
    }

    .price {
      font-size: 22px;
      font-weight: bold;
      color: #000;
      margin-top: 6px;
    }

    .specs {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
    }

    .specs span {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 30px;
      padding: 6px 14px;
      font-size: 13px;
      color: #444;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .heart {
      margin-left: auto;
      font-size: 22px;
      color: #ccc;
      cursor: pointer;
      transition: color 0.2s;
    }

    .heart:hover {
      color: red;
    }
  </style>
</head>
<body>

  {{-- ====================== NOTIFIKASI STATUS VERIFIKASI ====================== --}}
  @php
    $status = Auth::user()->status_verifikasi ?? 'belum_upload';
  @endphp

  @if($status === 'belum_upload')
    <div class="alert warning">
      ⚠️ Anda belum mengunggah KTP dan KK.  
      <a href="{{ route('user.profile') }}" style="color:#0d6efd;font-weight:600;">Klik di sini untuk unggah dokumen.</a>
    </div>
  @elseif($status === 'menunggu')
    <div class="alert info">
      ⏳ Dokumen KTP & KK Anda sedang menunggu verifikasi admin.
    </div>
  @elseif($status === 'disetujui')
    <div class="alert success">
      ✅ Akun Anda telah terverifikasi! Anda bisa mengajukan penyewaan mobil.
    </div>
  @elseif($status === 'ditolak')
    <div class="alert danger">
      ❌ Verifikasi ditolak.  
      Silakan unggah ulang dokumen KTP & KK Anda di halaman profil.
    </div>
  @endif

  {{-- ====================== DAFTAR MEREK ====================== --}}
  <h2>Merek</h2>
  <div class="brand-filter">
    @foreach($brands as $brand)
      <button>{{ $brand->nama_merek }}</button>
    @endforeach
  </div>

  {{-- ====================== DAFTAR MOBIL ====================== --}}
  <h2>Paling Populer</h2>
  <div class="car-list">
    @foreach($cars as $car)
      <div class="car-card">
        <div class="car-image">
          <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->model }}">
        </div>

        <div class="car-info">
          <h4>{{ $car->tahun }} {{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h4>
          <p>Edisi {{ ucfirst($car->warna) ?? '-' }}</p>
          <p class="price">Rp {{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</p>

          <div class="specs">
            <span>🚗 {{ number_format($car->kilometer ?? 0) }} km</span>
            <span>⚙️ {{ ucfirst($car->tipe_transmisi) }}</span>
            <span>👥 {{ $car->capacity->jumlah_orang ?? '-' }} Orang</span>
            <span>⛽ {{ $car->liter_tangki ?? 0 }} Liter</span>
            <span>📍 {{ ucfirst($car->lokasi ?? '-') }}</span>
            <span>🏢 Toyota Auto Center</span>
          </div>
        </div>

        <a href="{{ route('user.cars.show', $car->car_id) }}" class="heart">❤️</a>
      </div>
    @endforeach
  </div>

</body>
</html>
