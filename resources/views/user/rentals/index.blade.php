<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penyewaan Saya | RentCar</title>
  <style>
    body { font-family: 'Poppins', sans-serif; background:#f8f9fa; margin:0; padding:25px; }
    h2 { text-align:center; color:#222; margin-bottom:25px; }

    .rentals-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 18px;
    }

    .card {
      background:white;
      border-radius:14px;
      padding:18px;
      box-shadow:0 2px 6px rgba(0,0,0,0.08);
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      transition:0.25s;
    }
    .card:hover { transform: translateY(-4px); }

    .car-info h4 {
      margin:0;
      color:#111;
      font-size:18px;
      font-weight:600;
    }
    .car-info p { margin:4px 0; color:#666; font-size:14px; }

    .status {
      display:inline-block;
      padding:4px 10px;
      border-radius:8px;
      font-size:13px;
      font-weight:500;
      margin-top:8px;
    }
    .status.menunggu { background:#fff3cd; color:#856404; }
    .status.berjalan { background:#d1e7dd; color:#0f5132; }
    .status.selesai { background:#cfe2ff; color:#084298; }
    .status.dibatalkan { background:#f8d7da; color:#842029; }

    .btn-detail {
      text-decoration:none;
      text-align:center;
      background:#0d6efd;
      color:white;
      padding:8px 0;
      border-radius:8px;
      font-size:14px;
      font-weight:500;
      margin-top:12px;
      transition:background 0.25s;
    }
    .btn-detail:hover { background:#0b5ed7; }

    .empty {
      text-align:center;
      color:#666;
      font-size:15px;
      margin-top:30px;
    }
  </style>
</head>
<body>

  <h2>Riwayat Penyewaan Mobil</h2>

  @if(session('success'))
    <div style="background:#d1e7dd;color:#0f5132;padding:10px;margin-bottom:15px;border-radius:6px;">
      {{ session('success') }}
    </div>
  @endif

  @if($rentals->isEmpty())
    <p class="empty">Belum ada penyewaan mobil yang tercatat.</p>
  @else
    <div class="rentals-container">
      @foreach($rentals as $rental)
        <div class="card">
          <div class="car-info">
            <h4>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</h4>
            <p><b>Tanggal Sewa:</b> {{ $rental->tanggal_mulai }}</p>
            <p><b>Selesai:</b> {{ $rental->tanggal_selesai }}</p>
            <p><b>Durasi:</b> {{ $rental->durasi_hari }} hari</p>
            <p><b>Total:</b> Rp {{ number_format($rental->total_biaya,0,',','.') }}</p>
            <span class="status {{ $rental->status_rental }}">{{ ucfirst($rental->status_rental) }}</span>
          </div>
          <a href="{{ route('user.rentals.show', $rental->rental_id) }}" class="btn-detail">Lihat Detail</a>
        </div>
      @endforeach
    </div>
  @endif

</body>
</html>
