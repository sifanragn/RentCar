@extends('partials.container')

@section('title', 'Detail Riwayat Penyewaan')

@section('styles')
<style>
body {
  background: #f8f9fa;
  font-family: 'Poppins', sans-serif;
  padding: 20px 15px 90px;
}


/* ===== CARD WRAPPER ===== */
.detail-card {
  background: #fff;
  border-radius: 16px;
  padding: 22px 18px;
  border: 1.5px solid #e8e8e8;
  box-shadow: 0 6px 12px rgba(0,0,0,0.06);
  max-width: 550px;
  margin: 0 auto 20px;
  margin-top: 10px;
}

/* ===== TITLE ===== */
.detail-card h2 {
  text-align: center;
  color: #111;
  font-weight: 700;
  font-size: 20px;
  margin-bottom: 18px;
}


/* ===== TITLE ===== */
h2 {
  text-align: center;
  color: #111;
  font-weight: 700;
  font-size: 20px;
  margin-bottom: 18px;
}

/* ===== ROW ===== */
.detail-row {
  display: flex;
  justify-content: center;   /* ❗ bukan space-between lagi */
  align-items: flex-start;
  margin-bottom: 10px;
  font-size: 14px;
  gap: 8px;
}

/* label | separator | value */
.detail-row .label {
  color: #6b7280;
  font-weight: 500;
  min-width: 120px;
}

.detail-row .sep {
  color: #9ca3af;
  flex-shrink: 0;
}

.detail-row .value {
  font-weight: 600;
  color: #111;
  text-align: left;          /* value kanan label */
  flex: 1;
  word-break: break-word;
}

/* ===== DIVIDER ===== */
.divider {
  border-top: 1px solid #e5e7eb;
  margin: 14px 0;
}

/* ===== STATUS BADGE ===== */
.status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
}
.status::before {
  content: '●';
  font-size: 10px;
}
.status.menunggu     { background: #fff3cd; color: #856404; }
.status.berjalan     { background: #dcfce7; color: #166534; }
.status.selesai      { background: #d4edda; color: #155724; }
.status.dibatalkan   { background: #fee2e2; color: #991b1b; }

/* Pickup highlight */
.highlight {
  background: #f3f4f6;
  padding: 6px 10px;
  border-radius: 8px;
  display: inline-block;
  font-weight: 500;
}

/* ===== BUTTON ===== */
.back-btn {
  display: block;
  width: 100%;
  background: #000;
  color: #fff;
  padding: 11px 0;
  border-radius: 10px;
  margin-top: 22px;
  text-align: center;
  text-decoration: none;
  font-weight: 600;
  transition: .2s;
}
.back-btn:hover {
  background: #222;
}

/* ===== MOBILE ===== */
@media(max-width: 600px) {
  .detail-row {
    grid-template-columns: 1fr 10px 1fr;
  }
  .value {
    text-align: right;
  }
}
</style>
@endsection

@section('content')
{{-- 🔙 Link Kembali --}}
<a href="{{ route('user.rentals.index') }}" class="back-link" aria-label="Kembali">
  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black"
    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    class="feather feather-arrow-left">
    <line x1="19" y1="12" x2="5" y2="12"/>
    <polyline points="12 19 5 12 12 5"/>
  </svg>
</a>


<div class="detail-card">
  <h2>Detail Penyewaan Mobil</h2>

<div class="detail-row">
  <span class="label">Mobil</span>
  <span class="sep">:</span>
  <span class="value">{{ $rental->car->brand->nama_merek }} {{ $rental->car->model }} ({{ $rental->car->tahun }})</span>
</div>

<div class="detail-row">
  <span class="label">Warna</span>
  <span class="sep">:</span>
  <span class="value">{{ ucfirst($rental->car->warna ?? '-') }}</span>
</div>

<div class="divider"></div>

<div class="detail-row">
  <span class="label">Mulai</span>
  <span class="sep">:</span>
  <span class="value">{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d M Y H:i') }}</span>
</div>

<div class="detail-row">
  <span class="label">Selesai</span>
  <span class="sep">:</span>
  <span class="value">{{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d M Y H:i') }}</span>
</div>

<div class="detail-row">
  <span class="label">Durasi</span>
  <span class="sep">:</span>
  <span class="value">
    @if(isset($rental->durasi_jam))
      {{ $rental->durasi_jam }} Jam
    @else
      {{ $rental->durasi_hari }} Hari
    @endif
  </span>
</div>

<div class="divider"></div>

<div class="detail-row">
  <span class="label">Pickup</span>
  <span class="sep">:</span>
  <span class="value">{{ ucfirst(str_replace('_', ' ', $rental->metode_pickup)) }}</span>
</div>

<div class="detail-row">
  <span class="label">Driver</span>
  <span class="sep">:</span>
  <span class="value">{{ ucfirst($rental->driver) }}</span>
</div>

<div class="detail-row">
  <span class="label">Lokasi</span>
  <span class="sep">:</span>
  <span class="value"><span class="highlight">{{ $rental->lokasi_pickup ?? 'Ambil di tempat' }}</span></span>
</div>

<div class="divider"></div>

<div class="detail-row">
  <span class="label">Total Biaya</span>
  <span class="sep">:</span>
  <span class="value">Rp{{ number_format($rental->total_biaya,0,',','.') }}</span>
</div>

<div class="detail-row">
  <span class="label">Status</span>
  <span class="sep">:</span>
  <span class="value">
    <span class="status {{ $rental->status_rental }}">{{ ucfirst($rental->status_rental) }}</span>
  </span>
</div>

</div>

@include('partials.bottom-navbar')
@endsection
