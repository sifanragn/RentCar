<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Mobil | Rental Mobil</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;margin:0;padding:20px; }
    h2 { color:#222;margin-bottom:20px; }

    form { display:flex;flex-wrap:wrap;gap:10px;margin-bottom:25px; }
    select,input[type="date"],input[type="text"]{
      padding:8px 12px;border-radius:8px;border:1px solid #ccc;font-size:14px;background:white;min-width:160px;
    }
    button { background:#2ecc71;color:white;border:none;border-radius:8px;padding:8px 14px;cursor:pointer; }
    button:hover { background:#27ae60; }

    .car-list { display:flex;flex-direction:column;gap:20px; }
    .car-card {
      background:white;border:1px solid #ddd;border-radius:12px;display:flex;align-items:center;
      gap:16px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.08);transition:transform 0.2s;position:relative;
    }
    .car-card:hover { transform:translateY(-3px); }
    .car-card img { width:200px;border-radius:8px;object-fit:cover; }
    .car-info { flex:1; }
    .car-info h4 { margin:0;font-size:18px;color:#222;font-weight:600; }
    .car-info p { margin:4px 0;color:#555; }
    .price { font-weight:bold;color:#000;margin-top:5px;font-size:20px; }
    .specs { display:flex;flex-wrap:wrap;gap:8px;margin-top:8px; }
    .specs span {
      background:#fff;border:1px solid #ccc;border-radius:30px;padding:6px 12px;font-size:13px;color:#444;
    }
    .unavailable {
      position:absolute;top:10px;left:10px;background:rgba(220,53,69,0.9);
      color:white;padding:5px 10px;border-radius:6px;font-size:13px;
    }
    .pending {
      position:absolute;top:10px;left:10px;background:rgba(255,193,7,0.9);
      color:#222;padding:5px 10px;border-radius:6px;font-size:13px;font-weight:600;
    }
    .car-card.disabled { opacity:0.6; }

    .suggest-box {
      margin-top:30px;padding:20px;background:#fff3cd;border:1px solid #ffeeba;border-radius:10px;
    }
    .suggest-box h3 { margin-top:0;color:#856404; }
    .suggest-list { list-style:none;padding-left:0;margin-top:10px; }
    .suggest-list li { margin-bottom:6px; }
    .suggest-list a { text-decoration:none;color:#0d6efd; }
    .suggest-list a:hover { text-decoration:underline; }
  </style>
</head>
<body>

  <h2>Daftar Mobil</h2>

  {{-- 🔍 Filter Pencarian --}}
  <form method="GET" action="{{ route('user.cars.index') }}">
    <select name="brand_id">
      <option value="">Pilih Merek</option>
      @foreach($brands as $brand)
        <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
          {{ $brand->nama_merek }}
        </option>
      @endforeach
    </select>

    <input type="date" name="tanggal" value="{{ request('tanggal') }}">
    <select name="capacity_id">
      <option value="">Kapasitas</option>
      @foreach($capacities as $cap)
        <option value="{{ $cap->capacity_id }}" {{ request('capacity_id') == $cap->capacity_id ? 'selected' : '' }}>
          {{ $cap->jumlah_orang }} Orang
        </option>
      @endforeach
    </select>

    <input type="text" name="search" placeholder="Cari model..." value="{{ request('search') }}">
    <button type="submit">Cari</button>
  </form>

  {{-- 🚗 Daftar Mobil --}}
  <div class="car-list">
    @php
      $availableCars = [];
      $unavailableCars = [];
    @endphp

    @foreach($cars as $car)
      @php
        $rental = \App\Models\Rental::where('car_id', $car->car_id)
            ->whereIn('status_rental', ['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
            ->latest()
            ->first();
        $isUnavailable = !!$rental;
      @endphp

      <div class="car-card {{ $isUnavailable ? 'disabled' : '' }}">
        <div style="position:relative;">
          <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->model }}">
          @if($rental)
            @if($rental->status_rental === 'menunggu_pembayaran')
              <div class="pending">💰 Menunggu Pembayaran Penyewa</div>
            @else
              <div class="unavailable">🚫 Sedang Disewa</div>
            @endif
          @endif
        </div>

        <div class="car-info">
          <h4>{{ $car->tahun }} {{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h4>
          <p>Edisi {{ ucfirst($car->warna) ?? '-' }}</p>
          <p class="price">Rp {{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</p>
          <div class="specs">
            <span>⚙️ {{ ucfirst($car->tipe_transmisi) }}</span>
            <span>👥 {{ $car->capacity->jumlah_orang ?? '-' }} Orang</span>
            <span>⛽ {{ $car->liter_tangki ?? 0 }} Liter</span>
            <span>📍 {{ ucfirst($car->lokasi ?? '-') }}</span>
          </div>

          @if(!$isUnavailable)
            <a href="{{ route('user.cars.show', $car->car_id) }}" 
              style="display:inline-block;background:#0d6efd;color:white;padding:6px 12px;border-radius:6px;text-decoration:none;margin-top:10px;">
              🚗 Lihat Detail
            </a>
          @else
            <button disabled style="background:#6c757d;color:white;padding:6px 12px;border:none;border-radius:6px;margin-top:10px;">
              Tidak Tersedia
            </button>
          @endif
        </div>
      </div>
    @endforeach

    {{-- Rekomendasi kalau semua hasil sedang disewa --}}
    @if(count($cars) > 0 && count($availableCars) === 0)
      <div class="suggest-box">
        <h3>Semua mobil di filter kamu sedang disewa 😢</h3>
        <p>Berikut beberapa mobil lain yang masih tersedia:</p>
        <ul class="suggest-list">
          @foreach($suggestions as $sug)
            <li>
              🚗 <a href="{{ route('user.cars.show', $sug->car_id) }}">
                {{ $sug->brand->nama_merek ?? '-' }} {{ $sug->model }}
              </a> — Rp {{ number_format($sug->harga_sewa_per_hari, 0, ',', '.') }}
            </li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

</body>
</html>
