@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Buat Invoice Baru</h2>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.invoices.store', $rental->rental_id ?? 0) }}" method="POST">
            @csrf

            @if($rental)
            <div class="mb-3">
                <label>Mobil</label>
                <input type="text" class="form-control" 
                       value="{{ $rental->car->brand->nama_merek }} {{ $rental->car->model ?? '' }}" readonly>
            </div>

            <div class="mb-3">
                <label>Penyewa</label>
                <input type="text" class="form-control" value="{{ $rental->user->nama_lengkap }}" readonly>
            </div>

            <div class="mb-3">
                <label>Total Biaya Sewa</label>
                <input type="text" class="form-control" 
                       value="Rp {{ number_format($rental->total_biaya,0,',','.') }}" readonly>
            </div>
            @endif

            <div class="mb-3">
                <label>Status Pengembalian</label>
                <select name="status_pengembalian" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="tepat_waktu">Tepat Waktu</option>
                    <option value="telat">Telat</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Denda Tambahan (Rp)</label>
                <input type="number" step="1000" name="denda" id="denda" 
                       class="form-control" placeholder="Masukkan nominal denda jika ada">
            </div>

            {{-- 💳 Pilihan Metode Pembayaran muncul hanya jika denda > 0 --}}
            <div class="mb-3" id="payment-method-section" style="display:none;">
                <label>Metode Pembayaran (untuk denda tambahan)</label>
                <select name="payment_method" class="form-control">
                    <option value="qris">QRIS</option>
                    <option value="bca">BCA Virtual Account</option>
                    <option value="bri">BRI Virtual Account</option>
                    <option value="bni">BNI Virtual Account</option>
                    <option value="mandiri">Mandiri Virtual Account</option>
                </select>
                <small class="text-muted">
                    💡 Pilih metode pembayaran yang akan digunakan di Duitku.
                </small>
            </div>

            <div class="mb-3">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" 
                          placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">💾 Buat Invoice</button>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

{{-- Script untuk toggle tampilan pilihan metode pembayaran --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dendaInput = document.getElementById('denda');
    const paymentSection = document.getElementById('payment-method-section');

    dendaInput.addEventListener('input', function() {
        if (parseFloat(this.value) > 0) {
            paymentSection.style.display = 'block';
        } else {
            paymentSection.style.display = 'none';
        }
    });
});
</script>
@endsection
