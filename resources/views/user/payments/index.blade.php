@extends('partials.container')

@section('title', 'Daftar Pembayaran')

@section('styles')
<style>
  /* ===== Container utama ===== */
  .container {
    max-width: 900px;
    margin: auto;
    margin-left: -10px;
    margin-right: -10px;
  }

  /* ===== Card ===== */
  .card-daftar {
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    margin-left: -10px;
    margin-right: -10px;
    margin-bottom: 10px;
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #222;
    font-size: 25px;
    font-weight: 600;
  }

/* 🔎 Filter Box Horizontal Sejajar */
  /* ===== Card ===== */
  .card-filter {
    background: #fff;
    border-radius: 16px;
    padding: 1px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    margin-bottom: 15px;
  }

.filter-box {
  display: flex;
  flex-wrap: wrap;      /* biar kalau sempit bisa wrap */
  gap: 12px;            
  background: #f8f9fa;
  padding: 9px 13px;
  border-radius: 12px;
  margin-bottom: 20px;
  align-items: center;
}

.filter-box > div {
  display: flex;
  flex-direction: row;   /* label & input sejajar */
  align-items: center;
  gap: 6px;              /* jarak label & input */
  font-size: 14px;
}

.filter-box label {
  font-weight: 600;
  min-width: 100px;
}

.filter-box input,
.filter-box select {
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  outline: none;
  font-size: 14px;
  min-width: 140px;      /* supaya ukuran input konsisten */
}

.filter-box input:focus,
.filter-box select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 4px rgba(13, 110, 253, 0.3);
}

.filter-box button {
  background: #22c55e;
  border: none;
  color: #fff;
  padding: 6px 25px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}

