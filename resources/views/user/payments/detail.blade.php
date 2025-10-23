@extends('partials.container')

@section('title', 'Detail Payment')

@section('styles')
<style>
  .card-payment {
    max-width: 500px;
    margin: 15px auto 30px; /* bagian atas diperkecil dari 30px ke 15px */
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

  .step-section {
    margin-bottom: 25px;
  }

  .step-title {
    font-size: 16px;
    margin-bottom: 12px;
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
    border-color: #00AEEF;
    background: #F0F9FF;
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
    background: #00C853;
    color: #fff;
    padding: 9px;
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

  .btn:hover {
    background: #009E47;
  }

  .btn-kembali {
    display: block;
    width: 100%;
    background: #ccc;
    color: #000;
    padding: 8px;
    border-radius: 10px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: 0.2s;
  }

  .btn-kembali:hover {
    background: #bbb;
  }

  .back-link svg {
    vertical-align: middle;
    margin-left: -150px;
  }
</style>
@endsection

@section('content')
  {{-- Link Kembali --}}
  <a href="{{ route('user.cars.index') }}" class="back-link" aria-label="Kembali">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
      <line x1="19" y1="12" x2="5" y2="12"/>
      <polyline points="12 19 5 12 12 5"/>
    </svg>
  </a>

  <div class="card-payment">
    <h2>Detail Pembayaran</h2>

    {{-- STEP 1 --}}
    <div class="step-section">
      <h3 class="step-title">1. Pilih Metode Pembayaran</h3>
      <div class="methods" id="methods">
        <div class="m active" data-method="qris">
          <img src="{{ asset('images/qris.png') }}" class="metode-img" alt="QRIS">
        </div>
        <div class="m" data-method="bca">
          <img src="{{ asset('images/bca.png') }}" class="metode-img" alt="BCA">
        </div>
        <div class="m" data-method="bri">
          <img src="{{ asset('images/bri.png') }}" class="metode-img" alt="BRI">
        </div>
        <div class="m" data-method="bni">
          <img src="{{ asset('images/bni.png') }}" class="metode-img" alt="BNI">
        </div>
        <div class="m" data-method="mandiri">
          <img src="{{ asset('images/mandiri.png') }}" class="metode-img" alt="Mandiri">
        </div>
      </div>
    </div>

    {{-- STEP 2 --}}
    <div class="step-section">
      <h3 class="step-title">2. Informasi Penyewa</h3>
      <table>
        <tr><td>Nama Lengkap</td><td>: {{ $rental->user->nama_lengkap ?? '-' }}</td></tr>
        <tr><td>Email</td><td>: {{ $rental->user->email ?? '-' }}</td></tr>
      </table>
    </div>

    {{-- STEP 3 --}}
    <div class="step-section">
      <h3 class="step-title">3. Detail Pesanan</h3>
      <table>
        <tr><td>Nama Mobil</td><td>: {{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model ?? '-' }}</td></tr>
        <tr><td>Tahun Mobil</td><td>: {{ $rental->car->tahun ?? '-' }}</td></tr>
        <tr><td>Harga Sewa</td><td>: Rp{{ number_format($rental->car->harga_sewa_per_hari ?? 0, 0, ',', '.') }} / Hari</td></tr>
        <tr><td>Kapasitas</td><td>: {{ $rental->car->capacity->jumlah_orang ?? '-' }} Orang</td></tr>
        <tr><td>Durasi</td><td>: {{ $rental->durasi_hari }} Hari</td></tr>
        <tr><td>Pakai Sopir</td><td>: {{ $rental->driver === 'ya' ? 'Ya - Rp '.number_format(150000 * $rental->durasi_hari,0,',','.') : 'Tidak' }}</td></tr>
        <tr><td>Dari - Sampai</td><td>: {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m/Y') }}</td></tr>
        <tr><td>Total Biaya Sewa</td><td>: <b>Rp{{ number_format($rental->total_biaya,0,',','.') }}</b></td></tr>
        <tr><td>Lokasi Pengambilan</td><td>: {{ $rental->car->lokasi ?? 'Lokasi belum ditentukan' }}</td></tr>
      </table>
    </div>

    {{-- STEP 4 --}}
    <form action="{{ route('user.payments.start', $rental->rental_id) }}" method="POST" id="startForm">
      @csrf
      <input type="hidden" name="metode" id="metode" value="qris">
      <button type="submit" class="btn" id="payBtn">Lanjut ke Pembayaran Duitku</button>
    </form>

    {{-- STEP 5: Kembali --}}
    <a href="{{ route('user.rentals.create', $rental->car->car_id) }}" class="btn-kembali">← Kembali</a>
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
