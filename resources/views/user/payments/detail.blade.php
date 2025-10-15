<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pilih Metode Pembayaran</title>
  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: #fff;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 500px;
      margin: auto;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    h3 {
      font-size: 16px;
      margin-bottom: 10px;
      margin-top: 25px;
    }
    .methods {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .m {
      border: 2px solid #ddd;
      border-radius: 10px;
      padding: 8px 10px;
      cursor: pointer;
      transition: 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100px;
      height: 60px;
    }
    .m.active {
      border-color: #00AEEF;
      background: #F0F9FF;
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
      text-align: center;
      background: #00C853;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 25px;
      font-size: 16px;
    }
    .btn:hover {
      background: #009E47;
    }
    .btn-kembali {
      display: block;
      width: 100%;
      text-align: center;
      background: #ccc;
      color: #333;
      padding: 12px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      margin-top: 10px;
    }
    .metode-img {
      width: 60px;
      height: auto;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Pembayaran</h2>

    {{-- ========================== STEP 1: METODE PEMBAYARAN ========================== --}}
    <h3>1. Pilih Metode Pembayaran</h3>
    <div class="methods" id="methods">
      <div class="m active" data-method="qris">
        <img src="{{ asset('img/qris.png') }}" class="metode-img" alt="QRIS">
      </div>
      <div class="m" data-method="bca">
        <img src="{{ asset('img/bca.png') }}" class="metode-img" alt="BCA">
      </div>
      <div class="m" data-method="bri">
        <img src="{{ asset('img/bri.png') }}" class="metode-img" alt="BRI">
      </div>
      <div class="m" data-method="bni">
        <img src="{{ asset('img/bni.png') }}" class="metode-img" alt="BNI">
      </div>
      <div class="m" data-method="mandiri">
        <img src="{{ asset('img/mandiri.png') }}" class="metode-img" alt="Mandiri">
      </div>
    </div>

    {{-- ========================== STEP 2: INFORMASI PENYEWA ========================== --}}
    <h3>2. Informasi Penyewa</h3>
    <table>
      <tr>
        <td>Nama Lengkap</td>
        <td>: {{ $rental->user->nama_lengkap ?? '-' }}</td>
      </tr>
      <tr>
        <td>Email</td>
        <td>: {{ $rental->user->email ?? '-' }}</td>
      </tr>
    </table>

    {{-- ========================== STEP 3: DETAIL SEWAAN ========================== --}}
    <h3>3. Detail Pesanan</h3>
    <table>
      <tr>
        <td>Nama Mobil</td>
        <td>: {{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model ?? '-' }}</td>
      </tr>
      <tr>
        <td>Tahun Mobil</td>
        <td>: {{ $rental->car->tahun ?? '-' }}</td>
      </tr>
      <tr>
        <td>Harga Sewa</td>
        <td>: Rp{{ number_format($rental->car->harga_sewa_per_hari ?? 0, 0, ',', '.') }} / Hari</td>
      </tr>
      <tr>
        <td>Kapasitas</td>
        <td>: {{ $rental->car->capacity->jumlah_orang ?? '-' }} Orang</td>
      </tr>
      <tr>
        <td>Durasi</td>
        <td>: {{ $rental->durasi_hari }} Hari</td>
      </tr>
      <tr>
        <td>Pakai Sopir</td>
        <td>: {{ $rental->driver === 'ya' ? 'Ya - Rp '.number_format(150000 * $rental->durasi_hari,0,',','.') : 'Tidak' }}</td>
      </tr>
      <tr>
        <td>Dari - Sampai</td>
        <td>: {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m/Y') }}</td>
      </tr>
      <tr>
        <td>Total Biaya Sewa</td>
        <td>: <b>Rp{{ number_format($rental->total_biaya,0,',','.') }}</b></td>
      </tr>
      <tr>
        <td>Lokasi Pengambilan</td>
        <td>: {{ $rental->car->lokasi ?? 'Lokasi belum ditentukan' }}</td>
      </tr>
    </table>

    {{-- ========================== STEP 4: TOMBOL PEMBAYARAN ========================== --}}
    <form action="{{ route('user.payments.start', $rental->rental_id) }}" method="POST" id="startForm">
      @csrf
      <input type="hidden" name="metode" id="metode" value="qris">
      <button type="submit" class="btn" id="payBtn">Lanjut ke Pembayaran Duitku</button>
    </form>

    {{-- ========================== STEP 5: KEMBALI ========================== --}}
    <a href="{{ route('user.cars.index') }}" class="btn-kembali">← Kembali</a>
  </div>

  <script>
    const metodeInput = document.getElementById('metode');
    const methods = document.getElementById('methods');
    const payBtn = document.getElementById('payBtn');

    // Ganti metode pembayaran aktif
    methods.addEventListener('click', e => {
      const m = e.target.closest('.m');
      if (!m) return;
      document.querySelectorAll('.m').forEach(x => x.classList.remove('active'));
      m.classList.add('active');
      metodeInput.value = m.dataset.method;
    });

    // Saat klik Bayar
    document.getElementById('startForm').addEventListener('submit', () => {
      payBtn.disabled = true;
      payBtn.innerText = 'Menghubungkan ke Duitku...';
    });
  </script>
</body>
</html>
