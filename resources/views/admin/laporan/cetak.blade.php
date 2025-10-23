<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penyewaan & Invoice</title>
  <style>
    @page { size: A4 portrait; margin: 25mm 20mm; }
    body { font-family: 'Poppins', sans-serif; margin: 0; color: #222; }
    h2, h3, h4 { text-align: center; color: #0d6efd; margin: 20px 0 10px; }
    h3 { margin-top: 40px; text-transform: uppercase; }
    h4 { color: #333; font-size: 15px; margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 18px; }
    th, td { border: 1px solid #444; padding: 7px 10px; text-align: left; vertical-align: top; }
    th { background: #0d6efd; color: #fff; font-weight: 600; }
    tr:nth-child(even) td { background: #f8f9fb; }
    .total-box { text-align: right; font-weight: 600; margin-top: 10px; background: #f9fafc; padding: 10px 14px; display: inline-block; border: 1px solid #ccc; border-radius: 8px; }
    .divider { border-top: 2px solid #0d6efd; margin: 30px 0; }
    footer { margin-top: 50px; font-size: 12px; text-align: center; color: #555; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
  </style>
</head>
<body onload="window.print()">

  {{-- Header --}}
  <h2>LAPORAN PENYEWAAN & INVOICE RENTCAR</h2>
  <p style="text-align:center; font-size:13px; color:#555;">Dicetak: {{ $tanggalCetak }}</p>
  <hr class="divider">

  @php
    // Kelompokkan per bulan berdasarkan tanggal_mulai
    $groupedRentals = $rentals->sortBy('tanggal_mulai')->groupBy(function ($r) {
        return \Carbon\Carbon::parse($r->tanggal_mulai)->translatedFormat('F Y');
    });
  @endphp

  @foreach($groupedRentals as $bulan => $rentalGroup)
    <h3>{{ strtoupper($bulan) }}</h3>

    {{-- =======================
         PENYEWAAN SELESAI
    ======================= --}}
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

      {{-- Kuitansi Tambahan --}}
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

    {{-- =======================
         PENYEWAAN DIBATALKAN
    ======================= --}}
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

    {{-- Divider tiap bulan --}}
    <hr class="divider">
  @endforeach

  <div class="total-box">
    <strong>Total Pendapatan Keseluruhan:</strong>
    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
  </div>

  {{-- Footer --}}
  <footer>
    Dicetak pada {{ $tanggalCetak }} — Sistem RentCar Admin
  </footer>
</body>
</html>
