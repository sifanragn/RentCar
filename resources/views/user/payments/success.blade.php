<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>
    {{ $payment->payment_type === 'charge' ? 'Kuitansi Tambahan' : 'Kuitansi' }} 
    - {{ strtoupper($payment->status_pembayaran) }}
  </title>
  <style>
    body { font-family:'Poppins',sans-serif;background:#f5f7fa;text-align:center;padding:40px; }
    .box {
      background:white;max-width:600px;margin:auto;border-radius:16px;padding:35px;
      box-shadow:0 4px 12px rgba(0,0,0,0.08);
    }
    h1 {
      font-size:24px;
      margin-bottom:8px;
      color:#333;
    }
    h2 { margin-bottom:8px; }
    p { font-size:15px;color:#555; }
    .success { color:#28a745; }
    .failed { color:#dc3545; }
    .pending { color:#ffc107; }
    .btn {
      background:#0d6efd;color:white;padding:10px 18px;text-decoration:none;
      border-radius:8px;display:inline-block;margin-top:20px;
    }
    .btn:hover { opacity:0.9; }
    table {
      width:100%;border-collapse:collapse;margin-top:20px;
      text-align:left;font-size:14px;
    }
    th, td { padding:8px 10px; }
    th { width:40%;color:#444; }
    tr:nth-child(odd) td { background:#f9f9f9; }
    .tag {
      display:inline-block;padding:4px 10px;border-radius:12px;
      font-size:13px;font-weight:600;text-transform:capitalize;
    }
    .tag.success { background:#d1e7dd;color:#0f5132; }
    .tag.failed { background:#f8d7da;color:#842029; }
    .tag.pending { background:#fff3cd;color:#664d03; }
  </style>
</head>
<body>
  <div class="box">

    {{-- 🧾 Judul Kuitansi --}}
    <h1>
      {{ $payment->payment_type === 'charge' ? 'Kuitansi Tambahan' : 'Kuitansi' }}
    </h1>

    {{-- ✅ STATUS HEADER --}}
    @if($payment->status_pembayaran === 'success')
      <h2 class="success">✅ Pembayaran Berhasil!</h2>
      <p>Terima kasih! Pembayaran kamu telah dikonfirmasi dan transaksi dinyatakan lunas.</p>
    @elseif($payment->status_pembayaran === 'failed')
      <h2 class="failed">❌ Pembayaran Gagal</h2>
      <p>Transaksi kamu dibatalkan atau gagal diproses. Silakan hubungi admin atau coba ulang pembayaran.</p>
    @else
      <h2 class="pending">⏳ Pembayaran Masih Pending</h2>
      <p>Silakan lanjutkan pembayaran melalui tautan di bawah ini sebelum waktu habis.</p>
      <a href="{{ $payment->payment_token }}" target="_blank" class="btn">Lanjutkan Pembayaran di Duitku</a>
    @endif

    {{-- 📋 Detail Transaksi (tampilkan hanya jika success / failed) --}}
    @if(in_array($payment->status_pembayaran, ['success','failed']))
      <table>
        <tr><th>Payment ID:</th><td>#{{ $payment->payment_id }}</td></tr>
        <tr><th>Gateway:</th><td>{{ $payment->gateway ?? '-' }}</td></tr>
        <tr><th>Metode:</th><td>{{ strtoupper($payment->metode ?? '-') }}</td></tr>
        <tr><th>Tipe:</th><td>{{ ucfirst($payment->payment_type ?? '-') }}</td></tr>
        <tr><th>Status Pembayaran:</th>
            <td><span class="tag {{ strtolower($payment->status_pembayaran) }}">
              {{ ucfirst($payment->status_pembayaran) }}</span></td></tr>
        <tr><th>Total Bayar:</th><td><strong>Rp {{ number_format($payment->total_bayar, 0, ',', '.') }}</strong></td></tr>
        <tr><th>Tanggal Bayar:</th><td>{{ $payment->tanggal_bayar ?? '-' }}</td></tr>
        <tr><th>Gateway Ref:</th><td>{{ $payment->gateway_reference ?? '-' }}</td></tr>
      </table>

      <a href="{{ route('user.payments.index') }}" class="btn" style="background:#28a745;">Kembali</a>
    @endif

  </div>
</body>
</html>
