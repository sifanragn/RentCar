<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 20px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #222;
      margin-bottom: 20px;
    }
    h3 {
      margin-top: 25px;
      color: #333;
      font-size: 1rem;
      border-bottom: 1px solid #ddd;
      padding-bottom: 6px;
    }
    .method-row {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 15px;
      flex-wrap: wrap;
    }
    .method-row img {
      width: 80px;
      height: 40px;
      object-fit: contain;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 5px;
      cursor: pointer;
      transition: all 0.2s;
      background: #fff;
    }
    .method-row img:hover {
      border-color: #0d6efd;
      transform: scale(1.05);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    td {
      padding: 6px 0;
      vertical-align: top;
      color: #444;
    }
    td:first-child {
      width: 40%;
      font-weight: 600;
    }
    .pay-btn {
      display: block;
      width: 100%;
      text-align: center;
      margin-top: 25px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.25s ease;
    }
    .pay-btn:hover {
      background: #218838;
      transform: scale(1.03);
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #0d6efd;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="card">
    <h2>Pembayaran</h2>

    {{-- 1. Metode Pembayaran --}}
    <h3>1. Metode Pembayaran</h3>
    <div class="method-row">
      <img src="{{ asset('img/payments/qris.png') }}" alt="QRIS" onclick="selectMethod('QRIS')">
      <img src="{{ asset('img/payments/bri.png') }}" alt="BRI" onclick="selectMethod('BRI')">
      <img src="{{ asset('img/payments/bni.png') }}" alt="BNI" onclick="selectMethod('BNI')">
      <img src="{{ asset('img/payments/mandiri.png') }}" alt="Mandiri" onclick="selectMethod('Mandiri')">
    </div>

    {{-- 2. Informasi Penyewa --}}
    <h3>2. Informasi Penyewa</h3>
    <table>
      <tr><td>Nama Lengkap</td><td>: {{ auth()->user()->nama_lengkap }}</td></tr>
      <tr><td>Email</td><td>: {{ auth()->user()->email }}</td></tr>
    </table>

    {{-- 3. Detail Pesanan --}}
    <h3>3. Detail Pesanan</h3>
    <table>
      <tr>
        <td>Nama Mobil</td>
        <td>: {{ $payment->rental->car->brand->nama_merek ?? '-' }} {{ $payment->rental->car->model }}</td>
      </tr>
      <tr><td>Tahun Mobil</td><td>: {{ $payment->rental->car->tahun ?? '-' }}</td></tr>
      <tr><td>Harga Sewa</td><td>: Rp{{ number_format($payment->rental->car->harga_sewa_per_hari,0,',','.') }} / Hari</td></tr>
      <tr><td>Durasi</td><td>: {{ $payment->rental->durasi_hari }} Hari</td></tr>
      <tr><td>Kapasitas</td><td>: {{ $payment->rental->car->kapasitas_orang ?? '-' }} Orang</td></tr>
      <tr>
        <td>Pakai Sopir</td>
        <td>: {{ $payment->rental->driver === 'ya' ? 'Ya (+Rp150.000/hari)' : 'Tidak' }}</td>
      </tr>
      <tr>
        <td>Dari - Sampai</td>
        <td>: {{ \Carbon\Carbon::parse($payment->rental->tanggal_mulai)->format('d/m/y') }}
              s.d {{ \Carbon\Carbon::parse($payment->rental->tanggal_selesai)->format('d/m/y') }}</td>
      </tr>
      <tr>
        <td>Total Biaya Sewa</td>
        <td>: <b>Rp{{ number_format($payment->total_bayar,0,',','.') }}</b></td>
      </tr>
      <tr>
        <td>Lokasi Pengambilan</td>
        <td>: {{ $payment->rental->car->lokasi ?? 'Belum ditentukan' }}</td>
      </tr>
    </table>

    <button class="pay-btn" id="payNowBtn">Bayar</button>
    <a href="{{ route('user.payments.index') }}" class="back-link">← Kembali</a>
  </div>

  <script>
    let selectedMethod = null;

    function selectMethod(method) {
      selectedMethod = method;
      document.querySelectorAll('.method-row img').forEach(img => img.style.borderColor = '#ddd');
      event.target.style.borderColor = '#0d6efd';
    }

    document.getElementById('payNowBtn').addEventListener('click', () => {
      if (!selectedMethod) {
        alert('Silakan pilih metode pembayaran terlebih dahulu!');
        return;
      }

      // Simulasi redirect pembayaran (bisa diganti Midtrans Snap)
      alert('Metode ' + selectedMethod + ' dipilih.\nPembayaran sedang diproses...');
      window.location.href = "{{ route('user.payments.index') }}";
    });

    // Auto cancel jika user keluar halaman
    window.addEventListener('beforeunload', function () {
      navigator.sendBeacon(
        "{{ route('user.payments.cancelOnExit') }}",
        new Blob([JSON.stringify({ payment_id: "{{ $payment->payment_id }}" })], { type: 'application/json' })
      );
    });
  </script>
</body>
</html>
