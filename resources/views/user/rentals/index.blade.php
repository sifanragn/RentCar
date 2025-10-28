@extends('partials.container')

@section('title', 'Riwayat Penyewaan')

@section('styles')
<style>
h2 {
  text-align: center;
  color: #111;
  font-weight: 600;
  margin-bottom: 20px;
  font-size: 24px;
  margin-left: -10px;
}

/* ===== CONTAINER ===== */
.rentals-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 22px;
  width: 95%;
  margin: 0 auto;
  margin-bottom: 50px;
}

/* ===== CARD ===== */
.card {
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: 0.25s ease;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  margin-left: -10px;
  margin-right: 25px;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

/* ===== FOTO MOBIL ===== */
.card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-bottom: 1px solid #eee;
}

/* ===== STATUS RIBBON ===== */
.status-ribbon {
  position: absolute;
  top: 12px;
  right: 12px;
  color: #fff;
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 8px;
  font-weight: 500;
  text-transform: capitalize;
}
.status-ribbon.menunggu { background: #ffc107; }
.status-ribbon.berjalan { background: #198754; }
.status-ribbon.selesai { background: #198754; }
.status-ribbon.dibatalkan { background: #dc3545; }

/* ===== INFORMASI MOBIL ===== */
.car-info {
  padding: 16px 18px;
}
.car-info h4 {
  margin: 0 0 6px 0;
  color: #111;
  font-size: 17px;
  font-weight: 600;
}
.car-info p {
  margin: 4px 0;
  color: #555;
  font-size: 14px;
  line-height: 1.5;
}

/* ===== STATUS BADGE ===== */
.status {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  margin-top: 8px;
}
.status.menunggu { background: #fff3cd; color: #856404; }
.status.berjalan { background: #d1e7dd; color: #0f5132; }
.status.selesai { background: #cfe2ff; color: #084298; }
.status.dibatalkan { background: #f8d7da; color: #842029; }
.status-ribbon.draft { background: #6c757d; } 

/* ===== BUTTON DETAIL ===== */
.btn-detail {
  text-decoration: none;
  text-align: center;
  background: #000;
  color: #fff;
  padding: 10px 0;
  border-radius: 0 0 10px 10px;
  font-size: 14.5px;
  font-weight: 500;
  transition: 0.25s;
}
.btn-detail:hover {
  background: #404040;
}

/* ===== EMPTY STATE ===== */
.empty {
  text-align: center;
  color: #666;
  font-size: 15px;
  margin-top: 40px;
}

  .back-link {
    color:#000; text-decoration:none;
    font-weight:250; font-size:15px;
    margin-left: -275px;
    margin-bottom: 15px;
    }

  .back-link:hover { text-decoration:underline; }
/* ===== RESPONSIVE ===== */
@media (max-width: 480px) {
  h2 { font-size: 20px; }
  .card img { height: 160px; }
}
</style>
@endsection

@section('content')
<a 
  href="{{ request()->query('from') === 'profile' 
      ? route('user.profile.index') 
      : route('user.dashboard') }}" 
  class="back-link"
>
  <i class="fa-solid fa-arrow-left"></i>
</a>

  <h2>Riwayat Penyewaan Mobil</h2>

  {{-- Flash Message --}}
  @if(session('success'))
    <div style="background:#d1e7dd;color:#0f5132;padding:10px;margin-bottom:15px;border-radius:8px;text-align:center;">
      {{ session('success') }}
    </div>
  @endif

  {{-- Kondisi Data Kosong --}}
  @if($rentals->isEmpty())
    <p class="empty">Belum ada penyewaan mobil yang tercatat.</p>
  @else
    <div class="rentals-container">
@foreach($rentals as $rental)
  <div class="card">

    {{-- Status Ribbon --}}
    <div class="status-ribbon {{ $rental->status_rental }}">
      {{ ucfirst($rental->status_rental) }}
    </div>

    <div class="car-info">
      <h4>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model ?? '' }}</h4>
      <p><b>Tanggal Sewa:</b> {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d M Y') }}</p>
      <p><b>Selesai:</b> {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d M Y') }}</p>
      <p><b>Durasi:</b> {{ $rental->durasi_hari }} hari</p>
      <p><b>Total:</b> Rp {{ number_format($rental->total_biaya, 0, ',', '.') }}</p>

      {{-- 🚨 Peringatan untuk Draft --}}
      @if($rental->status_rental === 'draft')
        @php
          $expiredAt = \Carbon\Carbon::parse($rental->created_at)->addMinutes(30);
          $sisaMenit = now()->diffInMinutes($expiredAt, false);
        @endphp

        @if($sisaMenit > 0)
          <div style="margin-top:10px;padding:10px;background:#fff3cd;color:#664d03;border-radius:8px;font-size:13px;">
            ⚠️ Penyewaan ini belum dikonfirmasi. Akan otomatis dihapus dalam 
            <b><span class="cd" data-s="{{ $sisaMenit * 60 }}"></span></b>.
          </div>
        @else
          <div style="margin-top:10px;padding:10px;background:#f8d7da;color:#842029;border-radius:8px;font-size:13px;">
            ❌ Penyewaan ini telah kadaluarsa dan akan segera dihapus.
          </div>
        @endif
      @endif
    </div>

    <a href="{{ route('user.rentals.show', $rental->rental_id) }}" class="btn-detail">
      Lihat Detail
    </a>
  </div>
@endforeach
    </div>
  @endif
  
<script>
document.addEventListener("DOMContentLoaded", () => {
  const toMMSS = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
  document.querySelectorAll('.cd').forEach(el=>{
    let s = +el.dataset.s;
    if(s<=0){ el.textContent='00:00'; return; }
    const tick=()=>{
      el.textContent=toMMSS(s);
      if(s>0) setTimeout(()=>{ s--; tick(); },1000);
    };
    tick();
  });
});
</script>

  @include('partials.bottom-navbar')
@endsection
