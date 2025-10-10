<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Proses Pembayaran</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;padding:30px;margin:0;}
    .card{background:white;padding:24px;border-radius:14px;max-width:640px;margin:auto;box-shadow:0 2px 10px rgba(0,0,0,.08)}
    h2{text-align:center;margin:0 0 14px 0}
    .center{text-align:center}
    .qris{background:#f7f7f7;border-radius:12px;padding:16px;display:inline-block}
    .count{font-weight:700;color:#e5533d}
    .rowbtn{display:flex;gap:10px;justify-content:center;margin-top:16px}
    .btn{padding:10px 16px;border:none;border-radius:10px;color:#fff;cursor:pointer;font-weight:700}
    .danger{background:#dc3545}.secondary{background:#6c757d}
  </style>
</head>
<body>
  <div class="card">
    <h2>Pembayaran via QRIS</h2>

    <div class="center" style="margin-top:10px">
      <div class="qris">
        {{-- Ganti dengan QR kamu sendiri di public/img/qris.png --}}
        <img src="{{ asset('img/qris.png') }}" alt="QRIS" width="220">
      </div>
      <p style="margin-top:10px">Silakan scan QR ini menggunakan aplikasi pembayaran Anda.</p>
      <p>Sisa waktu: <span id="cd" class="count">--:--</span></p>
    </div>

    <div class="rowbtn">
      <form method="POST" action="{{ route('user.payments.cancel', $payment->payment_id) }}">
        @csrf
        <button class="btn danger" type="submit">Batalkan</button>
      </form>
      <a class="btn secondary" href="{{ route('user.payments.index') }}" style="text-decoration:none;line-height:38px">Ke Daftar</a>
    </div>
  </div>

  <script>
    // COUNTDOWN dari created_at + 30 menit
    const createdAt = new Date("{{ $payment->created_at }}").getTime();
    const expireAt  = createdAt + 30 * 60 * 1000;
    const el        = document.getElementById('cd');

    function fmt(sec){return `${String(Math.floor(sec/60)).padStart(2,'0')}:${String(sec%60).padStart(2,'0')}`;}

    const tick = () => {
      const now = Date.now();
      let left = Math.floor((expireAt - now)/1000);
      if (left <= 0) {
        el.textContent = '00:00';
        // biar status di DB ikut update, panggil checkExpired lalu balik ke index
        fetch("{{ route('user.payments.checkExpired') }}").finally(()=> location.href = "{{ route('user.payments.index') }}");
        return;
      }
      el.textContent = fmt(left);
      setTimeout(tick, 1000);
    };
    tick();

    // ping backend tiap 20 detik supaya status expired otomatis
    setInterval(()=>{
      fetch("{{ route('user.payments.checkExpired') }}")
        .then(r=>r.json())
        .then(d=>{ if((d.expired_updated||0)>0) location.href="{{ route('user.payments.index') }}"; });
    }, 20000);
  </script>
</body>
</html>
