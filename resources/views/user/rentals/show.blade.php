<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Sewa Mobil | RentCar</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      padding: 25px;
      margin: 0;
    }

    .card {
      background: white;
      max-width: 600px;
      margin: auto;
      border-radius: 14px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      padding: 22px;
    }

    h2 {
      text-align: center;
      color: #111;
      margin-bottom: 18px;
    }

    p {
      margin: 6px 0;
      color: #444;
      font-size: 15px;
    }

    .label {
      font-weight: 600;
      color: #0d6efd;
    }

    .divider {
      border-top: 1px solid #eee;
      margin: 15px 0;
    }

    .status {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      margin-top: 5px;
    }

    .status.menunggu { background: #fff3cd; color: #856404; }
    .status.berjalan { background: #d1e7dd; color: #0f5132; }
    .status.selesai { background: #cfe2ff; color: #084298; }
    .status.dibatalkan { background: #f8d7da; color: #842029; }

    .back-btn {
      display: block;
      text-align: center;
      background: #0d6efd;
      color: #fff;
      padding: 10px 0;
      border-radius: 8px;
      text-decoration: none;
      margin-top: 20px;
      font-weight: 500;
      transition: background 0.25s;
    }

    .back-btn:hover { background: #0b5ed7; }

    .highlight {
      background: #f1f5ff;
      padding: 6px 10px;
      border-radius: 8px;
      color: #0d6efd;
      display: inline-block;
    }
  </style>
</head>
<body>

  <div class="card">
    <h2>Detail Penyewaan</h2>

    {{-- Informasi Mobil --}}
    <p>
      <span class="label">Mobil:</span>
      {{ $rental->car->tahun ?? '' }} {{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}
    </p>
    <p><span class="label">Warna:</span> {{ ucfirst($rental->car->warna ?? '-') }}</p>

    <div class="divider"></div>

    {{-- Informasi Waktu --}}
    <p><span class="label">Tanggal Mulai:</span> {{ $rental->tanggal_mulai }}</p>
    <p><span class="label">Tanggal Selesai:</span> {{ $rental->tanggal_selesai }}</p>
    <p><span class="label">Durasi:</span> {{ $rental->durasi_hari }} hari</p>

    <div class="divider"></div>

    {{-- Pickup dan Driver --}}
    <p><span class="label">Metode Pickup:</span> {{ ucfirst(str_replace('_', ' ', $rental->metode_pickup)) }}</p>
    <p><span class="label">Driver:</span> {{ ucfirst($rental->driver) }}</p>
    <p><span class="label">Lokasi Jemput:</span>
      <span class="highlight">{{ $rental->lokasi_pickup ?? 'Tidak ada lokasi jemput (ambil di tempat)' }}</span>
    </p>

    <div class="divider"></div>

    {{-- Biaya dan Status --}}
    <p><span class="label">Total Biaya:</span> Rp {{ number_format($rental->total_biaya, 0, ',', '.') }}</p>
    <p>
      <span class="label">Status:</span>
      <span class="status {{ $rental->status_rental }}">{{ ucfirst($rental->status_rental) }}</span>
    </p>

    <a href="{{ route('user.rentals.index') }}" class="back-btn">← Kembali ke Riwayat</a>
  </div>

</body>
</html>
