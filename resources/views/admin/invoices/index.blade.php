@extends('layouts.admin.app')

@section('title', 'Daftar Invoice')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-invoice-index.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Daftar Invoice</h2>
    <p>Kelola semua invoice penyewaan dan pantau status pengembalian mobil.</p>
  </div>

  {{-- 🔹 Tombol Aksi --}}
  <div class="invoice-actions">
    <a href="{{ route('admin.rentals.index') }}" class="btn-back">
      ← Kembali ke Penyewaan
    </a>
    <a href="{{ route('admin.invoices.create', ['rental_id' => 0]) }}" class="btn-create">
      + Buat Invoice
    </a>
  </div>

  {{-- 🔹 Tabel Data --}}
  <div class="table-wrapper">
    @if($invoices->isEmpty())
      <p class="empty-text">Belum ada invoice yang dibuat.</p>
    @else
      <table class="invoice-table">
        <thead>
          <tr>
            <th>No. Transaksi</th>
            <th>Pengguna</th>
            <th>Mobil</th>
            <th>Status Pengembalian</th>
            <th>Status Invoice</th>
            <th>Total Charge</th>
            <th>Tanggal Cetak</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoices as $invoice)
            @php
              // Status pengembalian
              $statusPengembalian = strtolower($invoice->status_pengembalian ?? 'tidak_diketahui');
              $warnaPengembalian = match($statusPengembalian) {
                  'tepat_waktu' => 'status-green',
                  'telat' => 'status-yellow',
                  'rusak' => 'status-red',
                  default => 'status-gray'
              };

              // Status invoice
              $statusInvoice = strtolower($invoice->status_invoice ?? 'pending');
              $warnaInvoice = match($statusInvoice) {
                  'pending' => 'status-yellow',
                  'selesai' => 'status-green',
                  'cancel'  => 'status-red',
                  default   => 'status-gray'
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
              <td><span class="status {{ $warnaPengembalian }}">{{ ucfirst(str_replace('_', ' ', $invoice->status_pengembalian)) }}</span></td>
              <td><span class="status {{ $warnaInvoice }}">{{ ucfirst($statusInvoice) }}</span></td>
              <td>
                @if($invoice->denda_tambahan > 0)
                  <strong class="text-danger">Rp{{ number_format($invoice->denda_tambahan, 0, ',', '.') }}</strong>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>{{ \Carbon\Carbon::parse($invoice->tanggal_cetak)->format('d/m/Y H:i') }}</td>
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
</div>
@endsection