.filter-box button:hover {
  background: #16a34a;
}

  /* 🌟 Payment List */
  .payment-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .payment-card {
    background: white;
    border-radius: 16px;
    padding: 18px 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.10);
    transition: 0.25s ease;
    margin-left: -10px;
    margin-right: -10px;
  }
  .payment-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.12);
  }

  .card-left { display: flex; align-items: center; gap: 14px; }
  .icon-box {
    width: 30px; height: 30px;
    border-radius: 100px;
    background: #eef2ff;
    display: flex; align-items: center; justify-content: center;
  }
  
  .icon-box img { width: 26px; height: 26px; }

  .car-info { display: flex; flex-direction: column; }
  .car-name { font-weight: 600; color: #222; font-size: 15px; }
  .car-meta { font-size: 13px; color: #777; }

  .card-right { text-align: right; }
  .price { font-weight: 700; color: #000; font-size: 15px; margin-bottom: 5px; }

  .status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 6px;
  }
  .pending { background: #fff3cd; color: #856404; }
  .success { background: #d4edda; color: #155724; }
  .failed  { background: #f8d7da; color: #721c24; }

.btn {
  background: #0d6efd;
  color: #fff;
  padding: 7px 12px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px;
  display: inline-block;
  text-align: center;
  transition: 0.2s;
  cursor: pointer;
}

/* tombol kecil untuk “Lihat Info” */
.btn-info {
  min-width: 80px;
  padding: 5px 10px;
  font-size: 12px;
}

/* tombol normal untuk “Lihat Kuitansi” */
.btn-kuitansi {
  min-width: 105px;
  padding: 5px 10px;
}

.btn:hover {
  background: #0b5ed7;
}

  .btn-danger { background: #dc3545; }
  .btn-danger:hover { background: #b02a37; }

  .back-link {
    color:#000; text-decoration:none;
    font-weight:250; font-size:15px;
    margin-left: -150px;
    margin-bottom: 15px;
  }
  .back-link:hover { text-decoration:underline; }

  /* 🔹 Modal */
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
    padding: 30px 25px;
    width: 90%;
    max-width: 480px;
    box-shadow: 0 6px 18px rgba(0,0,0,.2);
    position: relative;
    animation: fadeIn .3s ease;
    text-align: center;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .close-btn {
    position: absolute;
    top: 12px;
    right: 15px;
    background: none;
    border: none;
    font-size: 22px;
    cursor: pointer;
    color: #555;
    transition: .2s;
  }
  .close-btn:hover { color: #000; }

  .receipt-title { font-size: 20px; font-weight: 600; margin-bottom: 6px; color: #222; }
  .receipt-status { font-weight: 600; font-size: 15px; margin: 8px 0; }
  .receipt-status.success { color: #28a745; }
  .receipt-status.failed  { color: #dc3545; }
  .receipt-status.pending { color: #ffc107; }
  .receipt-subtitle { color: #666; font-size: 14px; margin-bottom: 12px; }
  .receipt-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 14px; }
  .receipt-table th, .receipt-table td { padding: 8px 6px; text-align: left; vertical-align: top; }
  .receipt-table th { width: 45%; color: #333; }
  .receipt-table td { color: #555; }
  .receipt-table tr:nth-child(odd) { background: #f9f9f9; }

  /* 🔹 Responsive */
  @media (max-width:700px) {
  .payment-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .card-right {
    text-align: left;
    width: 100%;
  }

  /* Atur ulang filter box */
  .filter-box {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    width: 100%;
  }

  .filter-box > div {
    width: 100%;
  }

  .filter-box input,
  .filter-box select,
  .filter-box button {
    width: 100%;
    box-sizing: border-box;
  }

  .filter-box button {
    margin-top: 5px;
  }
}

</style>
@endsection

@section('content')
<a href="{{ route('user.dashboard') }}" class="back-link">← Kembali ke Dashboard</a>

  <!-- 🔍 FILTER -->
  <div class="card-filter">
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
      <button type="submit">Cari</button>
    </div>
  </form>
</div>

<div class="card-daftar">
  <h2>Daftar Pembayaran</h2>

  @if(session('warning'))
    <div style="background:#fff3cd;padding:10px;border-radius:8px;margin-bottom:12px;">
      {{ session('warning') }}
    </div>
  @endif

  @if($payments->isEmpty())
    <p>Belum ada pembayaran.</p>
  @else
    <div class="payment-list">
      @foreach($payments as $p)
        @php
          $exp = now()->diffInSeconds(\Carbon\Carbon::parse($p->created_at)->addMinutes(30), false);
        @endphp

        <div class="payment-card" data-payment-id="{{ $p->payment_id }}">
          <div class="card-left">
            <div class="icon-box">
              <img src="{{ asset('images/wallet.png') }}" alt="icon">
            </div>
            <div class="car-info">
              <div class="car-name">{{ $p->rental->car->brand->nama_merek ?? '-' }} {{ $p->rental->car->model ?? '-' }}</div>
              <div class="car-meta">
                {{ $p->payment_type === 'main' ? 'Kuitansi Utama' : ($p->payment_type === 'charge' ? 'Kuitansi Tambahan' : 'Kuitansi Akhir') }}
                • {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}
              </div>
            </div>
          </div>

          <div class="card-right">
            <div class="price">Rp{{ number_format($p->total_bayar, 0, ',', '.') }}</div>
            <div class="status {{ strtolower($p->status_pembayaran) }}">
              @if($p->status_pembayaran === 'pending')
                Menunggu (<span class="cd" data-s="{{ max(0,$exp) }}">--:--</span>)
              @elseif($p->status_pembayaran === 'success')
                Lunas
              @else
                Dibatalkan
              @endif
            </div>

            @if($p->status_pembayaran === 'pending')
              <a class="btn" href="{{ route('user.payments.continue', $p->payment_id) }}">Bayar</a>
              @if($p->payment_type !== 'charge')
                <form action="{{ route('user.payments.cancelSoft', $p->payment_id) }}" method="POST" onsubmit="return confirm('Batalkan pembayaran ini?')">
                  @csrf
                  <button type="submit" class="btn btn-danger">Batalkan</button>
                </form>
              @endif
            @else
<button 
  class="btn {{ $p->status_pembayaran === 'success' ? 'btn-kuitansi' : 'btn-info' }}" 
  onclick="openReceipt({{ $p->payment_id }})">
  {{ $p->status_pembayaran === 'success' ? 'Lihat Kuitansi' : 'Lihat Info' }}
</button>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<!-- 🧾 Modal -->
<div id="receiptModal" class="modal-overlay">
  <div class="modal-box">
    <button class="close-btn" onclick="closeModal()">×</button>
    <div id="receiptContent"><p>Memuat data...</p></div>
  </div>
</div>

@include('partials.bottom-navbar')

<script>
/* ---------------- Countdown ---------------- */
const toMMSS = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
document.querySelectorAll('.cd').forEach(el=>{
  let s = +el.dataset.s || 0;
  const row = el.closest('.payment-card');
  if (s <= 0) { el.textContent = '00:00'; return; }
  const tick = () => { el.textContent = toMMSS(s); if(s-->0) setTimeout(tick,1000); };
  tick();
});

/* ---------------- Modal Receipt ---------------- */
function openReceipt(id){
  const modal = document.getElementById('receiptModal');
  const box = document.getElementById('receiptContent');
  modal.style.display='flex';
  box.innerHTML='<p>⏳ Memuat data...</p>';

  fetch(`{{ url('/user/payments') }}/${id}/json`)
  .then(r=>r.json())
  .then(p=>{
    if(!p || !p.payment_id){ box.innerHTML='<p>Data tidak ditemukan.</p>'; return; }
    const cls = p.status_pembayaran.toLowerCase();
    const text = p.status_pembayaran==='success'?'✅ Pembayaran Berhasil':
                 p.status_pembayaran==='failed'?'❌ Pembayaran Gagal':'⏳ Pembayaran Pending';

    let extraRows = '';
    if(p.payment_type==='charge'){
      const statusPengembalian = p.rental?.invoice?.status_pengembalian?.replace(/_/g,' ') ?? '-';
      const catatan = p.rental?.invoice?.catatan ?? '-';
      extraRows = `
        <tr><th>Status Pengembalian</th><td>${statusPengembalian}</td></tr>
        <tr><th>Deskripsi</th><td>${catatan}</td></tr>
      `;
    }

    box.innerHTML = `
      <h3 class="receipt-title">${p.payment_type==='charge'?'Kuitansi Tambahan':'Kuitansi Pembayaran'}</h3>
      <div class="receipt-status ${cls}">${text}</div>
      <div class="receipt-subtitle">#${p.payment_id} • ${p.gateway||'Duitku'}</div>
      <table class="receipt-table">
        <tr><th>Mobil</th><td>${p.rental?.car?.brand?.nama_merek ?? '-'} ${p.rental?.car?.model ?? ''}</td></tr>
        <tr><th>Tanggal Sewa</th><td>${p.rental?.tanggal_mulai_fmt ?? '-'} → ${p.rental?.tanggal_selesai_fmt ?? '-'}</td></tr>
        <tr><th>Metode Pengambilan</th><td>${
          p.rental?.metode_pickup==='ambil_sendiri'?'Ambil Sendiri ke Kantor':(p.rental?.metode_pickup==='pickup_alamat'?'Antar ke Alamat Penyewa':'-')
        }</td></tr>
        ${extraRows}
        <tr><th>Metode</th><td>${p.metode?.toUpperCase() ?? '-'}</td></tr>
        <tr><th>Total</th><td><strong>Rp${Number(p.total_bayar).toLocaleString('id-ID')}</strong></td></tr>
        <tr><th>Tanggal Bayar</th><td>${p.tanggal_bayar_fmt ?? '-'}</td></tr>
        <tr><th>Status</th><td>${p.status_pembayaran}</td></tr>
      </table>
      ${p.status_pembayaran==='success'
        ? `<button onclick="downloadPDF(${p.payment_id})" class="btn" style="margin-top:10px;">⬇️ Download PDF</button>`
        : (p.status_pembayaran==='pending'
            ? `<a href="${p.payment_token}" target="_blank" class="btn" style="margin-top:10px;">Lanjutkan Pembayaran</a>`
            : ``)} <!-- 🔹 tombol “Tutup” dihapus -->
    `;
  });
}


function closeModal(){ document.getElementById('receiptModal').style.display='none'; }

function downloadPDF(id){
fetch(`/user/payments/${id}/download`)
    .then(async res=>{
      if(!res.ok){ const err=await res.json().catch(()=>({})); throw new Error(err.error||'Gagal mengunduh PDF.'); }
      return res.blob();
    })
    .then(blob=>{
      const url=URL.createObjectURL(blob);
      const a=document.createElement('a');
      a.href=url;
      a.download=`Kuitansi_${id}.pdf`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    })
    .catch(e=>alert('❌ '+e.message));
}

// Tutup modal jika klik di luar box
window.onclick = e=>{ if(e.target===document.getElementById('receiptModal')) closeModal(); };
</script>
@endsection
