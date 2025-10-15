@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Invoice</h2>

    {{-- Tombol Buat Invoice Baru --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary">
            ← Kembali ke Penyewaan
        </a>
        <a href="{{ route('admin.invoices.create', ['rental_id' => 0]) }}" 
           class="btn btn-primary">
            + Buat Invoice
        </a>
    </div>

    {{-- Tabel Invoice --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if($invoices->isEmpty())
                <p class="text-center text-muted">Belum ada invoice yang dibuat.</p>
            @else
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-primary">
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
                                    'tepat_waktu' => 'success',
                                    'telat' => 'warning',
                                    'rusak' => 'danger',
                                    default => 'secondary'
                                };

                                // Status invoice
                                $statusInvoice = strtolower($invoice->status_invoice ?? 'pending');
                                $warnaInvoice = match($statusInvoice) {
                                    'pending' => 'warning',
                                    'selesai' => 'success',
                                    'cancel'  => 'danger',
                                    default   => 'secondary'
                                };
                            @endphp

                            <tr>
                                {{-- Nomor Transaksi --}}
                                <td><strong>INV{{ str_pad($invoice->invoice_id, 4, '0', STR_PAD_LEFT) }}</strong></td>

                                {{-- Pengguna --}}
                                <td>
                                    {{ $invoice->rental->user->nama_lengkap ?? '-' }} <br>
                                    <small class="text-muted">{{ $invoice->rental->user->email ?? '-' }}</small>
                                </td>

                                {{-- Mobil --}}
                                <td>
                                    {{ optional($invoice->rental->car->brand ?? null)->nama_merek ?? '-' }}
                                    {{ $invoice->rental->car->model ?? '' }}
                                </td>

                                {{-- Status Pengembalian --}}
                                <td>
                                    <span class="badge bg-{{ $warnaPengembalian }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->status_pengembalian)) }}
                                    </span>
                                </td>

                                {{-- Status Invoice --}}
                                <td>
                                    <span class="badge bg-{{ $warnaInvoice }}">
                                        {{ ucfirst($statusInvoice) }}
                                    </span>
                                </td>

                                {{-- Total Charge (denda tambahan) --}}
                                <td>
                                    @if($invoice->denda_tambahan > 0)
                                        <strong class="text-danger">Rp{{ number_format($invoice->denda_tambahan, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Tanggal Cetak --}}
                                <td>{{ \Carbon\Carbon::parse($invoice->tanggal_cetak)->format('d/m/Y H:i') }}</td>

                                {{-- Aksi --}}
                                <td>
                                    <a href="{{ route('admin.invoices.show', $invoice->invoice_id) }}" 
                                       class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
