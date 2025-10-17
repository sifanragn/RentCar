<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kuitansi Pembayaran</title>
  <style>
    body {
      font-family: 'DejaVu Sans', 'Poppins', sans-serif;
      background: #f4f7fb;
      margin: 0;
      padding: 30px 0;
      color: #333;
      font-size: 14px;
    }

    .invoice-box {
      max-width: 800px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border: 1px solid #e0e6ed;
    }

    /* ===== HEADER ===== */
    .header {
      background: linear-gradient(135deg, #1976d2, #42a5f5);
      color: #fff;
      text-align: center;
      padding: 30px;
      position: relative;
    }

    .header h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .header small {
      display: block;
      font-size: 13px;
      opacity: 0.9;
      margin-top: 6px;
    }

    .header::after {
      content: '';
      position: absolute;
      bottom: -18px;
      left: 0;
      width: 100%;
      height: 36px;
      background: linear-gradient(to bottom, rgba(25,118,210,0.1), transparent);
      border-radius: 50% 50% 0 0;
    }

    /* ===== STAMP ===== */
    .stamp {
      position: absolute;
      top: 110px;
      right: 60px;
      transform: rotate(-12deg);
      color: #28a745;
      border: 3px solid #28a745;
      font-size: 34px;
      font-weight: bold;
      padding: 8px 22px;
      border-radius: 10px;
      opacity: 0.25;
      letter-spacing: 2px;
    }

    /* ===== CONTENT ===== */
    .content {
      padding: 40px;
      position: relative;
      z-index: 2;
    }

    .content p {
      margin: 8px 0;
    }

    /* ===== LABEL ===== */
    .status-label {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      margin-left: 8px;
    }
    .success { background: #d4edda; color: #155724; }
    .failed { background: #f8d7da; color: #721c24; }
    .pending { background: #fff3cd; color: #856404; }

    .badge-type {
      background: #e3f2fd;
      color: #0d6efd;
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 10px;
      display: inline-block;
    }

    /* ===== TABLE ===== */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 14px;
    }

    th, td {
      padding: 10px 12px;
      border-bottom: 1px solid #eee;
      vertical-align: top;
    }

    th {
      width: 35%;
      font-weight: 600;
      color: #111;
      background: #f9fafc;
      text-align: left;
    }

    tr:nth-child(even) td {
      background: #fcfdff;
    }

    /* ===== SIGNATURE ===== */
    .signature {
      margin-top: 50px;
      text-align: right;
      font-size: 13px;
    }

    .signature strong {
      display: block;
      margin-top: 40px;
      text-decoration: underline;
    }

    /* ===== FOOTER ===== */
    .footer {
      text-align: center;
      padding: 16px;
      font-size: 12px;
      color: #555;
      background: linear-gradient(90deg, #f5f7fa, #e2ebf0);
      border-top: 2px solid #dee2e6;
    }

    /* ===== BACKGROUND MOBIL ===== */
    .car-bg {
      position: absolute;
      bottom: 25px;
      right: 30px;
      width: 190px;
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

        @if($payment->payment_type === 'charge' && $payment->rental->invoice)
          <tr>
            <th>Status Pengembalian</th>
            <td>{{ str_replace('_', ' ', $payment->rental->invoice->status_pengembalian ?? '-') }}</td>
          </tr>
          <tr>
            <th>Deskripsi</th>
            <td>{{ $payment->rental->invoice->catatan ?? '-' }}</td>
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
        Hormat kami,<br>
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
