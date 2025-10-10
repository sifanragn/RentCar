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
}
</style>
@endsection

@section('content')
  <div class="filter-wrapper">
    <div class="filter-bar-horizontal">
      <select id="brand" class="filter-select">
        <option value="">Cari Mobil</option>
        <option value="toyota">Toyota</option>
        <option value="honda">Honda</option>
        <option value="bmw">BMW</option>
      </select>

      <input type="date" id="date" class="filter-input" />

      <select id="capacity" class="filter-select">
        <option value="">Kapasitas</option>
        <option value="2">2 Orang</option>
        <option value="4">4 Orang</option>
        <option value="7">7 Orang</option>
      </select>

      <input type="text" id="model" class="filter-input" placeholder="Model Mobil">

      <button class="btn-search">Search</button>
    </div>
  </div>

  <div class="card-container">
  @foreach ($cars as $car)
    <a href="{{ route('user.cars.show', $car->id) }}" class="card-link">
      <div class="card">
        <img src="{{ asset('storage/' . $car->gambar) }}" class="car-image" alt="{{ $car->model }}">
        <div class="card-content">
          <h3>{{ $car->tahun }} {{ $car->brand->nama_merek }} {{ $car->model }}</h3>
          <p>{{ $car->warna }}</p>
          <p class="price">Rp {{ number_format($car->harga, 0, ',', '.') }}</p>
          <div class="info-tags">
            <div class="tag">{{ $car->kilometer }} km</div>
            <div class="tag">{{ $car->transmisi }}</div>
            <div class="tag">{{ $car->capacity->jumlah_orang }} Orang</div>
            <div class="tag">{{ $car->bahan_bakar }}</div>
            <div class="tag">{{ $car->lokasi }}</div>
          </div>
        </div>
        <div class="fav-btn">
          <img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" alt="heart">
        </div>
      </div>
    </a>
  @endforeach
</div>


  @include('partials.bottom-navbar')
@endsection
