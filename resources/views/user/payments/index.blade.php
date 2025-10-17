<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Pembayaran</title>
  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: #f8f9fa;
      padding: 30px;
    }
    .container {
      max-width: 980px;
      margin: auto;
    }
    h2 {
      text-align: center;
      margin-bottom: 24px;
      color: #333;
    }

    /* 🔎 FILTER AREA */
    .filter-box {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      background: #fff;
      padding: 15px 18px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,.08);
      margin-bottom: 20px;
      align-items: center;
    }
    .filter-box label {
      font-weight: 600;
      margin-right: 6px;
      color: #444;
    }
    .filter-box input,
    .filter-box select {
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      outline: none;
    }
    .filter-box input:focus,
    .filter-box select:focus {
      border-color: #0d6efd;
    }
    .filter-box button {
      background: #0d6efd;
      border: none;
      color: #fff;
      padding: 7px 14px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: .2s;
    }
    .filter-box button:hover { background: #0b5ed7; }

    /* 🔹 Table */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,.08);
    }
    th, td {
      padding: 12px 14px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    thead {
      background: #0d6efd;
      color: #fff;
    }

    /* 🔸 Status & Buttons */
    .status {
      padding: 6px 10px;
      border-radius: 6px;
      color: #fff;
      font-weight: 700;
    }
    .pending { background: #ffc107; color: #222; }
    .success { background: #28a745; }
    .failed { background: #dc3545; }
    .btn {
      background: #0d6efd;
      color: #fff;
      padding: 8px 12px;
      border-radius: 8px;
      text-decoration: none;
      border: none;
      cursor: pointer;
      margin-right: 4px;
    }
    .btn:hover { background: #0b5ed7; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #b02a37; }
    .btn-back {
      display: inline-block;
      margin-top: 25px;
      background: #6c757d;
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: .2s;
    }
    .btn-back:hover { background: #5a6268; }
    td form { display: inline; }

    /* Modal */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.55);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }
    .modal-box {
      background: #fff;
      border-radius: 20px;
      padding: 30px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 6px 18px rgba(0,0,0,.2);
      position: relative;
      animation: fadeIn .3s ease;
      text-align: center;
    }
    @keyframes fadeIn {
      from {opacity:0; transform:translateY(20px);}
      to {opacity:1; transform:translateY(0);}
    }
    .close-btn {
      position: absolute;
      top: 12px; right: 15px;
      background: none;
      border: none;
      font-size: 22px;
      cursor: pointer;
      color: #555;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Daftar Pembayaran</h2>

  <!-- 🔍 FILTER -->
  <form method="GET" class="filter-box">
    <div>
      <label>No. Transaksi:</label>
      <input type="text" name="no_transaksi" value="{{ request('no_transaksi') }}" placeholder="Contoh: INV0007">
    </div>
    <div>
      <label>Tanggal:</label>
      <input type="date" name="tanggal" value="{{ request('tanggal') }}">
    </div>
    <div>
      <label>Status:</label>
      <select name="status">
        <option value="">Semua</option>
        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
        <option value="success" {{ request('status')=='success'?'selected':'' }}>Lunas</option>
        <option value="failed" {{ request('status')=='failed'?'selected':'' }}>Dibatalkan</option>
      </select>
    </div>
    <div>
      <button type="submit">🔎 Cari</button>
    </div>
  </form>

  @if($payments->isEmpty())
    <p>Belum ada pembayaran.</p>
  @else
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>No. Transaksi</th>
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
          $jenis = match($p->payment_type) {
              'main' => 'Kuitansi Utama',
              'charge' => 'Kuitansi Tambahan',
              'final' => 'Kuitansi Akhir',
              default => ucfirst($p->payment_type),
          };
          $warna = match($p->payment_type) {
              'main' => '#0d6efd',
              'charge' => '#e67e22',
              'final' => '#28a745',
              default => '#555',
          };
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td>
  <strong>{{ $p->no_transaksi ?? '-' }}</strong>
</td>

          </td>
          <td>{{ $p->rental->car->brand->nama_merek ?? '-' }} {{ $p->rental->car->model ?? '-' }}</td>
          <td><small style="color:{{ $warna }}">{{ $jenis }}</small></td>
          <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
          <td>Rp{{ number_format($p->total_bayar,0,',','.') }}</td>
          <td>
            <span class="status {{ strtolower($p->status_pembayaran) }}">
              @if($p->status_pembayaran === 'pending')
                🕓 Menunggu (<span class="cd" data-s="{{ max(0,floor($exp)) }}">--:--</span>)
              @elseif($p->status_pembayaran === 'success')
                ✅ Lunas
              @else
                ❌ Dibatalkan
              @endif
            </span>
          </td>
          <td>
            @if($p->status_pembayaran === 'pending')
              <a class="btn" href="{{ route('user.payments.continue', $p->payment_id) }}">Lanjutkan</a>
              @if($p->payment_type !== 'charge')
                <form action="{{ route('user.payments.cancelSoft', $p->payment_id) }}" method="POST" onsubmit="return confirm('Batalkan pembayaran ini?')">
                  @csrf
                  <button type="submit" class="btn btn-danger">Batalkan</button>
                </form>
              @endif
            @else
              <button class="btn" onclick="openReceipt({{ $p->payment_id }})">
                {{ $p->status_pembayaran === 'success' ? 'Lihat Kuitansi' : 'Lihat Info' }}
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

<!-- 🧾 Modal -->
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
  const row = el.closest('tr'); // ambil baris table terkait
  const statusSpan = row?.querySelector('.status');
  const btnArea = row?.querySelector('td:last-child');

  if (s <= 0) {
    el.textContent = '00:00';
    expireRow(row); // langsung ubah tampilan + sync ke backend
    return;
  }

  const tick = () => {
    el.textContent = toMMSS(s);
    if (s > 0) {
      s--;
      setTimeout(tick, 1000);
    } else {
      el.textContent = '00:00';
      expireRow(row); // ketika countdown selesai
    }
  };
  tick();
});

/* ---------------- Auto-cek expired berkala ---------------- */
let _alreadyReloaded = false;

function autoCheckExpired(){
  if (_alreadyReloaded) return;
  fetch("{{ route('user.payments.checkExpired') }}")
    .then(r=>r.json())
    .then(d=>{
      if ((d.count||0)>0 && !_alreadyReloaded){
        _alreadyReloaded=true;
        location.reload();
      }
    }).catch(()=>{});
}
setInterval(autoCheckExpired,10000);

/* ---------------- Fungsi: ubah row jadi dibatalkan ---------------- */
function expireRow(row){
  if(!row || _alreadyReloaded) return;
  _alreadyReloaded = true;

  fetch("{{ route('user.payments.checkExpired') }}")
    .then(r=>r.json())
    .then(d=>{
      console.log('⏰ Expired check:',d);
      const statusSpan = row.querySelector('.status');
      if(statusSpan){
        statusSpan.classList.remove('pending');
        statusSpan.classList.add('failed');
        statusSpan.innerHTML = '❌ Dibatalkan (Auto)';
      }
      const btnArea = row.querySelector('td:last-child');
      if(btnArea){
        btnArea.innerHTML = `
          <button class="btn" onclick="openReceipt(${row.dataset.paymentId || 0})">
            Lihat Info
          </button>`;
      }
    })
    .catch(err => console.error('Error expireRow:', err));
}


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
          <tr><th>Metode Pengambilan</th><td>${
            p.rental?.metode_pickup === 'ambil_sendiri'
              ? 'Ambil Sendiri ke Kantor'
              : (p.rental?.metode_pickup === 'pickup_alamat'
                  ? 'Antar ke Alamat Penyewa'
                  : '-')
          }</td></tr>
          ${extraRows}
          <tr><th>Metode</th><td>${p.metode?.toUpperCase() ?? '-'}</td></tr>
          <tr><th>Total</th><td><strong>Rp${Number(p.total_bayar).toLocaleString('id-ID')}</strong></td></tr>
          <tr><th>Tanggal Bayar</th><td>${p.tanggal_bayar_fmt ?? '-'}</td></tr>
          <tr><th>Status</th><td>${p.status_pembayaran}</td></tr>
        </table>

        ${p.status_pembayaran === 'success'
        ? `<button onclick="downloadPDF(${p.payment_id})" class="btn-link"
              style="background:${p.payment_type === 'charge' ? '#e67e22' : '#198754'};">
              ⬇️ Download ${p.payment_type === 'charge' ? 'Kuitansi Tambahan' : 'Kuitansi (PDF)'}
           </button>`
        : (p.status_pembayaran === 'pending'
            ? `<a href="${p.payment_token}" target="_blank" class="btn-link">Lanjutkan Pembayaran</a>`
            : `<a href="#" class="btn-link" onclick="closeModal()" style="background:#28a745;">Tutup</a>`)}
      `;
    });
}

function downloadPDF(id) {
  fetch(`/user/payments/${id}/download`)
    .then(async res => {
      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.error || 'Gagal mengunduh PDF.');
      }
      return res.blob();
    })
    .then(blob => {
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `Kuitansi_${id}.pdf`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    })
    .catch(e => alert('❌ ' + e.message));
}

function closeModal(){
  document.getElementById('receiptModal').style.display='none';
}
</script>
</body>
</html>