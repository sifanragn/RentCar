@extends('layouts.admin.app')

@section('title', 'Daftar Penyewaan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-rentals.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Daftar Semua Penyewaan</h2>
    <p>Kelola dan pantau semua transaksi penyewaan mobil di sistem.</p>
  </div>

  {{-- 🔎 Filter Form --}}
  <form method="GET" action="{{ route('admin.rentals.index') }}" class="filter-form">
    <div class="filter-grid">
      <div class="filter-item">
        <label>Nama Penyewa</label>
        <input type="text" name="nama_penyewa" value="{{ request('nama_penyewa') }}" placeholder="Cari nama...">
      </div>

      <div class="filter-item">
        <label>Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
      </div>

      <div class="filter-item">
        <label>Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
      </div>

      <div class="filter-item custom-select">
  <label>Status</label>
  <div class="select-selected">Semua</div>
  <ul class="select-options">
    <li data-value="">Semua</li>
    <li data-value="menunggu_pembayaran" class="status-menunggu">Menunggu Pembayaran</li>
    <li data-value="berjalan" class="status-berjalan">Berjalan</li>
    <li data-value="selesai" class="status-selesai">Selesai</li>
    <li data-value="dibatalkan" class="status-dibatalkan">Dibatalkan</li>
  </ul>
  <input type="hidden" name="status_rental" value="">
</div>


      <div class="filter-actions">
        <button type="submit" class="btn-filter">Terapkan</button>
        <a href="{{ route('admin.rentals.index') }}" class="btn-reset">Reset</a>
      </div>
    </div>
  </form>

  {{-- 🔹 Tabel Daftar Penyewaan --}}
  <div class="table-wrapper">
    @if($rentals->isEmpty())
      <p class="empty-text">Belum ada transaksi penyewaan.</p>
    @else
      <table class="rental-table">
        <thead>
          <tr>
            <th>Penyewa</th>
            <th>Mobil</th>
            <th>Tanggal Sewa</th>
            <th>Tanggal Selesai</th>
            <th>Total Biaya</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rentals as $rental)
          <tr>
            <td>{{ $rental->user->nama_lengkap ?? '-' }}</td>
            <td>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</td>
            <td>{{ $rental->tanggal_mulai }}</td>
            <td>{{ $rental->tanggal_selesai }}</td>
            <td>Rp{{ number_format($rental->total_biaya, 0, ',', '.') }}</td>
            <td>
              <span class="status {{ $rental->status_rental }}">
                {{ ucfirst(str_replace('_', ' ', $rental->status_rental)) }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.rentals.show', $rental->rental_id) }}" class="btn-view">
                <i class="bi bi-eye"></i> Detail
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
  <!-- CDN Flatpickr -->
<!-- ✅ Flatpickr CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- ✅ Inisialisasi & Styling -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  flatpickr("input[type='date']", {
    dateFormat: "Y-m-d",           // format value untuk backend
    altInput: true,                // tampilkan versi yang lebih enak dibaca
    altFormat: "d / m / Y",        // format tampilan di input
    allowInput: true,
    locale: "id",
    disableMobile: true,           // pastikan pakai flatpickr juga di mobile
    static: false,
    position: "below",
    onReady: function(selectedDates, dateStr, instance) {
      // tambahkan placeholder manual biar tetap "dd / mm / yyyy"
      const input = instance.altInput;
      if (!input.placeholder) input.placeholder = "dd / mm / yyyy";
    }
  });
});
</script>
</div>
@endsection
