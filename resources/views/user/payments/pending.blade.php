<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran</title>
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;padding:30px;margin:0;}
    .wrap{max-width:720px;margin:auto;background:#fff;border-radius:16px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,.08)}
    h2{margin:0 0 14px 0}
    h3{margin:18px 0 10px 0}
    .methods{display:flex;gap:12px;flex-wrap:wrap}
    .m{border:2px solid #e5e7eb;border-radius:12px;padding:10px 14px;cursor:pointer}
    .m.active{border-color:#0d6efd;background:#eef4ff}
    table{width:100%;border-collapse:collapse}
    td{padding:8px 0;border-bottom:1px solid #eee}
    td:first-child{width:200px;font-weight:600}
    .rowbtn{display:flex;gap:10px;margin-top:16px}
    .btn{padding:10px 14px;border-radius:10px;text-decoration:none;color:#fff;display:inline-block}
    .primary{background:#0d6efd}.secondary{background:#6c757d}
    .note{background:#f1f3f5;border-radius:8px;padding:10px;text-align:center;font-weight:700;margin:12px 0;}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Pembayaran</h2>

    <h3>1. Metode Pembayaran</h3>
    <div class="methods" id="methods">
      <div class="m active" data-method="qris">QRIS</div>
      <div class="m" data-method="bri" style="opacity:.5;pointer-events:none">BANK BRI (segera)</div>
      <div class="m" data-method="bni" style="opacity:.5;pointer-events:none">BNI (segera)</div>
      <div class="m" data-method="mandiri" style="opacity:.5;pointer-events:none">Mandiri (segera)</div>
    </div>

    <h3>2. Informasi Penyewa</h3>
    <table>
      <tr><td>Nama Lengkap</td><td>: {{ auth()->user()->nama_lengkap ?? '-' }}</td></tr>
      <tr><td>Email</td><td>: {{ auth()->user()->email }}</td></tr>
    </table>

    <h3>3. Detail Pesanan</h3>
    <table>
      <tr><td>Nama Mobil</td><td>: {{ $payment->rental->car->brand->nama_merek ?? '-' }} {{ $payment->rental->car->model ?? '-' }}</td></tr>
      <tr><td>Tahun Mobil</td><td>: {{ $payment->rental->car->tahun ?? '-' }}</td></tr>
      <tr><td>Harga Sewa</td><td>: Rp{{ number_format($payment->rental->car->harga_sewa_per_hari,0,',','.') }} / Hari</td></tr>
      <tr><td>Durasi</td><td>: {{ $payment->rental->durasi_hari }} Hari</td></tr>
      <tr><td>Pakai Sopir</td><td>: {{ $payment->rental->driver === 'ya' ? 'Ya (+Rp150.000/hari)' : 'Tidak' }}</td></tr>
      <tr><td>Dari - Sampai</td><td>: {{ $payment->rental->tanggal_mulai }} s.d {{ $payment->rental->tanggal_selesai }}</td></tr>
      <tr><td>Total Biaya Sewa</td><td>: <b>Rp{{ number_format($payment->total_bayar,0,',','.') }}</b></td></tr>
      <tr><td>Lokasi Pengambilan</td><td>: {{ $payment->rental->metode_pickup === 'pickup_alamat' ? 'Antar ke alamat' : 'Ambil di lokasi' }}</td></tr>
    </table>

    <div class="note">
      Status: <b>Pending</b>. Kamu punya waktu 30 menit untuk menyelesaikan pembayaran.
    </div>

    <div class="rowbtn">
      <a class="btn secondary" href="{{ route('user.rentals.create', $payment->rental->car_id) }}">← Kembali</a>
      <a class="btn primary" href="{{ route('user.payments.process', $payment->payment_id) }}">Bayar</a>
    </div>
  </div>

  <script>
    // toggle UI metode (untuk sekarang hanya qris yang aktif)
    let selected = 'qris';
    document.getElementById('methods').addEventListener('click', e=>{
      const m = e.target.closest('.m'); if(!m || m.style.pointerEvents==='none') return;
      document.querySelectorAll('.m').forEach(x=>x.classList.remove('active'));
      m.classList.add('active'); selected = m.dataset.method;
    });
  </script>
</body>
</html>
