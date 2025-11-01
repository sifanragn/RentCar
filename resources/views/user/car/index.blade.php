@extends('partials.container')

@section('title', 'Home')

@section('styles')
<style>
.filter-wrapper {
  width: 100%;
  overflow-x: auto;
  padding: 10px 12px; /* tambah padding horizontal */
  margin-top: -10px;
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.filter-wrapper::-webkit-scrollbar { display: none; }

/* ===== Filter Bar ===== */
.filter-bar-horizontal {
  display: flex;
  align-items: center;
  gap: 8px;
  width: max-content;
  padding: 6px 10px;
  margin: 0 auto;
}
.filter-select,
.filter-input {
  width: 110px;
  border: 1px solid #000;
  border-radius: 8px;
  padding: 4px 6px;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  background: #fff;
  color: #000;
  transition: 0.2s;
  flex-shrink: 0;
}
.filter-input[type="date"] { cursor: pointer; }
.filter-input:focus,
.filter-select:focus {
  border-color: #0077b6;
  box-shadow: 0 0 4px rgba(0, 119, 182, 0.3);
  outline: none;
}
.btn-search {
  background-color: #22c55e;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 5px 12px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
  flex-shrink: 0;
}
.btn-search:hover { background-color: #16a34a; }

/* ===== Card Mobil ===== */
.car-list {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.car-card {
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
  margin-left: -15px;
}

.car-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.car-image {
  width: 125px;
  height: auto;
  object-fit: contain;
  flex-shrink: 0;
}

.car-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.car-info h4 {
  margin: 0;
  font-size: 12px;
  font-weight: 600;
}
.car-info p {
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

/* Tag info */
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

/* Status label */
.unavailable, .pending {
  position: absolute;
  top: 6px;
  left: 6px;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.unavailable {
  background: rgba(220,53,69,0.9);
  color: white;
}
.pending {
  background: rgba(255,193,7,0.9);
  color: #222;
}

/* Tombol love */
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

/* 🔹 Tombol “Lihat Mobil Lain” – Hitam elegan */
.btn-see-other {
  display: inline-block;
  background-color: #000;  /* hitam utama */
  color: #fff;             /* teks putih */
  padding: 5px 10px;
  border: 2px solid #000;  /* outline hitam */
  border-radius: 6px;
  text-decoration: none;
  font-size: 12px;
  transition: 0.3s ease;
  margin-left: 40px;
}
.btn-see-other:hover {
  background-color: #222;  /* abu gelap saat hover */
  transform: scale(1.05);
}

/* Responsive */
@media (max-width: 480px) {
  .car-card {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .car-image { width: 80%; }
  .car-info { padding: 8px 0 0 0; }
}
</style>
@endsection


@section('content')

{{-- Filter Bar --}}
<div class="filter-wrapper">
  <form method="GET" action="{{ route('user.cars.index') }}">
    <div class="filter-bar-horizontal">
      <select name="brand_id" class="filter-select">
        <option value="">Semua Merek</option>
        @foreach($brands as $brand)
          <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
            {{ $brand->nama_merek }}
          </option>
        @endforeach
      </select>

      <input type="date" name="date" class="filter-input" value="{{ request('date') }}">
      <select name="capacity_id" class="filter-select">
        <option value="">Semua Kapasitas</option>
        @foreach($capacities as $capacity)
          <option value="{{ $capacity->capacity_id }}" {{ request('capacity_id') == $capacity->capacity_id ? 'selected' : '' }}>
            {{ $capacity->jumlah_orang }} Orang
          </option>
        @endforeach
      </select>

      <input type="text" name="search" class="filter-input" placeholder="Model Mobil" value="{{ request('search') }}">
      <button type="submit" class="btn-search">Search</button>
    </div>
  </form>
</div>

{{-- Daftar Mobil (Tampilan disamakan dengan Home) --}}
<div class="car-list">
  @foreach($cars as $car)
    @php
      $rental = \App\Models\Rental::where('car_id', $car->car_id)
          ->whereIn('status_rental', ['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
          ->latest()
          ->first();
    @endphp

    <a href="{{ route('user.cars.show', $car->car_id) }}" style="text-decoration:none; color:inherit;">
      <div class="car-card">
        <div style="position:relative;">
          <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->nama }}" class="car-image">
          @if($rental)
            @if($rental->status_rental === 'menunggu_pembayaran')
              <div class="pending">💰 Menunggu Pembayaran Penyewa</div>
            @else
              <div class="unavailable">🚫 Sedang Disewa</div>
            @endif
          @endif@extends('partials.container')

@section('title', 'Home')

@section('styles')
<style>
.filter-wrapper {
  width: 100%;
  overflow-x: auto;
  padding: 10px 12px; /* tambah padding horizontal */
  margin-top: -10px;
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.filter-wrapper::-webkit-scrollbar { display: none; }

/* ===== Filter Bar ===== */
.filter-bar-horizontal {
  display: flex;
  align-items: center;
  gap: 8px;
  width: max-content;
  padding: 6px 10px;
  margin: 0 auto;
}
.filter-select,
.filter-input {
  width: 110px;
  border: 1px solid #000;
  border-radius: 8px;
  padding: 4px 6px;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  background: #fff;
  color: #000;
  transition: 0.2s;
  flex-shrink: 0;
}
.filter-input[type="date"] { cursor: pointer; }
.filter-input:focus,
.filter-select:focus {
  border-color: #0077b6;
  box-shadow: 0 0 4px rgba(0, 119, 182, 0.3);
  outline: none;
}
.btn-search {
  background-color: #22c55e;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 5px 12px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
  flex-shrink: 0;
}
.btn-search:hover { background-color: #16a34a; }

/* ===== Card Mobil ===== */
.car-list {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.car-card {
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
  margin-left: -15px;
}

.car-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.car-image {
  width: 125px;
  height: auto;
  object-fit: contain;
  flex-shrink: 0;
}

.car-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.car-info h4 {
  margin: 0;
  font-size: 12px;
  font-weight: 600;
}
.car-info p {
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

/* Tag info */
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

/* Status label */
.unavailable, .pending {
  position: absolute;
  top: 6px;
  left: 6px;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
}
.unavailable {
  background: rgba(220,53,69,0.9);
  color: white;
}
.pending {
  background: rgba(255,193,7,0.9);
  color: #222;
}

/* Tombol love */
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

/* 🔹 Tombol “Lihat Mobil Lain” – Hitam elegan */
.btn-see-other {
  display: inline-block;
  background-color: #000;  /* hitam utama */
  color: #fff;             /* teks putih */
  padding: 5px 10px;
  border: 2px solid #000;  /* outline hitam */
  border-radius: 6px;
  text-decoration: none;
  font-size: 12px;
  transition: 0.3s ease;
  margin-left: 40px;
}
.btn-see-other:hover {
  background-color: #222;  /* abu gelap saat hover */
  transform: scale(1.05);
}

/* Responsive */
@media (max-width: 480px) {
  .car-card {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .car-image { width: 80%; }
  .car-info { padding: 8px 0 0 0; }
}
</style>
@endsection


@section('content')

{{-- Filter Bar --}}
<div class="filter-wrapper">
  <form method="GET" action="{{ route('user.cars.index') }}">
    <div class="filter-bar-horizontal">
      <select name="brand_id" class="filter-select">
        <option value="">Semua Merek</option>
        @foreach($brands as $brand)
          <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
            {{ $brand->nama_merek }}
          </option>
        @endforeach
      </select>

      <input type="date" name="date" class="filter-input" value="{{ request('date') }}">
      <select name="capacity_id" class="filter-select">
        <option value="">Semua Kapasitas</option>
        @foreach($capacities as $capacity)
          <option value="{{ $capacity->capacity_id }}" {{ request('capacity_id') == $capacity->capacity_id ? 'selected' : '' }}>
            {{ $capacity->jumlah_orang }} Orang
          </option>
        @endforeach
      </select>

      <input type="text" name="search" class="filter-input" placeholder="Model Mobil" value="{{ request('search') }}">
      <button type="submit" class="btn-search">Search</button>
    </div>
  </form>
</div>

{{-- Daftar Mobil --}}
<div class="car-list">
  @foreach($cars as $car)
    @php
      $rental = \App\Models\Rental::where('car_id', $car->car_id)
          ->whereIn('status_rental', ['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
          ->latest()
          ->first();
    @endphp

    <a href="{{ route('user.cars.show', $car->car_id) }}" style="text-decoration:none; color:inherit;">
      <div class="car-card">
        <div style="position:relative;">
          <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->nama }}" class="car-image">
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

          {{-- ✅ Harga per jam --}}
          <p class="price">
            Rp {{ number_format($car->harga_sewa_per_jam ?? 0, 0, ',', '.') }} / jam
          </p>

          <div class="info-tags">
            <div class="tag">{{ number_format($car->kilometer ?? 0) }} km</div>
            <div class="tag">{{ ucfirst($car->tipe_transmisi) }}</div>
            <div class="tag">{{ $car->capacity->jumlah_orang ?? '-' }} Orang</div>
            <div class="tag">{{ $car->liter_tangki ?? 0 }} Liter</div>
            <div class="tag">{{ ucfirst($car->lokasi ?? '-') }}</div>
            <div class="tag">{{ $car->dealer ?? 'Auto Center' }}</div>
          </div>
        </div>

        <div class="fav-btn">
          <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="heart">
        </div>
      </div>
    </a>
  @endforeach
</div>

@if($cars->isEmpty())
  <div class="no-result">
    <p style="color: red; font-size: 12px; margin-bottom: 15px;">Kriteria yang Anda cari tidak tersedia.</p>
    <a href="{{ route('user.cars.index') }}" class="btn-see-other">Lihat Mobil Lain</a>
  </div>
@endif

@include('partials.bottom-navbar')
@endsection

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

@if($cars->isEmpty())
  <div class="no-result">
    <p style="color: red; font-size: 12px; margin-bottom: 15px;">Kriteria yang Anda cari tidak tersedia.</p>
    <a href="{{ route('user.cars.index') }}" class="btn-see-other">Lihat Mobil Lain</a>
  </div>
@endif

@include('partials.bottom-navbar')
@endsection
