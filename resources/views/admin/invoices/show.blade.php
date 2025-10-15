<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Invoice #{{ $invoice->invoice_id }}</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f5f7fa;margin:0;padding:30px; }
    .container { max-width:800px;margin:auto;background:white;border-radius:12px;box-shadow:0 3px 8px rgba(0,0,0,0.08);padding:25px; }
    h2 { margin-top:0;color:#333; }
    .grid { display:grid;grid-template-columns:180px 1fr;row-gap:10px; }
    .label { font-weight:600;color:#333; }
    .value strong { color:#000; }
    .section-title { font-size:18px;margin-top:25px;border-bottom:2px solid #0d6efd;width:max-content;padding-bottom:3px; }
    .status { display:inline-block;padding:6px 12px;border-radius:20px;font-size:13px;font-weight:600;text-transform:capitalize; }
    .status.pending { background:#fff3cd;color:#856404; }
    .status.success { background:#d1e7dd;color:#0f5132; }
    .status.failed { background:#f8d7da;color:#842029; }
    .status.cancel { background:#f8d7da;color:#842029; }
    .status.selesai { background:#d1e7dd;color:#0f5132; }
    .status.waiting { background:#e2e3e5;color:#41464b; }
    a.back { color:#0d6efd;text-decoration:none;display:inline-block;margin-top:25px; }
    a.back:hover { text-decoration:underline; }
  </style>
</head>
<body>
<div class="container">
    @if(session('success'))
    <div style="background:#d1e7dd;color:#0f5132;padding:10px;border-radius:8px;margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#f8d7da;color:#842029;padding:10px;border-radius:8px;margin-bottom:15px;">
        {{ session('error') }}
    </div>
@endif
@if(session('info'))
    <div style="background:#fff3cd;color:#856404;padding:10px;border-radius:8px;margin-bottom:15px;">
        {{ session('info') }}
    </div>
@endif

  <h2>Detail Invoice #{{ $invoice->invoice_id }}</h2>

  {{-- 🧾 Data Penyewaan --}}
  <h3 class="section-title">🚗 Data Penyewaan</h3>
  <div class="grid">
    <div class="label">Mobil:</div>
    <div class="value">{{ $invoice->rental->car->brand->nama_merek ?? '' }} {{ $invoice->rental->car->model ?? '-' }}</div>

    <div class="label">Penyewa:</div>
    <div class="value">{{ $invoice->rental->user->nama_lengkap ?? '-' }} ({{ $invoice->rental->user->email ?? '-' }})</div>

    <div class="label">Tanggal Sewa:</div>
    <div class="value">{{ $invoice->rental->tanggal_mulai }} → {{ $invoice->rental->tanggal_selesai }}</div>

    <div class="label">Status Sewa:</div>
    <div class="value"><span class="status {{ strtolower($invoice->rental->status_rental) }}">{{ ucfirst($invoice->rental->status_rental) }}</span></div>
  </div>

  {{-- 💰 Data Pembayaran --}}
  @if($invoice->rental->payment)
  <h3 class="section-title">💳 Data Pembayaran</h3>
  <div class="grid">
      <div class="label">Payment ID:</div>
      <div class="value">#{{ $invoice->rental->payment->payment_id }}</div>

      <div class="label">Metode:</div>
      <div class="value">{{ strtoupper($invoice->rental->payment->metode) }}</div>

      <div class="label">Tipe:</div>
      <div class="value">{{ ucfirst($invoice->rental->payment->payment_type ?? '-') }}</div>

      <div class="label">Gateway:</div>
      <div class="value">{{ $invoice->rental->payment->gateway }}</div>

      <div class="label">Status Pembayaran:</div>
      <div class="value"><span class="status {{ strtolower($invoice->rental->payment->status_pembayaran) }}">{{ ucfirst($invoice->rental->payment->status_pembayaran) }}</span></div>

      <div class="label">Total Bayar:</div>
      <div class="value"><strong>Rp {{ number_format($invoice->rental->payment->total_bayar, 0, ',', '.') }}</strong></div>

      <div class="label">Tanggal Bayar:</div>
      <div class="value">{{ $invoice->rental->payment->tanggal_bayar ?? '-' }}</div>

      <div class="label">Gateway Ref:</div>
      <div class="value">{{ $invoice->rental->payment->gateway_reference }}</div>

      @if(!empty($invoice->rental->payment->payment_token))
        <div class="label">Link Pembayaran:</div>
        <div class="value">
          <a href="{{ $invoice->rental->payment->payment_token }}" target="_blank" style="color:#0d6efd;">Lihat di Duitku ↗</a>
        </div>
      @endif
  </div>
  @endif

  {{-- 🧾 Data Invoice --}}
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
    <div class="value">{{ $invoice->admin->nama_admin ?? '—' }}</div>

    <div class="label">Tanggal Cetak:</div>
    <div class="value">{{ $invoice->tanggal_cetak }}</div>
   {{-- 🔁 Kirim Ulang Pembayaran jika invoice dibatalkan --}}
@if(isset($invoice->status_invoice) && in_array(strtolower($invoice->status_invoice), ['cancel', 'dibatalkan']))
  <form method="POST" 
        action="{{ route('admin.invoices.retryPayment', $invoice->invoice_id) }}" 
        style="margin-top:25px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      @csrf
      <label for="payment_method" style="font-weight:600;">Metode:</label>
      <select name="payment_method" id="payment_method" required
              style="padding:6px 10px;border-radius:6px;border:1px solid #ccc;">
          <option value="qris">QRIS</option>
          <option value="bca">BCA Virtual Account</option>
          <option value="bri">BRI Virtual Account</option>
          <option value="bni">BNI Virtual Account</option>
          <option value="mandiri">Mandiri Virtual Account</option>
      </select>

      <button type="submit" class="btn"
              style="background:#ffc107;color:#000;font-weight:600;padding:8px 14px;border:none;border-radius:8px;cursor:pointer;">
          🔁 Kirim Ulang Pembayaran
      </button>
  </form>
@endif

{{-- 🛑 Tambahkan tombol Batalkan (untuk admin) --}}
@if(!in_array(strtolower($invoice->status_invoice), ['cancel', 'dibatalkan', 'selesai']))
  <form method="POST"
        action="{{ route('admin.invoices.cancel', $invoice->invoice_id) }}"
        onsubmit="return confirm('Yakin ingin membatalkan invoice ini?')"
        style="margin-top:20px;">
      @csrf
      <button type="submit" class="btn"
              style="background:#dc3545;color:#fff;font-weight:600;padding:8px 14px;border:none;border-radius:8px;cursor:pointer;">
          ❌ Batalkan Invoice
      </button>
  </form>
@endif


  </div>

  <a href="{{ route('admin.invoices.index') }}" class="back">← Kembali ke daftar</a>
</div>
</body>
</html>
