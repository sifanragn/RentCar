<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kuitansi Pembayaran</title>
  <style>
    body {
      font-family: 'Poppins', DejaVu Sans, sans-serif;
      background: #e8edf5;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .invoice-box {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      position: relative;
    }

    /* HEADER */
    .header {
      background: linear-gradient(135deg, #1976d2, #42a5f5);
      padding: 35px 40px;
      color: #fff;
      position: relative;
      text-align: center;
    }
    .header h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .header small {
      font-size: 13px;
      opacity: 0.9;
    }
    .header::after {
      content: '';
      position: absolute;
      bottom: -30px;
      left: 0;
      width: 100%;
      height: 50px;
      background: linear-gradient(to bottom, rgba(25,118,210,0.15), transparent);
      border-radius: 50% 50% 0 0;
    }

    /* STATUS STAMP */
    .stamp {
      position: absolute;
      top: 110px;
      right: 60px;
      transform: rotate(-12deg);
      color: #28a745;
      border: 3px solid #28a745;
      font-size: 38px;
      font-weight: bold;
      padding: 10px 24px;
      border-radius: 10px;
      opacity: 0.25;
      letter-spacing: 3px;
    }

    /* INFO AREA */
    .content {
      padding: 40px;
      position: relative;
      z-index: 2;
    }

    .content p {
      margin: 6px 0;
    }

    .status-label {
      display: inline-block;
      padding: 4px 9px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      margin-left: 6px;
    }
    .success { background: #d4edda; color: #155724; }
    .failed { background: #f8d7da; color: #721c24; }
    .pending { background: #fff3cd; color: #856404; }

    .badge-type {
      background: #e3f2fd;
      color: #0d6efd;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 8px;
      display: inline-block;
    }

    /* TABLE */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 14px;
      font-size: 14px;
    }
    th, td {
      padding: 10px 12px;
      border-bottom: 1px solid #eee;
      vertical-align: top;
    }
    th {
      background: #f9fafc;
      width: 35%;
      font-weight: 600;
      color: #111;
      text-align: left;
    }
    tr:nth-child(even) td { background: #fcfdff; }

    .signature {
      margin: 50px 0 10px;
      text-align: right;
      font-size: 13px;
    }
    .signature strong {
      display: block;
      margin-top: 40px;
      text-decoration: underline;
    }

    .footer {
      text-align: center;
      padding: 16px;
      font-size: 12px;
      color: #555;
      background: linear-gradient(90deg, #f5f7fa, #e2ebf0);
      border-top: 2px solid #dee2e6;
    }

    /* Background mobil */
    .car-bg {
      position: absolute;
      bottom: 20px;
      right: 30px;
      width: 200px;
      opacity: 0.08;
      z-index: 0;
    }
  </style>
</head>
<body>

  <div class="invoice-box">
    <div class="header">
      <h1>
        @if($payment->payment_type === 'charge')
          Kuitansi Tambahan
        @elseif($payment->payment_type === 'final')
          Kuitansi Akhir
        @else
          Kuitansi Pembayaran
        @endif
      </h1>
      <small>RentCar System – Bukti Pembayaran Resmi</small>
    </div>

    @if($payment->status_pembayaran === 'success')
      <div class="stamp">PAID</div>
    @endif

    <div class="content">
      <p><strong>ID Pembayaran:</strong> #{{ $payment->payment_id }}
        <span class="status-label {{ strtolower($payment->status_pembayaran) }}">
          {{ ucfirst($payment->status_pembayaran) }}
        </span>
      </p>

      <p><strong>No. Transaksi:</strong> {{ $payment->no_transaksi ?? '-' }}</p>
      <p><strong>Gateway:</strong> {{ $payment->gateway ?? 'Duitku' }}</p>
      <span class="badge-type">{{ ucfirst($payment->payment_type ?? 'Utama') }}</span>

      <table>
        <tr>
          <th>Mobil</th>
          <td>{{ $payment->rental->car->brand->nama_merek ?? '-' }} {{ $payment->rental->car->model ?? '' }}</td>
        </tr>
        <tr>
          <th>Tanggal Sewa</th>
          <td>
            {{ \Carbon\Carbon::parse($payment->rental->tanggal_mulai)->format('d M Y, H:i') }}
            → 
            {{ \Carbon\Carbon::parse($payment->rental->tanggal_selesai)->format('d M Y, H:i') }}
          </td>
        </tr>
        <tr>
          <th>Metode Pengambilan</th>
          <td>
            @if($payment->rental->metode_pickup === 'ambil_sendiri')
              Ambil Sendiri ke Kantor
            @elseif($payment->rental->metode_pickup === 'pickup_alamat')
              Antar ke Alamat Penyewa
            @else
              -
            @endif
          </td>
        </tr>

        {{-- Tambahan khusus jika ini kuitansi tambahan (charge) --}}
@if($payment->payment_type === 'charge' && $payment->rental->invoice)
  @php
    $invoice = $payment->rental->invoice;
    // cari payment utama
    $mainPayment = $payment->rental->payments()
        ->where('payment_type', 'main')
        ->where('status_pembayaran', 'success')
        ->latest()
        ->first();
  @endphp

  <tr>
    <th>Status Pengembalian</th>
    <td>{{ str_replace('_', ' ', $invoice->status_pengembalian ?? '-') }}</td>
  </tr>
  <tr>
    <th>Catatan Pengembalian</th>
    <td>{{ $invoice->catatan ?? '-' }}</td>
  </tr>
  <tr>
    <th>Total dari Kuitansi Utama</th>
    <td>
      @if($mainPayment)
        <strong>Rp{{ number_format($mainPayment->total_bayar, 0, ',', '.') }}</strong>
      @else
        <em>Belum ada pembayaran utama</em>
      @endif
    </td>
  </tr>
  <tr>
    <th>Denda Tambahan</th>
    <td><strong>Rp{{ number_format($invoice->denda_tambahan ?? 0, 0, ',', '.') }}</strong></td>
  </tr>
  <tr style="background:#f6fbff;">
    <th>Total Akhir</th>
    <td><strong>Rp{{ number_format(($invoice->total_akhir ?? $payment->total_bayar), 0, ',', '.') }}</strong></td>
  </tr>
@endif

        <tr>
          <th>Metode Pembayaran</th>
          <td>{{ strtoupper($payment->metode) }}</td>
        </tr>
        <tr>
          <th>Total</th>
          <td><strong>Rp{{ number_format($payment->total_bayar, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
          <th>Tanggal Pembayaran</th>
          <td>{{ \Carbon\Carbon::parse($payment->tanggal_bayar)->format('d M Y, H:i') }} WIB</td>
        </tr>
      </table>

      <div class="signature">
        Hormat Kami,<br>
        <strong>Admin RentCar</strong>
      </div>
    </div>

    <div class="footer">
      Terima kasih telah menggunakan layanan kami 🙏<br>
      <small>Dicetak otomatis oleh sistem RentCar pada {{ now()->format('d M Y, H:i') }} WIB</small>
    </div>

    <img class="car-bg" src="{{ public_path('img/car-bg.png') }}" alt="mobil">
  </div>
</body>
</html>
