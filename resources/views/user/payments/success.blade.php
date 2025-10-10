<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Transaksi</title>
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f6f7fb;margin:0;padding:30px;}
    .card{background:#fff;max-width:720px;margin:auto;padding:24px;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.08)}
    h2{text-align:center;margin:0 0 16px 0}
    table{width:100%;border-collapse:collapse}
    td{padding:8px 0;border-bottom:1px solid #eee}
    td:first-child{width:200px;font-weight:600}
    .badge{display:inline-block;padding:6px 10px;border-radius:8px;color:#fff;font-weight:700}
    .success{background:#28a745}.failed{background:#dc3545}
    a.btn{display:inline-block;background:#0d6efd;color:#fff;padding:10px 14px;border-radius:10px;text-decoration:none;margin-top:14px}
  </style>
</head>
<body>
  <div class="card">
    <h2>Detail Transaksi</h2>
    <table>
      <tr><td>ID Pembayaran</td><td>: {{ $payment->payment_id }}</td></tr>
      <tr><td>Metode</td><td>: {{ strtoupper($payment->metode) }}</td></tr>
      <tr><td>Mobil</td><td>: {{ $payment->rental->car->brand->nama_merek ?? '-' }} {{ $payment->rental->car->model ?? '-' }}</td></tr>
      <tr><td>Total</td><td>: Rp{{ number_format($payment->total_bayar,0,',','.') }}</td></tr>
      <tr>
        <td>Status</td>
        <td>:
          <span class="badge {{ $payment->status_pembayaran === 'success' ? 'success' : 'failed' }}">
            {{ $payment->status_pembayaran === 'success' ? 'Lunas' : 'Dibatalkan / Kadaluarsa' }}
          </span>
        </td>
      </tr>
      <tr><td>Dibuat</td><td>: {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i') }}</td></tr>
    </table>

    <a href="{{ route('user.payments.index') }}" class="btn">← Kembali ke daftar</a>
  </div>
</body>
</html>
