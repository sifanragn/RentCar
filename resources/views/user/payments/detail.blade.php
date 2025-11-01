@extends('partials.container')

@section('title', 'Detail Payment')

@section('styles')
<style>
  .card-payment {
    max-width: 500px;
    margin: 15px auto 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.5);
    padding: 20px;
  }

  h2 {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 700;
    font-size: 20px;
  }

  .step-section { margin-bottom: 25px; }

  .step-title {
    font-size: 16px;
    margin-bottom: 12px;
    font-weight: 600;
  }

  .methods {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .m {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 8px 10px;
    width: 100px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.2s;
  }

  .m.active {
    border-color: #000;
    background: #f5f5f5;
  }

  .metode-img {
    width: 60px;
    height: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  td {
    padding: 6px 0;
    font-size: 14px;
    border-bottom: 1px solid #eee;
  }

  td:first-child {
    width: 160px;
    font-weight: 600;
  }

  .btn {
    display: block;
    width: 100%;
    background: #000;
    color: #fff;
    padding: 10px;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    margin-top: 25px;
    text-align: center;
    cursor: pointer;
    transition: 0.2s;
    margin-bottom: 10px;
  }

  .btn:hover { background: #222; }

  .btn-kembali {
    display: block;
    width: 100%;
    background: #ccc;
    color: #000;
    padding: 9px;
    border-radius: 10px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: 0.2s;
  }

  .btn-kembali:hover { background: #bbb; }

  .alert {
    background: #ffecec;
    color: #b71c1c;
    border: 1px solid #ffcdd2;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
  }

  .back-link svg {
    vertical-align: middle;
    margin-left: -150px;
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
      <h3 class="step-title">1. Pilih Metode Pembayaran</h3>
      <div class="methods" id="methods">
        @foreach (['qris', 'bca', 'bri', 'bni', 'mandiri'] as $m)
          <div class="m {{ $loop->first ? 'active' : '' }}" data-method="{{ $m }}">
            <img src="{{ asset('img/' . $m . '.png') }}" class="metode-img" alt="{{ strtoupper($m) }}">
          </div>
        @endforeach
      </div>
    </div>

    {{-- ===================== STEP 2: INFORMASI PENYEWA ===================== --}}
    <div class="step-section">
      <h3 class="step-title">2. Informasi Penyewa</h3>
      <table>
        <tr><td>Nama Lengkap</td><td>: {{ $rental->user->nama_lengkap ?? '-' }}</td></tr>
        <tr><td>Email</td><td>: {{ $rental->user->email ?? '-' }}</td></tr>
      </table>
    </div>

    {{-- ===================== STEP 3: DETAIL PESANAN ===================== --}}
    <div class="step-section">
      <h3 class="step-title">3. Detail Pesanan</h3>
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

      <table>
        <tr>
          <td>Nama Mobil</td>
          <td>: {{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model ?? '-' }}</td>
        </tr>
        <tr><td>Tahun Mobil</td><td>: {{ $rental->car->tahun ?? '-' }}</td></tr>
        <tr><td>Harga Sewa</td><td>: Rp{{ number_format($hargaMobil, 0, ',', '.') }} / Jam</td></tr>
        <tr><td>Durasi</td><td>: {{ $durasiJam }} Jam</td></tr>
        <tr>
          <td>Pakai Sopir</td>
          <td>
            @if($rental->driver === 'ya' && $rental->driverData)
              Ya — {{ $rental->driverData->nama }}
              (Rp{{ number_format($rental->driverData->harga_per_jam, 0, ',', '.') }}/jam × {{ $durasiJam }} jam =
              <b>Rp{{ number_format($driverTarif, 0, ',', '.') }}</b>)
            @else
              Tidak
            @endif
          </td>
        </tr>
        <tr>
          <td>Total Biaya Sewa</td>
          <td>
            : <b>Rp{{ number_format($totalKeseluruhan, 0, ',', '.') }}</b>
            @if($driverTarif > 0)
              <br><small>(Mobil: Rp{{ number_format($mobilTarif, 0, ',', '.') }} + Sopir: Rp{{ number_format($driverTarif, 0, ',', '.') }})</small>
            @endif
          </td>
        </tr>
        <tr>
          <td>Dari - Sampai</td>
          <td>:
            {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m/Y H:i') }} s.d
            {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m/Y H:i') }}
          </td>
        </tr>
        <tr><td>Lokasi Pengambilan</td><td>: {{ $rental->car->lokasi ?? 'Lokasi belum ditentukan' }}</td></tr>
      </table>
    </div>

    {{-- ===================== STEP 4: PEMBAYARAN ===================== --}}
    <form action="{{ route('user.payments.start', $rental->rental_id) }}" method="POST" id="startForm">
      @csrf
      <input type="hidden" name="metode" id="metode" value="qris">
      <button type="submit" class="btn" id="payBtn">Lanjut ke Pembayaran Duitku</button>
    </form>

    <button type="button" class="btn-kembali" id="exitBtn">← Keluar</button>
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
  <style>
  .modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.45);
    display: none; justify-content: center; align-items: center;
    backdrop-filter: blur(6px);
    z-index: 99999;
    animation: fadeIn .25s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; } to { opacity: 1; }
  }

  .modal-box {
    background: #fff;
    padding: 22px 24px;
    border-radius: 14px;
    width: 90%;
    max-width: 360px;
    text-align: center;
    transform: scale(.9);
    opacity: 0;
    animation: pop .25s ease forwards;
  }

  @keyframes pop {
    to { opacity: 1; transform: scale(1); }
  }

  .modal-icon {
    font-size: 44px;
    margin-bottom: 8px;
    animation: spin 1.5s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .modal-title {
    font-size: 18px;
    font-weight: 700;
    color:#111;
    margin-bottom: 6px;
  }

  .modal-desc {
    color:#555;
    font-size:14px;
  }

  .modal-btns {
    margin-top: 20px;
    display: flex;
    gap: 10px;
  }

  .btn-danger, .btn-light {
    flex: 1; padding: 10px; border-radius: 8px;
    font-weight: 600; cursor: pointer; border: none;
    transition: .2s;
  }

  .btn-danger {
    background:#000; color:#fff;
  }
  .btn-danger:hover { background:#111; }

  .btn-light {
    background:#f1f1f1; color:#111;
  }
  .btn-light:hover { background:#e4e4e4; }
</style>

<div class="modal-overlay" id="exitModal">
  <div class="modal-box" onclick="event.stopPropagation()">

    <!-- icon jam pasir -->
    <div class="modal-icon">⏳</div>

    <div class="modal-title">Keluar dari halaman ini?</div>
    <p class="modal-desc">Langkah kamu tinggal sedikit lagi loh…</p>

    <div class="modal-btns">
      <button class="btn-light" id="cancelExit">Lanjutkan</button>

      <form action="{{ route('user.rentals.destroy', $rental->rental_id) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger">Keluar</button>
      </form>
    </div>
  </div>
</div>

<script>
  const exitBtn    = document.getElementById('exitBtn');
  const exitModal  = document.getElementById('exitModal');
  const cancelExit = document.getElementById('cancelExit');

  // buka modal
  exitBtn.onclick = () => exitModal.style.display = 'flex';

  // klik lanjutkan
  cancelExit.onclick = () => exitModal.style.display = 'none';

  // klik area luar close modal
  exitModal.onclick = () => exitModal.style.display = 'none';
</script>

@endsection
