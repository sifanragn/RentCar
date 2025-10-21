@extends('layouts.admin.app')

@section('title', 'Detail Invoice #' . $invoice->invoice_id)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-invoice-show.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  {{-- 💬 Flash Messages --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif
  @if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
  @endif

  <div class="invoice-card">
    <div class="header">
      <h2>Detail Invoice <span>#{{ $invoice->invoice_id }}</span></h2>
    </div>

    {{-- 🚗 Data Penyewaan --}}
    <h3 class="section-title">🚗 Data Penyewaan</h3>
    <div class="grid">
      <div class="label">Mobil:</div>
      <div class="value">{{ $invoice->rental->car->brand->nama_merek ?? '-' }} {{ $invoice->rental->car->model ?? '-' }}</div>

      <div class="label">Penyewa:</div>
      <div class="value">{{ $invoice->rental->user->nama_lengkap ?? '-' }} ({{ $invoice->rental->user->email ?? '-' }})</div>

      <div class="label">Tanggal Sewa:</div>
      <div class="value">{{ $invoice->rental->tanggal_mulai_wib ?? '-' }} → {{ $invoice->rental->tanggal_selesai_wib ?? '-' }}</div>

      <div class="label">Status Sewa:</div>
      <div class="value">
        <span class="status {{ strtolower($invoice->rental->status_rental) }}">{{ ucfirst($invoice->rental->status_rental) }}</span>
      </div>
    </div>

    {{-- 💳 Pembayaran Utama --}}
    @php $mainPayment = $invoice->rental->payments->firstWhere('payment_type', 'main'); @endphp
    @if($mainPayment)
      <h3 class="section-title">💳 Data Pembayaran Utama</h3>
      <div class="grid">
        <div class="label">Payment ID:</div>
        <div class="value">#{{ $mainPayment->payment_id }}</div>

        <div class="label">Metode:</div>
        <div class="value">{{ strtoupper($mainPayment->metode) }}</div>

        <div class="label">Tipe:</div>
        <div class="value">{{ ucfirst($mainPayment->payment_type ?? '-') }}</div>

        <div class="label">Gateway:</div>
        <div class="value">{{ $mainPayment->gateway ?? '-' }}</div>

        <div class="label">Status Pembayaran:</div>
        <div class="value">
          <span class="status {{ strtolower($mainPayment->status_pembayaran) }}">{{ ucfirst($mainPayment->status_pembayaran) }}</span>
        </div>

        <div class="label">Total Bayar:</div>
        <div class="value"><strong>Rp {{ number_format($mainPayment->total_bayar, 0, ',', '.') }}</strong></div>

        <div class="label">Tanggal Bayar:</div>
        <div class="value">{{ $mainPayment->tanggal_bayar ?? '-' }}</div>

        <div class="label">Gateway Ref:</div>
        <div class="value">{{ $mainPayment->gateway_reference ?? '-' }}</div>

        @if(!empty($mainPayment->payment_token))
          <div class="label">Link Pembayaran:</div>
          <div class="value"><a href="{{ $mainPayment->payment_token }}" target="_blank" class="link-blue">Lihat di Duitku ↗</a></div>
        @endif
      </div>
    @endif

    {{-- 💰 Pembayaran Denda --}}
    @php $chargePayment = $invoice->rental->payments->firstWhere('payment_type', 'charge'); @endphp
    @if($chargePayment)
      <h3 class="section-title">💰 Pembayaran Denda</h3>
      <div class="grid">
        <div class="label">Payment ID:</div>
        <div class="value">#{{ $chargePayment->payment_id }}</div>

        <div class="label">Metode:</div>
        <div class="value">{{ strtoupper($chargePayment->metode) }}</div>

        <div class="label">Status Pembayaran:</div>
        <div class="value">
          <span class="status {{ strtolower($chargePayment->status_pembayaran) }}">{{ ucfirst($chargePayment->status_pembayaran) }}</span>
        </div>

        <div class="label">Total Denda:</div>
        <div class="value"><strong>Rp {{ number_format($chargePayment->total_bayar, 0, ',', '.') }}</strong></div>

        <div class="label">Tanggal Bayar:</div>
        <div class="value">{{ $chargePayment->tanggal_bayar ?? '-' }}</div>

        <div class="label">Link Pembayaran:</div>
        <div class="value"><a href="{{ $chargePayment->payment_token }}" target="_blank" class="link-blue">Lihat di Duitku ↗</a></div>
      </div>
    @endif

    {{-- 📄 Data Invoice --}}
    <h3 class="section-title">📄 Data Invoice</h3>
    <div class="grid">
      <div class="label">Status Invoice:</div>
      <div class="value"><span class="status {{ strtolower($invoice->status_invoice) }}">{{ ucfirst($invoice->status_invoice) }}</span></div>

      <div class="label">Status Pengembalian:</div>
      <div class="value">{{ ucfirst($invoice->status_pengembalian) }}</div>

      <div class="label">Total Tagihan:</div>
      <div class="value"><strong>Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</strong></div>

      <div class="label">Denda Tambahan:</div>
      <div class="value">Rp {{ number_format($invoice->denda_tambahan, 0, ',', '.') }}</div>

      <div class="label">Total Akhir:</div>
      <div class="value"><strong>Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}</strong></div>

      <div class="label">Admin:</div>
      <div class="value">{{ $invoice->admin->nama_lengkap ?? '—' }}</div>

      <div class="label">Tanggal Cetak:</div>
      <div class="value">{{ $invoice->tanggal_cetak }}</div>
    </div>

    {{-- 🔁 Retry Payment --}}
    @if(isset($invoice->status_invoice) && in_array(strtolower($invoice->status_invoice), ['cancel', 'dibatalkan']))
      <form method="POST" action="{{ route('admin.invoices.retryPayment', $invoice->invoice_id) }}" class="form-inline">
        @csrf
        <label for="payment_method">Metode:</label>
        <select name="payment_method" id="payment_method" required>
          <option value="qris">QRIS</option>
          <option value="bca">BCA Virtual Account</option>
          <option value="bri">BRI Virtual Account</option>
          <option value="bni">BNI Virtual Account</option>
          <option value="mandiri">Mandiri Virtual Account</option>
        </select>
        <button type="submit" class="btn btn-warning">🔁 Kirim Ulang Pembayaran</button>
      </form>
    @endif

    {{-- 🛑 Batalkan Invoice --}}
    @if(!in_array(strtolower($invoice->status_invoice), ['cancel', 'dibatalkan', 'selesai']))
      <form method="POST" action="{{ route('admin.invoices.cancel', $invoice->invoice_id) }}" class="cancel-form"
            onsubmit="return confirm('Yakin ingin membatalkan invoice ini?')">
        @csrf
        <button type="submit" class="btn btn-danger">❌ Batalkan Invoice</button>
      </form>
    @endif

    <div class="back-wrapper">
      <a href="{{ route('admin.invoices.index') }}" class="btn-back">← Kembali ke Daftar</a>
    </div>
  </div>
</div>
@endsection
