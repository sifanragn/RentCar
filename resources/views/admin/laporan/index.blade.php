@extends('layouts.admin.app')

@section('title', 'Laporan Keuangan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-laporan.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Laporan Keuangan Rental</h2>
    <p>Data ringkasan transaksi penyewaan yang telah dilakukan pengguna.</p>
  </div>

  <div class="laporan-actions">
  <form action="{{ route('admin.laporan.index') }}" method="GET" class="filter-form">
    <label>Bulan:</label>
    <select name="bulan">
      <option value="">Semua</option>
      @for ($m = 1; $m <= 12; $m++)
        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
          {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
        </option>
      @endfor
    </select>

    <label>Tahun:</label>
    <select name="tahun">
      <option value="">Semua</option>
      @for ($y = now()->year; $y >= now()->year - 5; $y--)
        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
    </select>

    <label>Jenis Kuitansi:</label>
    <select name="jenis">
      <option value="">Semua</option>
      <option value="utama" {{ request('jenis') == 'utama' ? 'selected' : '' }}>Kuitansi Utama</option>
      <option value="tambahan" {{ request('jenis') == 'tambahan' ? 'selected' : '' }}>Kuitansi Tambahan</option>
    </select>

    <button type="submit" class="btn-filter">🔍 Tampilkan</button>
  </form>

  {{-- Tombol Cetak --}}
  <a href="{{ route('admin.laporan.cetak', ['bulan' => request('bulan'), 'tahun' => request('tahun'), 'jenis' => request('jenis')]) }}" 
     target="_blank" 
     class="btn-cetak">
    🖨 Cetak Laporan
  </a>
</div>

  {{-- 🔹 Tabel Laporan --}}
  <div class="table-wrapper">
    @if($rentals->isEmpty())
      <p class="empty-text">Belum ada data penyewaan untuk ditampilkan.</p>
    @else
      <table class="laporan-table">
        <thead>
  <tr>
    <th>No</th>
    <th>Nama User</th>
    <th>Mobil</th>
    <th>Tanggal Sewa</th>
    <th>Tanggal Selesai</th>
    <th>Total (Rp)</th>
    <th>Status</th>
    <th>Metode Pembayaran</th>
    <th>Jenis Kuitansi</th>
  </tr>
</thead>
<tbody>
  @foreach($rentals as $rental)
    @foreach($rental->payments as $payment)
<tr>
    <td>{{ $loop->parent->iteration }}</td>
    <td>{{ $rental->user->nama_lengkap }}</td>
    <td>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</td>
    <td>{{ $rental->tanggal_mulai }}</td>
    <td>{{ $rental->tanggal_selesai }}</td>
    <td>Rp{{ number_format($payment->total_bayar, 0, ',', '.') }}</td>

    <td>
        <span class="status {{ strtolower($payment->status_pembayaran) }}">
            {{ ucfirst($payment->status_pembayaran) }}
        </span>
    </td>

    <td class="payment-method">
    @php
        $method = strtolower($payment->metode_pembayaran);
        $logos = [
            'bca' => 'bca.png',
            'bni' => 'bni.png',
            'bri' => 'bri.png',
            'mandiri' => 'mandiri.png',
            'gopay' => 'gopay.png',
            'ovo' => 'ovo.png',
            'dana' => 'dana.png',
            'qris' => 'qris.png',
            'cash' => 'cash.png',
        ];
    @endphp

    @if(isset($logos[$method]))
        <img 
        src="{{ asset('images/' . $logos[$method]) }}" 
        alt="{{ strtoupper($method) }}" 
        class="payment-logo"
      >
    @else
        <span>-</span>
    @endif
</td>

    <td>
        <span class="badge {{ $payment->payment_type === 'charge' ? 'badge-tambahan' : 'badge-utama' }}">
            {{ $payment->payment_type === 'charge' ? 'Kuitansi Tambahan' : 'Kuitansi Utama' }}
        </span>
    </td>
</tr>
@endforeach

  @endforeach
</tbody>
      </table>

      {{-- 🔹 Total Pendapatan --}}
      <div class="total-box">
        <strong>Total Pendapatan:</strong> 
        <span>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
      </div>
    @endif
  </div>
</div>
@endsection
