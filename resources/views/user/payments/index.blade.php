<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Pembayaran</title>
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;padding:30px;}
    .container{max-width:980px;margin:auto;}
    h2{text-align:center;margin-bottom:24px;color:#333}
    table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.08)}
    th,td{padding:12px 14px;border-bottom:1px solid #eee;text-align:left}
    thead{background:#0d6efd;color:#fff}
    .status{padding:6px 10px;border-radius:6px;color:#fff;font-weight:700}
    .pending{background:#ffc107}.success{background:#28a745}.failed{background:#dc3545}
    .btn{background:#0d6efd;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none}
  </style>
</head>
<body>
<div class="container">
  <h2>Daftar Pembayaran</h2>
  @if($payments->isEmpty())
    <p>Belum ada pembayaran.</p>
  @else
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Mobil</th>
          <th>Tanggal</th>
          <th>Total</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      @foreach($payments as $i => $p)
        @php
          $exp = now()->diffInSeconds(\Carbon\Carbon::parse($p->created_at)->addMinutes(30), false);
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $p->rental->car->brand->nama_merek ?? '-' }} {{ $p->rental->car->model ?? '-' }}</td>
          <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
          <td>Rp{{ number_format($p->total_bayar,0,',','.') }}</td>
          <td>
            <span class="status {{ strtolower($p->status_pembayaran) }}">
              @if($p->status_pembayaran === 'pending')
                Menunggu (<span class="cd" data-s="{{ max(0,$exp) }}">--:--</span>)
              @elseif($p->status_pembayaran === 'success')
                Lunas
              @else
                Dibatalkan
              @endif
            </span>
          </td>
          <td>
            @if($p->status_pembayaran === 'pending')
              <a class="btn" href="{{ route('user.payments.process', $p->payment_id) }}">Detail</a>
            @else
              <a class="btn" href="{{ route('user.payments.show', $p->payment_id) }}">Detail</a>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  @endif
</div>

<script>
const toMMSS = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
document.querySelectorAll('.cd').forEach(el=>{
  let s = +el.dataset.s || 0;
  if (s <= 0) { el.textContent = '00:00'; return; }
  const tick = () => { el.textContent = toMMSS(s); if (s-- > 0) setTimeout(tick, 1000); };
  tick();
});

// ping backend tiap 30 detik untuk mark expired
setInterval(() => {
  fetch("{{ route('user.payments.checkExpired') }}")
    .then(r => r.json())
    .then(d => { if ((d.expired_updated||0) > 0) location.reload(); });
}, 30000);
</script>
</body>
</html>
