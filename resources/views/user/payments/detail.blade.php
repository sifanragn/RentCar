@extends('partials.container')

@section('title', 'Detail Payment')

@section('styles')
<style>
  /* Card container */
.card-payment {
  max-width: 500px;
  margin: 15px auto 30px;
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  border: 1.5px solid #e8e8e8;
  box-shadow: 0 6px 12px rgba(0,0,0,0.06);
}

/* Title */
h2 {
  text-align: center;
  margin-bottom: 25px;
  font-weight: 700;
  font-size: 20px;
  color: #111;
}

/* Section title */
.section-title {
  font-size: 14px;
  font-weight: 600;
  margin: 20px 0 8px;
  color: #222;
  text-transform: uppercase;
  letter-spacing: .5px;
}

/* Info box */
.info-box {
  background: #fafafa;
  padding: 14px;
  border-radius: 12px;
  border: 1px solid #eee;
  margin-bottom: 14px;
}

.info-row {
  display: flex;
  gap: 6px;
  padding: 6px 0;
  font-size: 14px;
}

.info-label {
  width: 120px;
  color: #666;
  font-weight: 500;
}

.info-separator {
  color: #666;
}

.info-value {
  flex: 1;
  font-weight: 600;
  color: #222;
}

.total-row .info-value {
  color: #0d6efd;
  font-weight: 700;
}

/* Payment methods */
.methods {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.m {
  border: 2px solid #ddd;
  border-radius: 12px;
  padding: 8px 10px;
  width: 100px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: .2s;
  background: #fff;
}

.m.active {
  border-color: #000;
  background: #f5f5f5;
}

.metode-img { width: 60px; }

/* Main Button */
.btn-pay {
  width: 100%;
  background: #000;
  color: #fff;
  padding: 14px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 15px;
  margin-top: 14px;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}
.btn-pay:hover { background: #222; }

/* Back Button */
.btn-back {
  width: 100%;
  margin-top: 10px;
  display: block;
  background: #f1f1f1;
  color: #333;
  padding: 11px;
  font-weight: 600;
  border-radius: 10px;
  text-align: center;
}
.btn-back:hover { background: #e4e4e4; }

/* Alert */
.alert {
  background: #ffecec;
  color: #b71c1c;
  border: 1px solid #ffcdd2;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-size: 13px;
}

</style>
@endsection

@section('content')
  {{-- 🔙 Link Kembali --}}
  <a href="{{ route('user.cars.index') }}" class="back-link" aria-label="Kembali">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      class="feather feather-arrow-left">
      <line x1="19" y1="12" x2="5" y2="12"/>
      <polyline points="12 19 5 12 12 5"/>
    </svg>
  </a>

  <div class="card-payment">
    <h2>Detail Pembayaran</h2>

    {{-- ⚠️ Error dari controller --}}
    @if(session('error'))
      <div class="alert">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ===================== STEP 1: METODE PEMBAYARAN ===================== --}}
<div class="step-section">
    <p class="section-title">1. Metode Pembayaran</p>

  <div class="methods" id="methods">
    @foreach (['qris', 'bca', 'bri', 'bni', 'mandiri'] as $m)
      <div class="m {{ $loop->first ? 'active' : '' }}" data-method="{{ $m }}">
        <img src="{{ asset('images/' . $m . '.png') }}" 
             class="metode-img"
             alt="{{ strtoupper($m) }}">
      </div>
    @endforeach
  </div>
</div>


    {{-- ===================== STEP 2: INFORMASI PENYEWA ===================== --}}
<p class="section-title">2. Informasi Penyewa</p>
<div class="info-box">
   <div class="info-row">
    <span class="info-label">Nama Lengkap</span>
    <span class="info-separator">:</span>
    <span class="info-value">{{ $rental->user->nama_lengkap ?? '-' }}</span>
  </div>

    <div class="info-row">
    <span class="info-label">Email</span>
    <span class="info-separator">:</span>
    <span class="info-value">{{ $rental->user->email ?? '-' }}</span>
  </div>
</div>

    {{-- ===================== STEP 3: DETAIL PESANAN ===================== --}}
    @php
    $hargaMobil = $rental->car->harga_sewa_per_jam ?? 0;
    $durasiJam  = $rental->durasi_jam ?? 0;
    $mobilTarif = $hargaMobil * $durasiJam;

    $driverTarif = 0;
    if ($rental->driver === 'ya' && $rental->driverData) {
        $hargaDriverPerJam = $rental->driverData->harga_per_jam ?? 0;
        $driverTarif = $hargaDriverPerJam * $durasiJam;
    }

    $totalKeseluruhan = $mobilTarif + $driverTarif;
@endphp

    <p class="section-title">3. Detail Pesanan</p>
<div class="info-box">

  <div class="info-row">
    <span class="info-label">Mobil</span>
    <span class="info-separator">:</span>
    <span class="info-value">{{ $rental->car->brand->nama_merek }} {{ $rental->car->model }}</span>
  </div>

  <div class="info-row">
    <span class="info-label">Tahun</span>
    <span class="info-separator">:</span>
    <span class="info-value">{{ $rental->car->tahun }}</span>
  </div>

  <div class="info-row">
    <span class="info-label">Harga/Jam</span>
    <span class="info-separator">:</span>
    <span class="info-value">Rp{{ number_format($hargaMobil,0,',','.') }}</span>
  </div>

  <div class="info-row">
    <span class="info-label">Durasi</span>
    <span class="info-separator">:</span>
    <span class="info-value">{{ $durasiJam }} Jam</span>
  </div>

  <div class="info-row">
    <span class="info-label">Driver</span>
    <span class="info-separator">:</span>
    <span class="info-value">
      @if($rental->driver === 'ya' && $rental->driverData)
        Ya — {{ $rental->driverData->nama }}
      @else 
        Tidak 
      @endif
    </span>
  </div>

  <div class="info-row total-row">
    <span class="info-label">Total</span>
    <span class="info-separator">:</span>
    <span class="info-value">Rp{{ number_format($totalKeseluruhan,0,',','.') }}</span>
  </div>

  <div class="info-row">
    <span class="info-label">Waktu</span>
    <span class="info-separator">:</span>
    <span class="info-value">
      {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m H:i') }} → 
      {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m H:i') }}
    </span>
  </div>

  <div class="info-row">
    <span class="info-label">Lokasi</span>
    <span class="info-separator">:</span>
    <span class="info-value">{{ $rental->car->lokasi ?? '-' }}</span>
  </div>

</div>



    {{-- ===================== STEP 4: PEMBAYARAN ===================== --}}
    <form action="{{ route('user.payments.start', $rental->rental_id) }}" method="POST" id="startForm">
      @csrf
      <input type="hidden" name="metode" id="metode" value="qris">
     <button type="submit" class="btn-pay" id="payBtn">Lanjut ke Pembayaran Duitku</button>
    </form>
  </div>

  @include('partials.bottom-navbar')

  <script>
    const metodeInput = document.getElementById('metode');
    const methods = document.getElementById('methods');
    const payBtn = document.getElementById('payBtn');

    methods.addEventListener('click', e => {
      const m = e.target.closest('.m');
      if (!m) return;
      document.querySelectorAll('.m').forEach(x => x.classList.remove('active'));
      m.classList.add('active');
      metodeInput.value = m.dataset.method;
    });

    document.getElementById('startForm').addEventListener('submit', () => {
      payBtn.disabled = true;
      payBtn.innerText = 'Menghubungkan ke Duitku...';
    });
  </script>
@endsection
