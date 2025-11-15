@extends('partials.container')

@section('title', 'Nomor Darurat')

@section('styles')
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(180deg, #f3f7ff 0%, #fff 100%);
  color: #111;
  margin: 0;
  padding: 0;
}

/* ===== WRAPPER ===== */
.emergency-container {
  max-width: 480px;
  margin: 20px auto 50px; /* tambahkan jarak bawah, misalnya 110px */
  padding: 0 18px;
}

h2 {
  text-align: center;
  font-weight: 700;
  font-size: 21px;
  margin-bottom: 25px;
  color: #1a1a1a;
}

/* ===== CARD ITEM ===== */
.emergency-item {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.07);
  padding: 16px 18px 14px;
  margin-bottom: 16px;
  position: relative;
  overflow: hidden;
  transition: all 0.25s ease;
  border:1px solid #ccc;
}
.emergency-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

/* ===== ICON BADGE ===== */
.icon-badge {
  width: 50px;
  height: 50px;
  border-radius: 25%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  color: #fff;
  background: #000;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

/* ===== CONTENT ===== */
.item-content {
  flex: 1;
}

.item-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.item-header h5 {
  font-size: 15px;
  font-weight: 600;
  color: #222;
  margin: 0;
}

.item-number a {
  display: inline-block;
  color: #007bff;
  font-weight: 600;
  text-decoration: none;
  font-size: 14px;
  margin-top: 6px;
}
.item-number a:hover {
  text-decoration: underline;
}

.item-desc {
  font-size: 13px;
  color: #555;
  margin-top: 4px;
  line-height: 1.4;
}

/* ===== CARD LAYOUT ===== */
.card-body {
  display: flex;
  gap: 14px;
}

/* ===== EMPTY STATE ===== */
.empty {
  text-align: center;
  color: #888;
  font-size: 14px;
  margin-top: 40px;
}

@media (max-width: 480px) {
  .emergency-item { padding: 14px 16px 12px; }
  h2 { font-size: 19px; }
}
</style>

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endsection


@section('content')
<div class="emergency-container">
  <h2>Nomor Darurat</h2>

@php
  $svgMap = [
    '📞' => '<i class="fa-solid fa-phone-volume"></i>',          // Telepon umum
    '🚓' => '<i class="fa-solid fa-user-shield"></i>',           // Polisi
    '🚑' => '<i class="fa-solid fa-truck-medical"></i>',         // Ambulans
    '🚒' => '<i class="fa-solid fa-fire"></i>',                  // Pemadam
    '🛠️' => '<i class="fa-solid fa-toolbox"></i>',                // Servis ringan
    '🛠'  => '<i class="fa-solid fa-toolbox"></i>',                // Versi tanpa FE0F
    '🧰'  => '<i class="fa-solid fa-wrench"></i>',                 // 🧰 Layanan perbaikan berat (ubah jadi gear)
    '🛞' => '<i class="fa-solid fa-truck-ramp-box"></i>',     // Derek mobil
    '💡' => '<i class="fa-solid fa-bolt"></i>',                  // Listrik / teknis
    '📍' => '<i class="fa-solid fa-location-dot"></i>',          // Kantor / lokasi
  ];
@endphp


  @forelse ($numbers as $n)
    <div class="emergency-item">
      <div class="card-body">

        {{-- Icon Section --}}
        <div class="icon-badge">
          {!! $svgMap[$n->icon] ?? '<i class="fa-solid fa-phone"></i>' !!}
        </div>

        {{-- Info Section --}}
        <div class="item-content">
          <div class="item-header">
            <h5>{{ $n->keperluan }}</h5>
          </div>
<div class="item-number">
  <a href="tel:{{ $n->nomor }}">{{ $n->nomor }}</a>
</div>
          @if($n->keterangan)
            <p class="item-desc">{{ $n->keterangan }}</p>
          @endif
        </div>

      </div>
    </div>
  @empty
    <p class="empty">Belum ada nomor darurat yang tersedia.</p>
  @endforelse
</div>

@include('partials.bottom-navbar')
@endsection
