<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Transaksi | Admin Panel</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f5f7fa;margin:0;padding:30px; }
    .container { max-width:900px;margin:auto;background:white;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.08);overflow:hidden; }
    header { background:#0d6efd;color:white;padding:18px 25px; }
    header h2 { margin:0;font-size:20px; }
    section { padding:25px; }
    .grid { display:grid;grid-template-columns:180px 1fr;row-gap:10px;column-gap:10px; }
    .grid label { font-weight:600;color:#333; }
    .status { display:inline-block;padding:6px 12px;border-radius:20px;font-size:13px;font-weight:600;text-transform:capitalize; }
    .status.pending { background:#fff3cd;color:#856404; }
    .status.success { background:#d1e7dd;color:#0f5132; }
    .status.failed { background:#f8d7da;color:#842029; }
    .status.waiting { background:#e2e3e5;color:#41464b; }
    .card-section { background:#fafafa;border-radius:8px;padding:18px;margin-bottom:25px; }
    hr { border:none;height:1px;background:#ddd;margin:25px 0; }
    .btn { display:inline-block;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;font-weight:600;text-decoration:none; }
    .btn-blue { background:#0d6efd;color:white; }
    .btn-blue:hover { background:#0b5ed7; }
    .btn-danger { background:#dc3545;color:white; }
    .btn-danger:hover { background:#bb2d3b; }
    .btn-green { background:#198754;color:white; }
    .btn-green:hover { background:#157347; }
    .back-link { color:#0d6efd;text-decoration:none;margin-top:20px;display:inline-block; }
    .back-link:hover { text-decoration:underline; }
    select { padding:6px 10px;border-radius:6px;border:1px solid #ccc;margin-top:8px; }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h2>Detail Transaksi Penyewaan</h2>
    </header>

    <section>
      <h3 style="margin-top:0;">🧾 Data Penyewaan</h3>
      <div class="grid">
        <label>Penyewa:</label>
        <div>{{ $rental->user->nama_lengkap ?? '-' }} (ID: {{ $rental->user_id }})</div>

        <label>Email:</label>
        <div>{{ $rental->user->email ?? '-' }}</div>

        <label>Mobil:</label>
        <div>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model ?? '' }}</div>

        <label>Tanggal Sewa:</label>
        <div>{{ $rental->tanggal_mulai }} → {{ $rental->tanggal_selesai }} ({{ $rental->durasi_hari }} hari)</div>

        <label>Driver:</label>
        <div>{{ ucfirst($rental->driver) }}</div>

        <label>Metode Pengambilan:</label>
        <div>{{ str_replace('_', ' ', ucfirst($rental->metode_pickup)) }}</div>

        <label>Total Biaya:</label>
        <div><strong>Rp{{ number_format($rental->total_biaya, 0, ',', '.') }}</strong></div>

        <label>Status Sewa:</label>
        <div><span class="status {{ strtolower($rental->status_rental) }}">{{ ucfirst($rental->status_rental) }}</span></div>
      </div>

      {{-- 🔹 Tampilkan tombol buat invoice jika sewa selesai --}}
      @if(in_array($rental->status_rental, ['selesai', 'dikembalikan']))
        <hr>
        <a href="{{ route('admin.invoices.create', $rental->rental_id) }}" class="btn btn-green">
          🧾 Buat Invoice
        </a>
      @endif
    </section>

    <section class="card-section">
      <h3>💳 Data Pembayaran</h3>

      @if($rental->payment)
        <div class="grid">
          <label>Payment ID:</label>
          <div>#{{ $rental->payment->payment_id }}</div>

          <label>Gateway:</label>
          <div>{{ $rental->payment->gateway }}</div>

          <label>Metode:</label>
          <div>{{ strtoupper($rental->payment->metode) }}</div>

          <label>Total Bayar:</label>
          <div><strong>Rp{{ number_format($rental->payment->total_bayar, 0, ',', '.') }}</strong></div>

          <label>Status Pembayaran:</label>
          <div>
            <span class="status {{ strtolower($rental->payment->status_pembayaran) }}">
              {{ ucfirst($rental->payment->status_pembayaran) }}
            </span>
            @if($rental->payment->status_pembayaran === 'pending')
              <form method="POST" action="{{ route('admin.payments.refresh', $rental->payment->payment_id) }}" style="display:inline;">
                @csrf
                <button class="btn btn-blue" style="margin-left:10px;">Refresh Status</button>
              </form>
            @endif
          </div>

          <label>Gateway Reference:</label>
          <div>{{ $rental->payment->gateway_reference }}</div>

          <label>Tanggal Bayar:</label>
          <div>{{ $rental->payment->tanggal_bayar ?? '-' }}</div>

          <label>Link Transaksi:</label>
          <div>
            <a href="{{ $rental->payment->payment_token }}" target="_blank" style="color:#0d6efd;">
              Lihat di Duitku ↗
            </a>
          </div>
        </div>
      @else
        <p style="color:#dc3545;font-weight:500;">❌ Belum ada data pembayaran untuk transaksi ini.</p>
      @endif
    </section>

    <section>
      <form method="POST" action="{{ route('admin.rentals.updateStatus', $rental->rental_id) }}">
        @csrf
        <label for="status_rental">Ubah Status Penyewaan:</label><br>
        <select name="status_rental" id="status_rental" required>
          <option value="">-- Pilih Status --</option>
          <option value="berjalan">🚗 Sedang Berjalan</option>
          <option value="selesai">✅ Selesai</option>
          <option value="dikembalikan">📦 Dikembalikan</option>
          <option value="dibatalkan">❌ Dibatalkan</option>
        </select>
        <br><br>
        <button type="submit" class="btn btn-blue">Update Status</button>
      </form>

      <a href="{{ route('admin.rentals.index') }}" class="back-link">← Kembali ke daftar</a>
    </section>
  </div>
</body>
</html>
