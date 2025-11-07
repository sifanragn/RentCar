@extends('partials.container')

@section('title', 'Daftar Mobil')

@section('styles')
<style>
/* ===== Filter Bar ===== */
.filter-wrapper {
  width: 100%;
  overflow-x: auto;
  padding: 10px 12px;
  margin-top: -10px;
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.filter-wrapper::-webkit-scrollbar { display: none; }

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
  font-size: 12px;
  background: #fff;
  color: #000;
  transition: .2s;
  flex-shrink: 0;
}
.filter-input[type=date]{cursor:pointer;}
.filter-input:focus,
.filter-select:focus{
  border-color:#0077b6;
  box-shadow:0 0 4px rgba(0,119,182,.3);
  outline:none;
}

.btn-search {
  background-color: #333; /* abu item elegan */
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 5px 12px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
}
.btn-search:hover {
  background-color: #111; /* lebih hitam saat hover */
}


/* ===== Card Mobil ===== */
.car-list {
  display:flex;
  flex-direction:column;
  gap:15px;
  padding:0 5px;
}

.car-card{
  display:flex;
  align-items:center;
  gap:10px;
  width:100%;
  border:1px solid #ccc;
  border-radius:20px;
  background:#fff;
  box-shadow:0 2px 6px rgba(0,0,0,.1);
  padding:8px;
  position:relative;
  transition:.3s;
}
.car-card:hover{
  transform:translateY(-4px);
  box-shadow:0 6px 12px rgba(0,0,0,.15);
}

.car-image{
  width:125px;
  object-fit:contain;
  border-radius:12px;
}

/* Info */
.car-info{
  flex:1;
  display:flex;
  flex-direction:column;
}
.car-info h4{
  font-size:12px;
  margin:0;
  font-weight:600;
}
.car-info p{
  margin:2px 0;
  font-size:11px;
  color:#555;
}
.price{
  font-weight:600;
  font-size:13px;
  margin-top:3px;
}

/* Tags */
.info-tags{
  display:flex;
  flex-wrap:wrap;
  gap:3px;
  margin-top:6px;
}
.tag{
  border:1px solid #000;
  border-radius:12px;
  padding:2px 6px;
  font-size:9px;
}

/* Favourite Button */
.fav-btn{
  position:absolute;
  top:10px;
  right:12px;
  background:#fff;
  border:1px solid #ccc;
  border-radius:50%;
  width:26px;height:26px;
  display:flex;
  align-items:center;
  justify-content:center;
}
.fav-btn img{
  width:13px;height:13px;
}

/* Status badge (lebih elegan) */
.unavailable, .pending {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 6px 12px;
  border-radius: 30px;
  font-size: 11px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
}

/* Warna untuk Sedang Disewa */
.unavailable {
  background: #ff4d4d;
  color: #fff;
}

/* Warna untuk Menunggu Pembayaran */
.pending {
  background: #ffc107;
  color: #000;
}

/* Emoji biar lebih kecil */
.unavailable::before,
.pending::before {
  font-size: 13px;
  display: inline-block;
}


/* No result button */
.btn-see-other{
  display:inline-block;
  background:#000;
  color:#fff;
  padding:5px 10px;
  border-radius:6px;
  font-size:12px;
  text-decoration:none;
  border:2px solid #000;
}
.btn-see-other:hover{
  background:#222;
  transform:scale(1.05);
}

/* Responsive */
@media(max-width:480px){
  .car-card{
    flex-direction:column;
    text-align:center;
  }
  .car-image{
    width:80%;
  }
}

/* ============================= */
/* ===== RESPONSIVE FIX ======== */
/* ============================= */

/* Tablet & Mobile (<=992px) */
@media (max-width: 992px) {
  .car-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 10px;
  }

  .car-card {
    display: flex;
    flex-direction: row; /* ✅ tetap menyamping */
    align-items: center;
    justify-content: flex-start;
    text-align: left;
    width: 100%;
    border-radius: 16px;
    padding: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .car-image {
    width: 38%; /* ✅ gambar proporsional */
    max-width: 140px;
    height: auto;
    border-radius: 12px;
    object-fit: contain;
    flex-shrink: 0;
  }

  .car-info {
    flex: 1;
    padding-left: 8px;
  }

  .car-info h4 {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 2px;
  }

  .car-info p {
    font-size: 11px;
    color: #555;
    margin: 1px 0;
  }

  .price {
    font-size: 12px;
    font-weight: 600;
    margin-top: 3px;
  }

  .info-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 6px;
  }

  .tag {
    font-size: 9px;
    padding: 2px 6px;
    border: 1px solid #000;
    border-radius: 10px;
  }

  .fav-btn {
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
  }

  .fav-btn img {
    width: 12px;
    height: 12px;
  }

  .unavailable,
  .pending {
    top: 8px;
    left: 8px;
    font-size: 10px;
    padding: 4px 8px;
  }
}

</style>
@endsection

@section('content')

{{-- Filter --}}
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
      <option value="">Kapasitas</option>
      @foreach($capacities as $capacity)
      <option value="{{ $capacity->capacity_id }}" {{ request('capacity_id') == $capacity->capacity_id ? 'selected' : '' }}>
        {{ $capacity->jumlah_orang }} Org
      </option>
      @endforeach
    </select>

    <input type="text" name="search" class="filter-input" placeholder="Model" value="{{ request('search') }}">

    <button type="submit" class="btn-search">Cari</button>
  </div>
</form>
</div>


{{-- ===== List Mobil ===== --}}
<div class="car-list">

@foreach($cars as $car)
@php
$rental = \App\Models\Rental::where('car_id', $car->car_id)
->whereIn('status_rental',['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
->latest()->first();
@endphp

<a href="{{ route('user.cars.show',$car->car_id) }}" style="text-decoration:none; color:inherit;">
<div class="car-card">

<img src="{{ asset('storage/'.$car->foto) }}" class="car-image">

<div class="car-info">
  <h4>{{ $car->tahun }} {{ $car->brand->nama_merek }} {{ $car->model }}</h4>
  <p>Edisi {{ ucfirst($car->warna) }}</p>

  <p class="price">Rp {{ number_format($car->harga_sewa_per_jam,0,',','.') }} / jam</p>

  <div class="info-tags">
    <div class="tag">{{ number_format($car->kilometer) }} km</div>
    <div class="tag">{{ ucfirst($car->tipe_transmisi) }}</div>
    <div class="tag">{{ $car->capacity->jumlah_orang }} Org</div>
    <div class="tag">{{ $car->liter_tangki }} L</div>
    <div class="tag">{{ ucfirst($car->lokasi) }}</div>
    <div class="tag">{{ $car->dealer ?? 'Auto Center' }}</div>
  </div>
</div>

<div class="fav-btn">
  <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png">
</div>

@if($rental)
@if($rental->status_rental === 'menunggu_pembayaran')
<div class="pending">💰 Pembayaran Pending</div>
@else
<div class="unavailable">⛔ Sedang Disewa</div>
@endif
@endif

</div>
</a>
@endforeach

</div>

@if($cars->isEmpty())
<div style="text-align:center; margin-top:10px;">
<p style="color:red; font-size:12px;">Tidak ditemukan mobil sesuai filter.</p>
<a href="{{ route('user.cars.index') }}" class="btn-see-other">Lihat Mobil Lain</a>
</div>
@endif

@include('partials.bottom-navbar')

@endsection
