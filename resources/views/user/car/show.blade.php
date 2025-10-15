<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $car->brand->nama_merek ?? 'Detail Mobil' }}</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;margin:0;padding:20px; }
    .container { max-width:800px;margin:auto;background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    img { width:100%;border-radius:12px;margin-bottom:20px;object-fit:cover; }
    h2 { margin-bottom:10px;color:#222; }
    p { margin:5px 0;color:#555; }
    .price { font-size:22px;color:#000;font-weight:bold;margin-top:10px; }
    .btn {
      display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;
      border-radius:8px;margin-top:20px;font-weight:600;
    }
    .btn:hover { background:#0056b3; }
    .alert {
      margin-top:20px;padding:15px;border-radius:8px;
    }
    .alert-warning {
      background:#fff3cd;border:1px solid #ffeeba;color:#856404;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="{{ asset('storage/'.$car->foto) }}" alt="{{ $car->model }}">
    <h2>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h2>
    <p>Tahun: {{ $car->tahun }}</p>
    <p>Warna: {{ ucfirst($car->warna ?? '-') }}</p>
    <p>Transmisi: {{ ucfirst($car->tipe_transmisi ?? '-') }}</p>
    <p>Kapasitas: {{ $car->capacity->jumlah_orang ?? '-' }} orang</p>
    <p>Lokasi: {{ ucfirst($car->lokasi ?? '-') }}</p>
    <p class="price">Rp {{ number_format($car->harga_sewa_per_hari,0,',','.') }} / hari</p>

    @php
      $rental = \App\Models\Rental::where('car_id', $car->car_id)
          ->whereIn('status_rental', ['verifikasi_diperlukan','menunggu','menunggu_pembayaran','berjalan'])
          ->latest()
          ->first();
    @endphp

    @if($rental && $rental->status_rental === 'menunggu_pembayaran')
      <div class="alert alert-warning">
        ⚠️ Mobil ini sedang <b>menunggu pembayaran penyewa</b>.  
        Silakan pilih mobil lain terlebih dahulu.
      </div>
    @elseif($rental)
      <div class="alert alert-warning">
        🚫 Mobil ini sedang disewa oleh pengguna lain.  
        Silakan pilih mobil lain.
      </div>
    @else
      <a href="{{ route('user.rentals.create', $car->car_id) }}" class="btn">🚗 Sewa Mobil Ini</a>
    @endif
  </div>
</body>
</html>
