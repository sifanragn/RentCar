@extends('partials.container')

@section('title', 'Home')

@section('styles')
<style>
/* ===== Wrapper Scroll ===== */
.filter-wrapper {
  width: 100%;
  overflow-x: auto; /* bisa di-scroll ke samping */
  padding: 10px 0;
  margin-top: -20px; /* dinaikin ke atas */
}
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

/* ===== Bar Horizontal ===== */
.filter-bar-horizontal {
  display: flex;
  align-items: center; /* sejajarin semua item */
  gap: 10px;
  width: max-content; /* biar panjang menyesuaikan isi */
  padding: 10px;
  margin: 0 auto;
}

/* ===== Input & Select ===== */
.filter-select,
.filter-input {
  width: 120px;
  border: 1px solid #000;
  border-radius: 10px;
  padding: 5px 5px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  background: #fff;
  color: #000;
  transition: 0.2s;
  flex-shrink: 0; /* biar tidak mengecil saat di-scroll */
}

.filter-input[type="date"] {
  cursor: pointer;
}

.filter-input:focus,
.filter-select:focus {
  border-color: #0077b6;
  box-shadow: 0 0 4px rgba(0, 119, 182, 0.3);
  outline: none;
}

/* ===== Tombol Search ===== */
.btn-search {
  background-color: #22c55e;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 5px 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
  flex-shrink: 0;
}

.btn-search:hover {
  background-color: #16a34a;
}

/* ===== Scrollbar Style (optional) ===== */
.filter-wrapper::-webkit-scrollbar {
  height: 6px;
}
.filter-wrapper::-webkit-scrollbar-thumb {
}

/* ===== Card Mobil ===== */
.card-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.card-link {
  text-decoration: none;
  color: inherit;
  display: flex;
  justify-content: center; /* center-kan card */
  width: 100%;
}

.card-link:visited,
.card-link:hover,
.card-link:active {
  color: inherit;
  text-decoration: none;
}

.card {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-start;
  width: 100%;
  max-width: 700px;
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

.btn-see-other {
  display: inline-block;
  background-color: #4299e1;  /* biru */
  color: white;
  font-weight: 500;
  font-size: 12px;             /* lebih kecil */
  padding: 4px 12px;           /* lebih compact */
  border-radius: 6px;          /* agak bulat */
  text-decoration: none;
  margin: 5px 0 10px 15px;     /* atas-kanan-bawah-kiri */
  transition: 0.3s;
  margin-left: 40px;
  margin-top: 0px;
}

.btn-see-other:hover {
  background-color: #2b6cb0;
}


/* ===== Responsive ===== */
@media (max-width: 480px) {
  .card {
    width: 95%;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .car-image {
    width: 80%;
  }

  .card-content h3 {
    font-size: 13px;
  }

  .card-content p,
  .price {
    font-size: 12px;
  }

  .info-tags {
    justify-content: center;
  }

  .no-result {
  text-align: center;
  margin: 40px 0;
}

}
</style>
@endsection

@section('content')
 <div class="filter-wrapper">
  <form method="GET" action="{{ route('user.cars.index') }}">
    <div class="filter-bar-horizontal">

      {{-- Brand --}}
      <select name="brand_id" class="filter-select">
        <option value="">Semua Merek</option>
        @foreach($brands as $brand)
          <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
            {{ $brand->nama_merek }}
          </option>
        @endforeach
      </select>

      {{-- Tanggal sewa --}}
      <input type="date" name="date" class="filter-input" value="{{ request('date') }}">

      {{-- Capacity --}}
      <select name="capacity_id" class="filter-select">
        <option value="">Semua Kapasitas</option>
        @foreach($capacities as $capacity)
          <option value="{{ $capacity->capacity_id }}" {{ request('capacity_id') == $capacity->capacity_id ? 'selected' : '' }}>
            {{ $capacity->jumlah_orang }} Orang
          </option>
        @endforeach
      </select>

      {{-- Cari model --}}
      <input type="text" name="search" class="filter-input" placeholder="Model Mobil" value="{{ request('search') }}">

      {{-- Tombol Search --}}
      <button type="submit" class="btn-search">Search</button>

    </div>
  </form>
</div>

<div class="card-container">
  @foreach ($cars as $car)
    <a href="{{ route('user.cars.show', $car->car_id) }}" class="card-link">
      <div class="card">
        <img src="{{ asset('images/dmobil1.png') }}" alt="Mobil" class="car-image">
        <div class="card-content">
          <h3>{{ $car->tahun }} {{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
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
          <p class="price">Rp {{ number_format($car->harga_sewa_per_hari,0,',','.') }}</p>
          <div class="info-tags">
            <div class="tag">{{ number_format($car->kilometer ?? 0) }} km</div>
            <div class="tag">{{ ucfirst($car->tipe_transmisi) }}</div>
            <div class="tag">{{ $car->capacity->jumlah_orang ?? '-' }} Orang</div>
            <div class="tag">{{ $car->liter_tangki ?? 0 }} Liter</div>
            <div class="tag">{{ ucfirst($car->lokasi ?? '-') }}</div>
            <div class="tag">{{ $car->dealer ?? 'Auto Center' }}</div>
            <div class="tag">{{ \Carbon\Carbon::parse($car->tanggal_mulai)->format('j M') }} - {{ \Carbon\Carbon::parse($car->tanggal_selesai)->format('j M Y') }}</div>
          </div>
        </div>
        <div class="fav-btn">
          <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="heart">
        </div>
      </div>
    </a>
  @endforeach
</div>

<div class="card-container">
  @if($cars->isNotEmpty())
    @foreach($cars as $car)
      <a href="{{ route('user.cars.show', $car->car_id) }}" class="card-link">
        {{-- Card mobil --}}
      </a>
    @endforeach
  @else
    <div class="no-result">
      <p style="color: red; font-size: 12px; margin-bottom: 15px;">
  Kriteria yang Anda cari tidak tersedia.
</p>
      <a href="{{ route('user.cars.index') }}" class="btn-see-other">Lihat Mobil Lain</a>
    </div>

    {{-- Bisa tampilkan suggestions juga --}}
    @if($suggestions->isNotEmpty())
      <div class="suggestion-container">
        @foreach($suggestions as $car)
          <a href="{{ route('user.cars.show', $car->car_id) }}" class="card-link">
            {{-- Card mobil saran --}}
          </a>
        @endforeach
      </div>
    @endif
  @endif
</div>

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

  @include('partials.bottom-navbar')
@endsection
