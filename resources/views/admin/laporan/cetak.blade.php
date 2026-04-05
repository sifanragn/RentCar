<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penyewaan & Invoice</title>

  <style>
/* ===== RESET ===== */
body {
    font-family: "Segoe UI", Arial, sans-serif;
    font-size: 12px;
    color: #000;
    margin: 20px;
    line-height: 1.5;
}

/* ===== HEADER ===== */
h2 {
    text-align: center;
    margin-bottom: 5px;
    font-size: 18px;
    letter-spacing: 1px;
}

h3 {
    margin-top: 25px;
    margin-bottom: 5px;
    font-size: 14px;
    border-left: 4px solid #000;
    padding-left: 6px;
}

h4 {
    margin-top: 15px;
    margin-bottom: 5px;
    font-size: 13px;
}

/* ===== DIVIDER ===== */
.divider {
    border: none;
    border-top: 1px solid #999;
    margin: 15px 0;
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
    margin-bottom: 15px;
}

table thead {
    background: #000;
        color: #fff;         /* teks putih biar kontras */

}

table th {
    font-size: 11px;
    padding: 6px;
    border: 1px solid #000;
    text-align: center;
}

table td {
    font-size: 11px;
    padding: 5px;
    border: 1px solid #000;
}

/* Zebra biar enak dibaca */
table tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* ===== TOTAL BOX ===== */
.total-box {
    margin-top: 20px;
    padding: 10px;
    border: 2px solid #000;
    font-size: 14px;
    text-align: right;
}

/* ===== FOOTER ===== */
footer {
    margin-top: 25px;
    text-align: center;
    font-size: 11px;
    color: #555;
}

/* ===== PRINT SETTING ===== */
@media print {

    body {
        margin: 10mm;
    }

    h2, h3, h4 {
        page-break-after: avoid;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group; /* header ulang tiap halaman */
    }

    tfoot {
        display: table-footer-group;
    }

    /* Hilangin link underline */
    a {
        text-decoration: none;
        color: #000;
    }
}
</style>
</head>

<body onload="window.print()">

  {{-- Header --}}
  <h2>LAPORAN PENYEWAAN & INVOICE RENTCAR</h2>
  <p style="text-align:center; font-size:13px; color:#555;">Dicetak: {{ $tanggalCetak }}</p>
  <hr class="divider">

  @php
    $groupedRentals = $rentals->sortBy('tanggal_mulai')->groupBy(function ($r) {
        return \Carbon\Carbon::parse($r->tanggal_mulai)->translatedFormat('F Y');
    });
  @endphp

  @foreach($groupedRentals as $bulan => $rentalGroup)
    <h3>{{ strtoupper($bulan) }}</h3>

    @php
      $selesai = $rentalGroup->whereIn('status_rental', ['selesai', 'selesai_dengan_charge']);
      $dibatalkan = $rentalGroup->where('status_rental', 'dibatalkan');
    @endphp

    @if($selesai->count())
      <h4>📗 Penyewaan Selesai</h4>

      {{-- Kuitansi Utama --}}
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama User</th>
            <th>Mobil</th>
            <th>Tgl Sewa</th>
            <th>Tgl Selesai</th>
            <th>Total (Rp)</th>
            <th>Status</th>
            <th>Tipe Kuitansi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($selesai as $rental)
            @foreach($rental->payments->where('payment_type', 'main') as $p)
              <tr>
                <td>{{ $loop->parent->iteration }}</td>
                <td>{{ $rental->user->nama_lengkap }}</td>
                <td>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</td>
                <td>{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m/Y H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m/Y H:i') }}</td>
                <td>{{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                <td>{{ ucfirst($rental->status_rental) }}</td>
                <td>Kuitansi Utama</td>
              </tr>
            @endforeach
          @endforeach
        </tbody>
      </table>

      @php
        $charges = $selesai->flatMap(fn($r) => $r->payments->where('payment_type', 'charge'));
      @endphp

      @if($charges->count())
        <h4>📘 Kuitansi Tambahan (Charge)</h4>
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama User</th>
              <th>Mobil</th>
              <th>Denda (Rp)</th>
              <th>Total Akhir (Rp)</th>
              <th>Status Invoice</th>
              <th>Status Pembayaran</th>
              <th>Tanggal Pembayaran</th>
            </tr>
          </thead>
          <tbody>
            @foreach($charges as $p)
              @php
                $invoice = $p->rental->invoice ?? null;
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->rental->user->nama_lengkap }}</td>
                <td>{{ $p->rental->car->brand->nama_merek ?? '-' }} {{ $p->rental->car->model }}</td>
                <td>{{ number_format($invoice->denda_tambahan ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($invoice->total_akhir ?? $p->total_bayar, 0, ',', '.') }}</td>
                <td>{{ ucfirst($invoice->status_invoice ?? '-') }}</td>
                <td>{{ ucfirst($p->status_pembayaran) }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    @endif

    @if($dibatalkan->count())
      <h4>📕 Penyewaan Dibatalkan</h4>
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama User</th>
            <th>Mobil</th>
            <th>Tgl Sewa</th>
            <th>Tgl Selesai</th>
            <th>Total (Rp)</th>
            <th>Status</th>
            <th>Alasan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($dibatalkan as $rental)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $rental->user->nama_lengkap }}</td>
              <td>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</td>
              <td>{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m/Y H:i') }}</td>
              <td>{{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m/Y H:i') }}</td>
              <td>{{ number_format($rental->total_biaya, 0, ',', '.') }}</td>
              <td>{{ ucfirst($rental->status_rental) }}</td>
              <td>{{ $rental->catatan ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    <hr class="divider">
  @endforeach

  <div class="total-box">
    <strong>Total Pendapatan Keseluruhan:</strong>
    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
  </div>

  <footer>
    Dicetak pada {{ $tanggalCetak }} — Sistem RentCar Admin
  </footer>

</body>
</html>
