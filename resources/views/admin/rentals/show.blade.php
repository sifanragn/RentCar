<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Transaksi Pembayaran | Admin Panel</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;margin:0;padding:20px; }
    .card { background:white;padding:25px;border-radius:10px;max-width:800px;margin:auto;box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { margin-bottom:20px;color:#333; }
    .info { display:grid;grid-template-columns:180px 1fr;row-gap:8px;column-gap:10px;margin-bottom:15px; }
    .info label { font-weight:600;color:#333; }
    .status-box { display:inline-block;padding:5px 12px;border-radius:6px;font-size:13px;text-transform:capitalize; }
    .status-box.pending { background:#fff3cd;color:#856404; }
    .status-box.success { background:#d1e7dd;color:#0f5132; }
    .status-box.failed { background:#f8d7da;color:#842029; }
    .status-box.waiting { background:#ffeeba;color:#856404; }
    .back-link { display:inline-block;margin-top:20px;color:#0d6efd;text-decoration:none; }
    .back-link:hover { text-decoration:underline; }
    .btn-refresh { background:#0d6efd;color:white;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;margin-top:10px; }
    .btn-refresh:hover { background:#0b5ed7; }
  </style>
</head>
<body>

  <div class="card">
    <h2>Detail Transaksi Pembayaran</h2>

    {{-- Informasi Umum Penyewaan --}}
    <div class="info">
      <label>Penyewa:</label>
      <div>{{ $rental->user->nama_lengkap ?? '-' }} (ID: {{ $rental->user_id }})</div>

      <label>Email:</label>
      <div>{{ $rental->user->email ?? '-' }}</div>

      <label>Mobil:</label>
      <div>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</div>

      <label>Tanggal Mulai:</label>
      <div>{{ $rental->tanggal_mulai }}</div>

      <label>Tanggal Selesai:</label>
      <div>{{ $rental->tanggal_selesai }}</div>

      <label>Durasi:</label>
      <div>{{ $rental->durasi_hari }} Hari</div>

      <label>Driver:</label>
      <div>{{ ucfirst($rental->driver) }}</div>

      <label>Metode Pengambilan:</label>
      <div>{{ str_replace('_', ' ', ucfirst($rental->metode_pickup)) }}</div>

      <label>Total Biaya:</label>
      <div><strong>Rp{{ number_format($rental->total_biaya, 0, ',', '.') }}</strong></div>
    </div>

    <hr style="margin:20px 0;">

    {{-- Informasi Pembayaran --}}
    <h3>💳 Data Pembayaran</h3>

    @if($rental->payment)
      <div class="info">
        <label>Payment ID:</label>
        <div>{{ $rental->payment->payment_id }}</div>

        <label>Gateway:</label>
        <div>{{ $rental->payment->gateway ?? 'Midtrans' }}</div>

        <label>Metode:</label>
        <div>{{ ucfirst($rental->payment->metode ?? '-') }}</div>

        <label>Total Bayar:</label>
        <div><strong>Rp{{ number_format($rental->payment->total_bayar ?? 0, 0, ',', '.') }}</strong></div>

        <label>Status Pembayaran:</label>
        <div><span class="status-box {{ strtolower($rental->payment->status_pembayaran) }}">{{ ucfirst($rental->payment->status_pembayaran) }}</span></div>

        <label>Gateway Ref:</label>
        <div>{{ $rental->payment->gateway_reference ?? '-' }}</div>

        <label>Token Transaksi:</label>
        <div>{{ $rental->payment->payment_token ?? '-' }}</div>

        <label>Waktu Pembayaran:</label>
        <div>{{ $rental->payment->tanggal_bayar ?? '-' }}</div>
      </div>
    @else
      <p>❌ Belum ada data pembayaran untuk transaksi ini.</p>
    @endif

    <hr style="margin:25px 0;">

    {{-- Tombol Aksi --}}
    <form method="POST" action="{{ route('admin.rentals.updateStatus', $rental->rental_id) }}">
      @csrf
      <label for="status_rental">Ubah Status Penyewaan:</label>
      <select name="status_rental" required>
        <option value="">-- Pilih Status --</option>
        <option value="berjalan">🚗 Sedang Berjalan</option>
        <option value="selesai">✅ Selesai</option>
        <option value="dibatalkan">❌ Dibatalkan</option>
      </select>
      <br>
      <button type="submit" class="btn-refresh">Update Status</button>
    </form>

    <a href="{{ route('admin.rentals.index') }}" class="back-link">← Kembali ke daftar</a>
  </div>

</body>
</html>
