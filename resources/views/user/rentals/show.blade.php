@extends('partials.container')

@section('title', 'Detail Riwayat Penyewaan')

@section('styles')
<style>
body {
  background: #f8f9fa;
  font-family: 'Poppins', sans-serif;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
  padding: 40px 15px;
}

/* ===== TITLE ===== */
h2 {
  text-align: center;
  color: #111;
  font-weight: 600;
  font-size: 22px;
  margin-bottom: 22px;
  letter-spacing: 0.3px;
}

/* ===== CARD ===== */
.detail-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
  padding: 26px 28px;
  max-width: 600px;
  width: 100%;
  transition: 0.25s ease;
}
.detail-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* ===== DETAIL ROW ===== */
.detail-row {
  display: grid;
  grid-template-columns: 150px 1fr;
  align-items: start;
  margin-bottom: 10px;
  row-gap: 2px;
}
.label {
  font-weight: 600;
  color: #111827;
  font-size: 14px;
}
.value {
  color: #1e293b;
  font-size: 14.5px;
  font-weight: 500;
  word-wrap: break-word;
}

/* ===== DIVIDER ===== */
.divider {
  border-top: 1px solid #e5e7eb;
  margin: 18px 0;
}

/* ===== STATUS BADGE ===== */
.status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  text-transform: capitalize;
  margin-top: 3px;
}
.status::before {
  content: '●';
  font-size: 10px;
}
.status.menunggu   { background: #fff3cd; color: #856404; }
.status.berjalan   { background: #dcfce7; color: #166534; }
.status.selesai    { background: #d4edda; color: #155724; } /* 💚 hijau lembut */
.status.dibatalkan { background: #fee2e2; color: #991b1b; }

/* ===== LOKASI PICKUP ===== */
.highlight {
  background: #e5e5e5;
  padding: 6px 10px;
  border-radius: 8px;
  color: #000;
  font-size: 13.5px;
  font-weight: 500;
  display: inline-block;
  margin-top: 3px;
}

/* ===== BUTTON (ubah biru → item) ===== */
.back-btn {
  display: block;
  width: 100%;
  text-align: center;
  background: #000;
  color: #fff;
  padding: 10px 0;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14.5px;
  margin-top: 25px;
  transition: all 0.25s;
  letter-spacing: 0.3px;
}
.back-btn:hover {
  background: #333;
  transform: translateY(-1px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 600px) {
  body {
    align-items: flex-start;
    padding: 25px 10px;
  }
  .detail-card {
    padding: 22px 18px;
  }
  .detail-row {
    grid-template-columns: 1fr;
    margin-bottom: 10px;
  }
  .label {
    margin-bottom: 2px;
  }
  .value {
    font-size: 14px;
  }
  .back-btn {
    font-size: 14px;
    padding: 9px 0;
  }
}
</style>
@endsection

@section('content')
<div class="detail-card">
  <h2>Detail Penyewaan Mobil</h2>

  {{-- === INFORMASI MOBIL === --}}
  <div class="detail-row">
    <span class="label">Mobil</span>
    <span class="value">
      {{ $rental->car->tahun ?? '' }} 
      {{ $rental->car->brand->nama_merek ?? '-' }} 
      {{ $rental->car->model }}
    </span>
  </div>

  <div class="detail-row">
    <span class="label">Warna</span>
    <span class="value">{{ ucfirst($rental->car->warna ?? '-') }}</span>
  </div>

  <div class="divider"></div>

  {{-- === WAKTU PENYEWAAN === --}}
  <div class="detail-row">
    <span class="label">Tanggal Mulai</span>
    <span class="value">{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d M Y') }}</span>
  </div>

  <div class="detail-row">
    <span class="label">Tanggal Selesai</span>
    <span class="value">{{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d M Y') }}</span>
  </div>

  <div class="detail-row">
    <span class="label">Durasi</span>
    <span class="value">{{ $rental->durasi_hari }} hari</span>
  </div>

  <div class="divider"></div>

  {{-- === METODE PICKUP & DRIVER === --}}
  <div class="detail-row">
    <span class="label">Metode Pickup</span>
    <span class="value">{{ ucfirst(str_replace('_', ' ', $rental->metode_pickup)) }}</span>
  </div>

  <div class="detail-row">
    <span class="label">Driver</span>
    <span class="value">{{ ucfirst($rental->driver) }}</span>
  </div>

  <div class="detail-row">
    <span class="label">Lokasi Jemput</span>
    <span class="value">
      <span class="highlight">
        {{ $rental->lokasi_pickup ?? 'Tidak ada lokasi jemput (ambil di tempat)' }}
      </span>
    </span>
  </div>

  <div class="divider"></div>

  {{-- === TOTAL BIAYA & STATUS === --}}
  <div class="detail-row">
    <span class="label">Total Biaya</span>
    <span class="value">Rp {{ number_format($rental->total_biaya, 0, ',', '.') }}</span>
  </div>

  <div class="detail-row">
    <span class="label">Status</span>
    <span class="value">
      <span class="status {{ $rental->status_rental }}">{{ ucfirst($rental->status_rental) }}</span>
    </span>
  </div>

  <a href="{{ route('user.rentals.index') }}" class="back-btn">← Kembali ke Riwayat</a>
</div>

@include('partials.bottom-navbar')
@endsection
