@extends('layouts.admin.app')

@section('title', 'Detail Penyewaan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-rentals-detail.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Detail Transaksi Penyewaan</h2>
    <p>Lihat dan kelola detail penyewaan berikut secara lengkap.</p>
  </div>

  {{-- 🔹 DATA PENYEWAAN --}}
  <div class="detail-wrapper">
    <h3>🧾 Data Penyewaan</h3>
    <div class="detail-grid">
      <label>Penyewa:</label>
      <div>{{ $rental->user->nama_lengkap ?? '-' }} (ID: {{ $rental->user_id }})</div>

      <label>Email:</label>
      <div>{{ $rental->user->email ?? '-' }}</div>

      <label>Mobil:</label>
      <div>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model ?? '' }}</div>

      <label>Tanggal Sewa:</label>
      <div>{{ $rental->tanggal_mulai }} → {{ $rental->tanggal_selesai }} ({{ $rental->durasi_hari }} hari)</div>

      <label>Driver:</label>
      <div>{{ ucfirst($rental->driver) }}</div>

      <label>Metode Pengambilan:</label>
      <div>{{ str_replace('_', ' ', ucfirst($rental->metode_pickup)) }}</div>

      <label>Total Biaya:</label>
      <div><strong>Rp{{ number_format($rental->total_biaya, 0, ',', '.') }}</strong></div>

      <label>Status Sewa:</label>
<div>
  <span class="status {{ strtolower($rental->status_rental) }}">
    {{ ucfirst($rental->status_rental) }}
  </span>

  {{-- ⏳ Countdown kalau draft --}}
  @if($rental->status_rental === 'draft' && $rental->expired_at)
    <div id="countdown" style="margin-top:5px; color:#f39c12; font-weight:600;"></div>
    <script>
      const expireTime = new Date("{{ \Carbon\Carbon::parse($rental->expired_at)->format('Y-m-d H:i:s') }}").getTime();

      const countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const distance = expireTime - now;

        if (distance < 0) {
          clearInterval(countdownInterval);
          document.getElementById("countdown").innerHTML = "❌ Draft telah kedaluwarsa";
          return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = 
          `⏳ Draft akan kedaluwarsa dalam <b>${minutes} menit ${seconds} detik</b>`;
      }, 1000);
    </script>
  @endif
</div>

    @if(in_array($rental->status_rental, ['selesai']))
      <hr>
      <a href="{{ route('admin.invoices.create', $rental->rental_id) }}" class="btn btn-green">
        + Buat Charge
      </a>
    @endif
  </div>

  {{-- 💳 DATA PEMBAYARAN --}}
  <div class="detail-wrapper card-section">
    <h3>💳 Data Pembayaran</h3>
    @if($rental->payment)
      <div class="detail-grid">
        <label>Payment ID:</label>
        <div>#{{ $rental->payment->payment_id }}</div>

        <label>Gateway:</label>
        <div>{{ $rental->payment->gateway }}</div>

        <label>Metode:</label>
        <div>{{ strtoupper($rental->payment->metode) }}</div>

        <label>Total Bayar:</label>
        <div><strong>Rp{{ number_format($rental->payment->total_bayar, 0, ',', '.') }}</strong></div>

        <label>Status Pembayaran:</label>
        <div>
          <span class="status {{ strtolower($rental->payment->status_pembayaran) }}">
            {{ ucfirst($rental->payment->status_pembayaran) }}
          </span>
          @if($rental->payment->status_pembayaran === 'pending')
            <form method="POST" action="{{ route('admin.payments.refresh', $rental->payment->payment_id) }}" style="display:inline;">
              @csrf
              <button class="btn btn-blue" style="margin-left:10px;">Refresh Status</button>
            </form>
          @endif
        </div>

        <label>Gateway Reference:</label>
        <div>{{ $rental->payment->gateway_reference }}</div>

        <label>Tanggal Bayar:</label>
        <div>{{ $rental->payment->tanggal_bayar ?? '-' }}</div>

        <label>Link Transaksi:</label>
        <div>
          <a href="{{ $rental->payment->payment_token }}" target="_blank" class="link-blue">
            Lihat di Duitku ↗
          </a>
        </div>
      </div>
    @else
      <p class="no-payment">❌ Belum ada data pembayaran untuk transaksi ini.</p>
    @endif
  </div>

 {{-- 🔄 FORM UPDATE STATUS --}}
<div class="detail-wrapper">
  <h3>🔧 Update Status Penyewaan</h3>
  <form method="POST" action="{{ route('admin.rentals.updateStatus', $rental->rental_id) }}">
    @csrf
    <label for="status_rental">Ubah Status:</label><br>

    {{-- custom dropdown --}}
    <div class="status-dropdown">
      <button type="button" class="dropdown-toggle" id="statusDropdown">
        <span id="selected-status">-- Pilih Status --</span>
        <i class="bi bi-chevron-down"></i>
      </button>
      <ul class="dropdown-menu" id="statusOptions">
        <li data-value="berjalan"><span class="icon">🚗</span> <span class="label">Sedang Berjalan</span></li>
        <li data-value="selesai"><span class="icon">✅</span> <span class="label">Selesai</span></li>
        <li data-value="dibatalkan"><span class="icon">❌</span> <span class="label">Dibatalkan</span></li>
      </ul>
      <input type="hidden" name="status_rental" id="statusInput" required>
    </div>
</form>
    <div class="form-actions">
  <button type="submit" class="btn-submit">Update Status</button>
  <a href="{{ route('admin.rentals.index') }}" class="btn-cancel">Kembali</a>
</div>
  </div>

  <script>
document.addEventListener("DOMContentLoaded", function() {
  const dropdownToggle = document.getElementById("statusDropdown");
  const dropdownMenu = document.getElementById("statusOptions");
  const selectedStatus = document.getElementById("selected-status");
  const hiddenInput = document.getElementById("statusInput");

  // toggle menu tampil / sembunyi
  dropdownToggle.addEventListener("click", () => {
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
  });

  // pilih opsi
  dropdownMenu.querySelectorAll("li").forEach(item => {
    item.addEventListener("click", () => {
      const value = item.getAttribute("data-value");
      selectedStatus.innerHTML = item.innerHTML;
      hiddenInput.value = value;
      dropdownMenu.style.display = "none";
    });
  });

  // klik di luar = tutup dropdown
  document.addEventListener("click", (e) => {
    if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.style.display = "none";
    }
  });
});
</script>
</div>
@endsection
