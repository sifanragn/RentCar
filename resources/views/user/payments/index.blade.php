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

/* ===== Filter Bar Style ===== */
.filter-wrapper {
  width: 100%;
  overflow-x: auto;
  padding: 10px 12px;
  margin-top: -10px;
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.filter-wrapper::-webkit-scrollbar { display: none; }

.filter-bar-horizontal {
  display: flex;
  align-items: center;
  gap: 8px;
  width: max-content;
  padding: 6px 10px;
  margin: 0 auto;
}
.filter-select,
.filter-input {
  width: 110px;
  border: 1px solid #000;
  border-radius: 8px;
  padding: 4px 6px;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  background: #fff;
  color: #000;
  transition: 0.2s;
  flex-shrink: 0;
}
.filter-input[type="date"] { cursor: pointer; }
.filter-input:focus,
.filter-select:focus {
  border-color: #000;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.25);
  outline: none;
}
.btn-search {
  background-color: #000;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 5px 12px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
  flex-shrink: 0;
}
.btn-search:hover { background-color: #333; }

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
  background: #f2f2f2;
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

/* 🔧 Samakan ukuran semua tombol */
.btn,
.btn-kuitansi,
.btn-info,
.btn-danger {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  height: 28px; /* ✅ tetap seperti sebelumnya */
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  line-height: 1;
  cursor: pointer;
  text-decoration: none;
  transition: 0.25s ease;
  border: none;
  background: #000;
  color: #fff;
  box-sizing: border-box;
}

/* Warna tombol utama */
.btn, .btn-kuitansi, .btn-info {
  background: #000;
  color: #fff;
}
.btn:hover, .btn-kuitansi:hover, .btn-info:hover {
  background: #333;
}

/* Warna tombol batalkan */
.btn-danger {
  background: #dc3545;
  color: #fff;
}
.btn-danger:hover {
  background: #b02a37;
}

/* Kalau ingin jarak antar tombol seragam */
.card-right .btn + .btn {
  margin-left: 8px;
}

/* 🚫 Hilangkan outline / efek biru saat klik atau fokus */
.btn:focus,
.btn:active,
.btn-info:focus,
.btn-info:active,
.btn-kuitansi:focus,
.btn-kuitansi:active,
.btn-danger:focus,
.btn-danger:active {
  outline: none !important;
  box-shadow: none !important;
}

/* 🧹 Hilangkan gaya default browser pada <button> */
button.btn {
  appearance: none;
  -webkit-appearance: none;
  border: none;
}

/* 💥 Efek klik lembut biar terasa tanpa warna biru */
.btn:active {
  transform: scale(0.97);
}

/* 🚫 Hilangkan highlight biru bawaan Chrome, Edge, Safari */
.btn,
button.btn,
a.btn {
  -webkit-tap-highlight-color: transparent !important;
  -webkit-focus-ring-color: transparent !important;
  user-select: none;
  outline: none !important;
  box-shadow: none !important;
}

/* 🚫 Hilangkan border fokus di Firefox */
button.btn::-moz-focus-inner {
  border: 0;
}

/* Pastikan outline tidak muncul di mode focus-visible */
button.btn:focus-visible {
  outline: none !important;
}

/* 🔧 Pastikan warna tidak berubah saat ditekan */
.btn:focus,
.btn:active {
  background-color: #000 !important;
  color: #fff !important;
}

.back-link {
  color:#000; text-decoration:none;
  font-weight:250; font-size:15px;
  margin-left: -275px;
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
  backdrop-filter: blur(4px);
}
.modal-box {
  background: #ffffff;
  border-radius: 18px;
  padding: 28px 22px;
  width: 90%;
  max-width: 470px;
  box-shadow: 0 10px 28px rgba(0,0,0,0.25);
  position: relative;
  animation: fadeIn .3s ease;
  text-align: left;
  font-family: 'Poppins', sans-serif;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95) translateY(15px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #666;
  transition: .2s;
}
.close-btn:hover { color: #000; transform: scale(1.15); }

.receipt-title {
  font-size: 20px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 8px;
  text-align: center;
}

.receipt-status {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 6px;
  font-weight: 600;
  font-size: 14px;
  margin: 10px auto 14px;
  padding: 8px 12px;
  border-radius: 8px;
  width: fit-content;
}
.receipt-status.success { background: #dcfce7; color: #166534; }
.receipt-status.failed  { background: #fee2e2; color: #991b1b; }
.receipt-status.pending { background: #fef9c3; color: #854d0e; }

.receipt-subtitle {
  color: #64748b;
  font-size: 13px;
  margin-bottom: 14px;
  text-align: center;
}

.receipt-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 8px;
  font-size: 13px;
  background: #f9fafb;
  border-radius: 8px;
  overflow: hidden;
}
.receipt-table th, .receipt-table td {
  padding: 9px 10px;
  text-align: left;
  vertical-align: top;
  border-bottom: 1px solid #e5e7eb;
}
.receipt-table th {
  width: 45%;
  color: #1e293b;
  font-weight: 600;
}
.receipt-table td { color: #374151; }
.receipt-table tr:last-child td { border-bottom: none; }

.btn {
  background: #000;
  color: #fff;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 13px;
  display: inline-block;
  text-align: center;
  transition: 0.2s;
  cursor: pointer;
  text-decoration: none;
}
.btn:hover {
  background: #333;
  transform: translateY(-1px);
}

@media (max-width:700px) {
  .payment-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  .card-right { text-align: left; width: 100%; }
  .modal-box { padding: 22px 18px; font-size: 13px; }
  .receipt-table th, .receipt-table td { padding: 7px 8px; }
}
</style>
@endsection

@section('content')
<a 
  href="{{ request()->query('from') === 'profile' 
      ? route('user.profile.index') 
      : route('user.dashboard') }}" 
  class="back-link"
>
  <i class="fa-solid fa-arrow-left"></i>
</a>

<!-- 🔍 FILTER -->
<div class="filter-wrapper">
  <form method="GET" class="filter-bar-horizontal">
    <input type="text" name="no_transaksi" 
           value="{{ request('no_transaksi') }}" 
           placeholder="No. Transaksi" class="filter-input">

    <input type="date" name="tanggal" 
           value="{{ request('tanggal') }}" 
           class="filter-input">

    <select name="status" class="filter-select">
      <option value="">Semua</option>
      <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
      <option value="success" {{ request('status')=='success'?'selected':'' }}>Lunas</option>
      <option value="failed" {{ request('status')=='failed'?'selected':'' }}>Dibatalkan</option>
    </select>

    <button type="submit" class="btn-search">Cari</button>
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
          // tambahan dari branch lain: logika jenis kuitansi dan warna
          $jenis = match(true) {
              $p->payment_type === 'main' => 'Kuitansi Utama',
              $p->payment_type === 'charge' => 'Kuitansi Tambahan',
              $p->payment_type === 'invoice' && $p->rental->invoice?->denda_tambahan > 0 
                  && $p->total_bayar == $p->rental->invoice->denda_tambahan => 'Kuitansi Tambahan',
              $p->payment_type === 'final' => 'Kuitansi Akhir',
              default => ucfirst($p->payment_type),
          };
          $warna = match(true) {
              $p->payment_type === 'main' => '#0d6efd',
              $p->payment_type === 'charge' 
                  || ($p->payment_type === 'invoice' && $p->rental->invoice?->denda_tambahan > 0 && $p->total_bayar == $p->rental->invoice->denda_tambahan)
                  => '#e67e22',
              $p->payment_type === 'final' => '#28a745',
              default => '#555',
          };
        @endphp

        <div class="payment-card" data-payment-id="{{ $p->payment_id }}">
          <div class="card-left">
            <div class="icon-box">
              <img src="{{ asset('images/wallet.png') }}" alt="icon">
            </div>
            <div class="car-info">
              <div class="car-name">{{ $p->rental->car->brand->nama_merek ?? '-' }} {{ $p->rental->car->model ?? '-' }}</div>
              <div class="car-meta">
                {{ $jenis }} • {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}
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
              <a class="btn" href="{{ route('user.payments.continue', $p->payment_id) }}">Lanjutkan</a>

              @php
                $isKuitansiTambahan = $p->payment_type === 'charge' ||
                  ($p->payment_type === 'invoice' &&
                  $p->rental->invoice?->denda_tambahan > 0 &&
                  $p->total_bayar == $p->rental->invoice->denda_tambahan);
              @endphp

              @if(!$isKuitansiTambahan)
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
            : ``)}
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
