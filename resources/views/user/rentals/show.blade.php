<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Sewa Mobil</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
    .card { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    h2 { color: #333; }
    p { margin: 6px 0; color: #555; }
    .label { font-weight: bold; color: #0d6efd; }
    .back-btn { display:inline-block; margin-top:20px; background:#0d6efd; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; }
    .back-btn:hover { background:#0b5ed7; }
  </style>
</head>
<body>

  <div class="card">
    <h2>Detail Penyewaan</h2>
    <p><span class="label">Mobil:</span> {{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</p>
    <p><span class="label">Tanggal Mulai:</span> {{ $rental->tanggal_mulai }}</p>
    <p><span class="label">Tanggal Selesai:</span> {{ $rental->tanggal_selesai }}</p>
    <p><span class="label">Durasi:</span> {{ $rental->durasi_hari }} hari</p>
    <p><span class="label">Metode Pickup:</span> {{ ucfirst(str_replace('_', ' ', $rental->metode_pickup)) }}</p>
    <p><span class="label">Driver:</span> {{ ucfirst($rental->driver) }}</p>
    <p><span class="label">Total Biaya:</span> Rp {{ number_format($rental->total_biaya, 0, ',', '.') }}</p>
    <p><span class="label">Status:</span> {{ ucfirst($rental->status_rental) }}</p>

    <a href="{{ route('user.rentals.index') }}" class="back-btn">← Kembali</a>
  </div>

</body>
</html>
