@extends('layouts.admin.app')

@section('title', 'Buat Invoice Baru')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-invoice-create.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Buat Invoice Baru</h2>
    <p>Lengkapi data untuk membuat invoice penyewaan mobil.</p>
  </div>

  <div class="form-card">
    <form action="{{ route('admin.invoices.store', $rental->rental_id ?? 0) }}" method="POST">
      @csrf

      {{-- 🔹 Informasi Rental --}}
      @if($rental)
      <div class="form-group">
        <label>Mobil</label>
        <input type="text" value="{{ $rental->car->brand->nama_merek }} {{ $rental->car->model ?? '' }}" readonly>
      </div>

      <div class="form-group">
        <label>Penyewa</label>
        <input type="text" value="{{ $rental->user->nama_lengkap }}" readonly>
      </div>

      <div class="form-group">
        <label>Total Biaya Sewa</label>
        <input type="text" value="Rp {{ number_format($rental->total_biaya,0,',','.') }}" readonly>
      </div>
      @endif

      {{-- 🔹 Status Pengembalian --}}
      <div class="form-group">
        <label>Status Pengembalian <span class="required">*</span></label>
        <select name="status_pengembalian" required>
          <option value="">-- Pilih Status --</option>
          <option value="telat">Telat</option>
          <option value="rusak">Rusak</option>
        </select>
      </div>

      {{-- 🔹 Denda --}}
      <div class="form-group">
        <label>Denda Tambahan (Rp)</label>
        <input type="number" step="1000" name="denda" id="denda" placeholder="Masukkan nominal denda jika ada">
      </div>

      {{-- 💳 Pilihan Metode Pembayaran --}}
      <div class="form-group" id="payment-method-section" style="display:none;">
        <label>Metode Pembayaran (untuk denda tambahan)</label>
        <select name="payment_method">
          <option value="qris">QRIS</option>
          <option value="bca">BCA Virtual Account</option>
          <option value="bri">BRI Virtual Account</option>
          <option value="bni">BNI Virtual Account</option>
          <option value="mandiri">Mandiri Virtual Account</option>
        </select>
        <small class="hint">💡 Pilih metode pembayaran yang akan digunakan di Duitku.</small>
      </div>

      {{-- 🔹 Catatan --}}
      <div class="form-group">
        <label>Catatan</label>
        <textarea name="catatan" placeholder="Keterangan tambahan (opsional)"></textarea>
      </div>

      {{-- 🔹 Tombol Aksi --}}
      <div class="form-actions">
        <button type="submit" class="btn-submit">💾 Buat Invoice</button>
        <a href="{{ route('admin.invoices.index') }}" class="btn-cancel">Batal</a>
      </div>
    </form>
  </div>
</div>

{{-- 🔄 Script Toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const dendaInput = document.getElementById('denda');
  const paymentSection = document.getElementById('payment-method-section');
  dendaInput.addEventListener('input', function() {
    paymentSection.style.display = parseFloat(this.value) > 0 ? 'block' : 'none';
  });
});
</script>
@endsection
