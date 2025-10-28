<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proses Pembayaran</title>
  <style>
    body {
      font-family:'Poppins',sans-serif;
      background:#f8f9fa;
      padding:25px;
      margin:0;
    }
    .card {
      max-width:650px;
      background:white;
      margin:auto;
      border-radius:14px;
      box-shadow:0 2px 10px rgba(0,0,0,0.08);
      padding:25px;
    }
    h2 { text-align:center;margin-bottom:20px; }
    table { width:100%;border-collapse:collapse;margin-bottom:15px; }
    td { padding:6px 0;border-bottom:1px solid #eee;vertical-align:top; }
    td:first-child { width:170px;font-weight:600; }
    .highlight {
      background:#e9f7ef;
      padding:10px;
      border-radius:8px;
      text-align:center;
      font-weight:600;
      color:#28a745;
      margin:15px 0;
    }
    .countdown {
      font-weight:700;
      color:#ff5722;
    }
    .btn {
      display:inline-block;
      width:100%;
      text-align:center;
      padding:12px;
      border-radius:10px;
      text-decoration:none;
      font-weight:600;
      font-size:15px;
      border:none;
      cursor:pointer;
      margin-top:8px;
      transition:.2s;
    }
    .btn-primary { background:#007bff;color:#fff; }
    .btn-primary:hover { background:#0056b3; }
    .btn-danger { background:#dc3545;color:#fff; }
    .btn-danger:hover { background:#b02a37; }
    .expired-box {
      background:#f8d7da;
      color:#842029;
      padding:12px;
      border-radius:8px;
      text-align:center;
      font-weight:600;
      margin-top:10px;
      display:none;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Pembayaran Sedang Diproses</h2>

    <table>
      <tr><td>ID Pembayaran</td><td>: {{ $payment->payment_id }}</td></tr>
      <tr><td>Nama Penyewa</td><td>: {{ $payment->rental->user->nama_lengkap ?? '-' }}</td></tr>
      <tr><td>Email</td><td>: {{ $payment->rental->user->email ?? '-' }}</td></tr>
      <tr><td>Metode Pembayaran</td><td>: {{ strtoupper($payment->metode) }}</td></tr>
      <tr><td>Nama Mobil</td><td>: {{ $payment->rental->car->brand->nama_merek ?? '-' }} {{ $payment->rental->car->model ?? '-' }}</td></tr>
      <tr><td>Tahun Mobil</td><td>: {{ $payment->rental->car->tahun ?? '-' }}</td></tr>
      <tr><td>Harga Sewa</td><td>: Rp{{ number_format($payment->rental->car->harga_sewa_per_hari ?? 0, 0, ',', '.') }} / Hari</td></tr>
      <tr><td>Kapasitas</td><td>: {{ $payment->rental->car->capacity->jumlah_orang ?? '-' }} Orang</td></tr>
      <tr><td>Durasi</td><td>: {{ $payment->rental->durasi_hari }} Hari</td></tr>
      <tr><td>Pake Sopir</td><td>: {{ $payment->rental->driver === 'ya' ? 'Ya (+Rp150.000/hari)' : 'Tidak' }}</td></tr>
      <tr><td>Dari - Sampai</td><td>: {{ $payment->rental->tanggal_mulai }} s.d {{ $payment->rental->tanggal_selesai }}</td></tr>
      <tr><td>Total Bayar</td><td>: <b>Rp{{ number_format($payment->total_bayar, 0, ',', '.') }}</b></td></tr>
    </table>

    {{-- ✅ Countdown Info --}}
    <div class="highlight">
      Waktu tersisa untuk menyelesaikan pembayaran:
      <span id="countdown" class="countdown">--:--</span>
    </div>

    {{-- ✅ Tampilkan QRIS / Rekening (sesuai metode) --}}
    @if($payment->metode === 'qris')
      <div style="text-align:center;margin-bottom:15px;">
        <img src="{{ asset('img/qris-example.png') }}" alt="QRIS" width="200">
        <p style="font-size:14px;">Scan kode QR di atas untuk melakukan pembayaran.</p>
      </div>
    @elseif($payment->metode === 'bca')
      <div class="highlight">
        Transfer ke Rekening BCA <br>
        <b>1234567890 a.n RentalMobil.ID</b>
      </div>
    @elseif($payment->metode === 'bri')
      <div class="highlight">
        Transfer ke Rekening BRI <br>
        <b>9876543210 a.n RentalMobil.ID</b>
      </div>
    @endif

    {{-- ✅ Tombol Batalkan --}}
    <form action="{{ route('user.payments.cancelSoft', $payment->payment_id) }}" method="POST" id="cancelForm">
      @csrf
      <button type="submit" class="btn btn-danger">Batalkan Pembayaran</button>
    </form>

    <div class="expired-box" id="expiredBox">
      ⚠️ Waktu pembayaran telah habis. Silakan lakukan penyewaan ulang.
    </div>
  </div>

  @php
    // Hitung sisa waktu (detik) dari server
    $expireAt = \Carbon\Carbon::parse($payment->created_at)->addMinutes(30);
    $remaining = max(0, now()->diffInSeconds($expireAt, false));
  @endphp

  <script>
    // =================== COUNTDOWN ===================
    let remaining = {{ $remaining }};
    const el = document.getElementById('countdown');
    const expiredBox = document.getElementById('expiredBox');
    const cancelBtn = document.querySelector('.btn-danger');

    function format(sec){
      const m = Math.floor(sec / 60);
      const s = sec % 60;
      return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    function tick(){
      if(remaining <= 0){
        el.innerText = "00:00";
        expiredBox.style.display = 'block';
        cancelBtn.style.display = 'none';
        return;
      }
      el.innerText = format(remaining);
      remaining--;
      setTimeout(tick, 1000);
    }
    tick();

    // =================== AUTO EXPIRE CHECK ===================
    setInterval(()=>{
      fetch("{{ route('user.payments.checkExpired') }}")
        .then(res => res.json())
        .then(data => {
          if(data.expired_updated > 0){
            window.location.reload();
          }
        });
    }, 60000); // tiap 1 menit

    // =================== AUTO STATUS CHECK ===================
    const paymentId = "{{ $payment->payment_id }}";
    setInterval(() => {
      fetch(`/user/payments/check-status/${paymentId}`)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            alert('✅ Pembayaran berhasil! Anda akan diarahkan ke halaman sukses.');
            window.location.href = `/user/payments/show/${paymentId}`;
          } else if (data.status === 'failed') {
            alert('❌ Pembayaran gagal atau dibatalkan.');
            window.location.href = `/user/payments/index`;
          }
        })
        .catch(err => console.error('Error cek status:', err));
    }, 10000); // tiap 10 detik

    setInterval(() => {
  fetch('/user/payments/status-list')
    .then(res => res.json())
    .then(list => {
      list.forEach(p => {
        if (p.status_pembayaran !== 'pending') {
          window.location.href = '/user/payments'; // redirect jika sudah sukses/gagal
        }
      });
    });
}, 5000); // cek setiap 5 detik

  </script>
</body>
</html>
