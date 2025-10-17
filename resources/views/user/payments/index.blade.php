@extends('partials.container')

@section('title', 'Daftar Pembayaran')

@section('styles')
<style>
  /* ✅ Container utama (tidak ada border radius) */
  .container {
    max-width: 900px;
    margin: auto;
    margin-left: -10px;
    margin-right: -10px;
  }

  /* ✅ Card pembungkus daftar pembayaran */
  .card-daftar {
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    margin-left: -10px;
    margin-right: -10px;
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #222;
    font-size: 25px;
    font-weight: 600;
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
    padding: 18px 20px;
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

  .card-left {
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .icon-box {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .icon-box img {
    width: 26px;
    height: 26px;
  }

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
    transition: .2s;
  }
  .btn:hover { background: #0b5ed7; }

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

  .receipt-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 6px;
    color: #222;
    text-align: center;
  }
  .receipt-status {
    font-weight: 600;
    font-size: 15px;
    margin: 8px 0;
    text-align: center;
  }
  .receipt-status.success { color: #28a745; }
  .receipt-status.failed  { color: #dc3545; }
  .receipt-status.pending { color: #ffc107; }

  .receipt-subtitle {
    color: #666;
    font-size: 14px;
    margin-bottom: 12px;
    text-align: center;
  }

  .receipt-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    font-size: 14px;
  }
  .receipt-table th, .receipt-table td {
    padding: 8px 6px;
    text-align: left;
    vertical-align: top;
  }
  .receipt-table th {
    width: 45%;
    color: #333;
  }
  .receipt-table td {
    color: #555;
  }
  .receipt-table tr:nth-child(odd) {
    background: #f9f9f9;
  }

  .back-link {
    color:#000; text-decoration:none;
    font-weight:250; font-size:15px;
    margin-left: -150px;
    margin-bottom: 10px;
  }
  .back-link:hover { text-decoration:underline; }

  /* 🔹 Responsive */
  @media (max-width:700px) {
    .payment-card { flex-direction: column; align-items: flex-start; gap: 10px; }
    .card-right { text-align: left; width: 100%; }
  }
</style>
@endsection

@section('content')
  <a href="{{ route('user.dashboard') }}" class="back-link">← Kembali ke Dashboard</a>
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

          <div class="payment-card">
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
              @else
                <button class="btn" onclick="openReceipt({{ $p->payment_id }})">Lihat {{ $p->status_pembayaran === 'success' ? 'Kuitansi' : 'Info' }}</button>
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
  /* Countdown */
  const toMMSS = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
  document.querySelectorAll('.cd').forEach(el=>{
    let s = +el.dataset.s || 0;
    if (s <= 0) { el.textContent = '00:00'; return; }
    const tick = () => { el.textContent = toMMSS(s); if (s-- > 0) setTimeout(tick, 1000); };
    tick();
  });

  /* Modal */
  function openReceipt(id){
    const modal = document.getElementById('receiptModal');
    const box = document.getElementById('receiptContent');
    modal.style.display = 'flex';
    box.innerHTML = '<p>⏳ Memuat data...</p>';

    fetch(`{{ url('/user/payments') }}/${id}/json`)
    .then(r=>r.json())
    .then(p=>{
      if(!p || !p.payment_id){ box.innerHTML='<p>Data tidak ditemukan.</p>'; return; }
      const cls = p.status_pembayaran.toLowerCase();
      const text = p.status_pembayaran==='success'?'✅ Pembayaran Berhasil':
                   p.status_pembayaran==='failed'?'❌ Pembayaran Gagal':'⏳ Pembayaran Pending';
      box.innerHTML = `
        <h3 class="receipt-title">${p.payment_type==='charge'?'Kuitansi Tambahan':'Kuitansi Pembayaran'}</h3>
        <div class="receipt-status ${cls}">${text}</div>
        <div class="receipt-subtitle">#${p.payment_id} • ${p.gateway||'Duitku'}</div>
        <table class="receipt-table">
          <tr><th>Mobil</th><td>${p.rental?.car?.brand?.nama_merek ?? '-'} ${p.rental?.car?.model ?? ''}</td></tr>
          <tr><th>🕓 Tanggal Sewa</th><td>${p.rental?.tanggal_mulai_fmt ?? '-'} → ${p.rental?.tanggal_selesai_fmt ?? '-'}</td></tr>
          <tr><th>Metode</th><td>${p.metode?.toUpperCase() ?? '-'}</td></tr>
          <tr><th>Total</th><td><strong>Rp${Number(p.total_bayar).toLocaleString('id-ID')}</strong></td></tr>
          <tr><th>Tanggal Bayar</th><td>${p.tanggal_bayar_fmt ?? '-'}</td></tr>
          <tr><th>Status</th><td>${p.status_pembayaran}</td></tr>
        </table>`;
    });
  }

  function closeModal(){ document.getElementById('receiptModal').style.display='none'; }

  // Tutup modal jika klik di luar box
  window.onclick = e => {
    const modal = document.getElementById('receiptModal');
    if(e.target === modal) modal.style.display = 'none';
  };
  </script>
@endsection
