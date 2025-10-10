<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Penyewaan | Admin Panel</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;margin:0;padding:20px; }
    h2 { color:#333;margin-bottom:20px; }
    table { width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden; }
    th,td { padding:10px;border-bottom:1px solid #eee;text-align:left; }
    th { background:#0d6efd;color:white; }
    tr:hover { background:#f1f5ff; }
    .status { padding:4px 10px;border-radius:6px;font-size:13px; }
    .status.verifikasi_diperlukan { background:#ffeeba;color:#856404; }
    .status.menunggu { background:#fff3cd;color:#856404; }
    .status.berjalan { background:#d1e7dd;color:#0f5132; }
    .status.selesai { background:#cfe2ff;color:#084298; }
    .status.dibatalkan { background:#f8d7da;color:#842029; }
    .btn-view { text-decoration:none;background:#0d6efd;color:#fff;padding:6px 12px;border-radius:6px; }
    .btn-view:hover { background:#0b5ed7; }
  </style>
</head>
<body>

  <h2>Daftar Semua Penyewaan</h2>

  @if($rentals->isEmpty())
    <p>Belum ada transaksi penyewaan.</p>
  @else
    <table>
      <thead>
        <tr>
          <th>Penyewa</th>
          <th>Mobil</th>
          <th>Tanggal Sewa</th>
          <th>Tanggal Selesai</th>
          <th>Total Biaya</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rentals as $rental)
          <tr>
            <td>{{ $rental->user->name ?? '-' }}</td>
            <td>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</td>
            <td>{{ $rental->tanggal_mulai }}</td>
            <td>{{ $rental->tanggal_selesai }}</td>
            <td>Rp{{ number_format($rental->total_biaya, 0, ',', '.') }}</td>
            <td><span class="status {{ $rental->status_rental }}">{{ ucfirst(str_replace('_', ' ', $rental->status_rental)) }}</span></td>
            <td><a href="{{ route('admin.rentals.show', $rental->rental_id) }}" class="btn-view">Detail</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

</body>
</html>
