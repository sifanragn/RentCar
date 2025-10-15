<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Pembayaran</title>
  <style>
    body{
      font-family:'Poppins',Arial,sans-serif;
      background:#f8f9fa;
      padding:30px;
    }
    .container{
      max-width:980px;
      margin:auto;
    }
    h2{
      text-align:center;
      margin-bottom:24px;
      color:#333;
    }
    table{
      width:100%;
      border-collapse:collapse;
      background:#fff;
      border-radius:10px;
      overflow:hidden;
      box-shadow:0 2px 6px rgba(0,0,0,.08);
    }
    th,td{
      padding:12px 14px;
      border-bottom:1px solid #eee;
      text-align:left;
    }
    thead{
      background:#0d6efd;
      color:#fff;
    }
    .status{
      padding:6px 10px;
      border-radius:6px;
      color:#fff;
      font-weight:700;
    }
    .pending{background:#ffc107;}
    .success{background:#28a745;}
    .failed{background:#dc3545;}
    .btn{
      background:#0d6efd;
      color:#fff;
      padding:8px 12px;
      border-radius:8px;
      text-decoration:none;
      border:none;
      cursor:pointer;
      margin-right:4px;
    }
    .btn:hover{ background:#0b5ed7; }
    .btn-danger{ background:#dc3545; }
    .btn-danger:hover{ background:#b02a37; }
    .btn-back{
      display:inline-block;
      margin-top:25px;
      background:#6c757d;
      color:#fff;
      padding:10px 18px;
      border-radius:8px;
      text-decoration:none;
      font-weight:600;
      transition:.2s;
    }
    .btn-back:hover{ background:#5a6268; }
    td form{ display:inline; }

    /* 🧾 Modal Kuitansi */
    .modal-overlay {
      position:fixed;
      inset:0;
      background:rgba(0,0,0,0.55);
      display:none;
      align-items:center;
      justify-content:center;
      z-index:1000;
    }
    .modal-box {
      background:#fff;
      border-radius:20px;
      padding:30px;
      width:90%;
      max-width:500px;
      box-shadow:0 6px 18px rgba(0,0,0,.2);
      position:relative;
      animation:fadeIn .3s ease;
      text-align:center;
    }
    @keyframes fadeIn {
      from{opacity:0; transform:translateY(20px);}
      to{opacity:1; transform:translateY(0);}
    }
    .close-btn {
      position:absolute;
      top:12px; right:15px;
      background:none;
      border:none;
      font-size:22px;
      cursor:pointer;
      color:#555;
    }
    .receipt-title { font-size:22px;font-weight:600;margin-bottom:6px;color:#222; }
    .receipt-status { font-weight:600;font-size:15px;margin:8px 0; }
    .receipt-status.success{color:#28a745;}
    .receipt-status.failed{color:#dc3545;}
    .receipt-status.pending{color:#ffc107;}
    .receipt-subtitle { color:#666;font-size:14px;margin-bottom:18px; }
    .receipt-table{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;}
    .receipt-table th,.receipt-table td{padding:8px 6px;text-align:left;}
    .receipt-table tr:nth-child(odd){background:#f9f9f9;}
    .btn-link{
      display:inline-block;
      background:#0d6efd;
      color:white;
      padding:8px 14px;
      border-radius:8px;
      margin-top:18px;
      text-decoration:none;
      font-size:14px;
    }
    .btn-link:hover{background:#0b5ed7;}
  </style>
</head>
<body>
<div class="container">
  <h2>Daftar Pembayaran</h2>

  @if(session('warning'))
    <div class="alert alert-warning" style="margin:10px 0;">{{ session('warning') }}</div>
  @endif

  @if($payments->isEmpty())
    <p>Belum ada pembayaran.</p>
  @else
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Mobil</th>
          <th>Jenis Kuitansi</th>
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

          switch($p->payment_type){
            case 'main': $jenis = 'Kuitansi Utama'; $warna = '#0d6efd'; break;
            case 'charge': $jenis = 'Kuitansi Tambahan'; $warna = '#e67e22'; break;
            case 'final': $jenis = 'Kuitansi Akhir'; $warna = '#28a745'; break;
            default: $jenis = ucfirst($p->payment_type); $warna = '#555';
          }
        @endphp

        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $p->rental->car->brand->nama_merek ?? '-' }} {{ $p->rental->car->model ?? '-' }}</td>
          <td><small style="color:{{ $warna }}">{{ $jenis }}</small></td>
          <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
          <td>Rp{{ number_format($p->total_bayar,0,',','.') }}</td>
          <td>
            <span class="status {{ strtolower($p->status_pembayaran) }}">
              @if($p->status_pembayaran === 'pending')
                🕓 Menunggu Pembayaran (<span class="cd" data-s="{{ max(0,$exp) }}">--:--</span>)
              @elseif($p->status_pembayaran === 'success')
                ✅ Lunas
              @else
                ❌ Dibatalkan
              @endif
            </span>
          </td>
          <td>
            {{-- tombol aksi --}}
            @if($p->status_pembayaran === 'pending')
              <a class="btn" href="{{ route('user.payments.continue', $p->payment_id) }}">
                Lanjutkan Pembayaran
              </a>
              @if($p->payment_type !== 'charge')
                <form action="{{ route('user.payments.cancelSoft', $p->payment_id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan pembayaran ini?')">
                  @csrf
                  <button type="submit" class="btn btn-danger">Batalkan</button>
                </form>
              @endif

            @elseif($p->status_pembayaran === 'success')
              <button class="btn" onclick="openReceipt({{ $p->payment_id }})">
                Lihat Kuitansi
              </button>

            @else
              <button class="btn" onclick="openReceipt({{ $p->payment_id }})">
                Lihat Informasi
              </button>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  @endif

  <small style="display:block;margin-top:10px;color:#777;">
    ℹ️ Kuitansi tambahan tidak dapat dibatalkan.
  </small>

  <a href="{{ route('user.dashboard') }}" class="btn-back">← Kembali ke Dashboard</a>
</div>

<!-- 🧾 POPUP MODAL -->
<div id="receiptModal" class="modal-overlay">
  <div class="modal-box">
    <button class="close-btn" onclick="closeModal()">×</button>
    <div id="receiptContent"><p>Memuat data...</p></div>
  </div>
</div>

<script>
/* ---------------- Countdown ---------------- */
const toMMSS = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
document.querySelectorAll('.cd').forEach(el=>{
  let s = +el.dataset.s || 0;
  if (s <= 0) { el.textContent = '00:00'; return; }
  const tick = () => { el.textContent = toMMSS(s); if (s-- > 0) setTimeout(tick, 1000); };
  tick();
});

/* ---------------- Auto-cek expired ---------------- */
setInterval(() => {
  fetch("{{ route('user.payments.checkExpired') }}")
    .then(r => r.json())
    .then(d => { if ((d.count||0) > 0) location.reload(); })
    .catch(()=>{});
}, 30000);

/* ---------------- Modal Functions ---------------- */
function openReceipt(id){
  const modal = document.getElementById('receiptModal');
  const box   = document.getElementById('receiptContent');
  modal.style.display = 'flex';
  box.innerHTML = '<p>⏳ Memuat data...</p>';

  fetch(`{{ url('/user/payments') }}/${id}/json`)
    .then(res => {
      if (!res.ok) throw new Error('HTTP '+res.status);
      return res.json();
    })
    .then(p => {
  if (!p || !p.payment_id) {
    box.innerHTML = '<p>Data tidak ditemukan.</p>';
    return;
  }

  const statusClass = p.status_pembayaran.toLowerCase();
  const statusText =
    p.status_pembayaran === 'success' ? '✅ Pembayaran Berhasil' :
    p.status_pembayaran === 'failed'  ? '❌ Pembayaran Gagal'    :
                                         '⏳ Pembayaran Pending';

  // 🧩 bagian info tambahan khusus charge
  let extraRows = '';
  if (p.payment_type === 'charge') {
    const statusPengembalian = p.rental?.invoice?.status_pengembalian
      ? p.rental.invoice.status_pengembalian.replace(/_/g,' ')
      : '-';
    const catatan = p.rental?.invoice?.catatan || '-';

    extraRows = `
      <tr><th>Status Pengembalian</th><td>${statusPengembalian}</td></tr>
      <tr><th>Deskripsi</th><td>${catatan}</td></tr>
    `;
  }

  box.innerHTML = `
    <h3 class="receipt-title">
      ${p.payment_type==='charge' ? 'Kuitansi Tambahan' : 'Kuitansi Pembayaran'}
    </h3>
    <div class="receipt-status ${statusClass}">${statusText}</div>
    <div class="receipt-subtitle">#${p.payment_id} • ${p.gateway||'Duitku'}</div>

    <table class="receipt-table">
      <tr><th>Mobil</th><td>${p.rental?.car?.brand?.nama_merek ?? '-'} ${p.rental?.car?.model ?? ''}</td></tr>
      <tr><th>🕓 Tanggal Sewa</th><td>${p.rental?.tanggal_mulai_fmt ?? '-'} → ${p.rental?.tanggal_selesai_fmt ?? '-'}</td></tr>
      ${extraRows}
      <tr><th>Metode</th><td>${p.metode?.toUpperCase() ?? '-'}</td></tr>
      <tr><th>Total</th><td><strong>Rp${Number(p.total_bayar).toLocaleString('id-ID')}</strong></td></tr>
      <tr><th>Tanggal Bayar</th><td>${p.tanggal_bayar_fmt ?? '-'}</td></tr>
      <tr><th>Status</th><td>${p.status_pembayaran}</td></tr>
    </table>

    ${p.status_pembayaran==='pending'
      ? `<a href="${p.payment_token}" target="_blank" class="btn-link">Lanjutkan Pembayaran</a>`
      : `<a href="#" class="btn-link" onclick="closeModal()" style="background:#28a745;">Tutup</a>`}
  `;
})
}

/* ---------------- Close Modal ---------------- */
function closeModal(){
  document.getElementById('receiptModal').style.display='none';
}
</script>
</body>
</html>
