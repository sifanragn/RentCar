@extends('layouts.admin.app')

@section('title', 'Daftar Charge')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-invoice-index.css') }}">

<style>
/* === ACTION BUTTON BAWAH === */
.invoice-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 25px;
}

/* === BUTTON STYLE (SAMAIN KY CAR) === */
.btn-cancel {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  height: 44px;
  padding: 0 26px;
  border-radius: 10px;

  font-size: 15px;
  font-weight: 600;
  text-decoration: none;

  background: rgba(255,255,255,0.06);
  color: #d0d0d0;
  border: 1px solid rgba(255,255,255,0.1);

  transition: 0.25s ease;
}

.btn-cancel:hover {
  background: rgba(255,255,255,0.12);
}
</style>
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Daftar Charge</h2>
    <p>Kelola semua charge penyewaan dan pantau status pengembalian mobil.</p>
  </div>

  {{-- 🔹 Tabel Data --}}
  <div class="table-wrapper">
    @if($invoices->isEmpty())
      <p class="empty-text">Belum ada charge yang dibuat.</p>
    @else
      <table class="invoice-table">
        <thead>
          <tr>
            <th>No. Transaksi</th>
            <th>Pengguna</th>
            <th>Mobil</th>
            <th>Status Pengembalian</th>
            <th>Status Charge</th>
            <th>Total Charge</th>
            <th>Tanggal Cetak</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoices as $invoice)
            @php
              $statusPengembalian = strtolower($invoice->status_pengembalian ?? 'tidak_diketahui');
              $warnaPengembalian = match($statusPengembalian) {
                  'tepat_waktu' => 'status-green',
                  'telat' => 'status-yellow',
                  'rusak' => 'status-red',
                  default => 'status-gray'
              };

              $statusInvoice = strtolower($invoice->status_invoice ?? 'pending');
              $warnaInvoice = match($statusInvoice) {
                  'pending' => 'status-yellow',
                  'selesai' => 'status-green',
                  'dibatalkan' => 'status-red',
                  default => 'status-gray'
              };
            @endphp

            <tr>
              <td><strong>INV{{ str_pad($invoice->invoice_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
              
              <td>
                {{ $invoice->rental->user->nama_lengkap ?? '-' }}<br>
                <small class="text-muted">{{ $invoice->rental->user->email ?? '-' }}</small>
              </td>

              <td>
                {{ optional($invoice->rental->car->brand)->nama_merek ?? '-' }}
                {{ $invoice->rental->car->model ?? '' }}
              </td>

              <td>
                <span class="status {{ $warnaPengembalian }}">
                  {{ ucfirst(str_replace('_', ' ', $invoice->status_pengembalian)) }}
                </span>
              </td>

              <td>
                <span class="status {{ $warnaInvoice }}">
                  {{ ucfirst($statusInvoice) }}
                </span>
              </td>

              <td>
                @if($invoice->denda_tambahan > 0)
                  <strong class="text-danger">
                    Rp{{ number_format($invoice->denda_tambahan, 0, ',', '.') }}
                  </strong>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>

              <td>
                {{ \Carbon\Carbon::parse($invoice->tanggal_cetak)->format('d/m/Y H:i') }}
              </td>

              <td>
                <a href="{{ route('admin.invoices.show', $invoice->invoice_id) }}" class="btn-detail">
                  <i class="bi bi-eye"></i> Detail
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- 🔹 Tombol Kembali (SUDAH DI BAWAH KANAN) --}}
  <div class="invoice-actions">
    <a href="{{ route('admin.rentals.index') }}" class="btn-cancel">
      Kembali
   </a>
  </div>

</div>
@endsection