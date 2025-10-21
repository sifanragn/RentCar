@extends('layouts.admin.app')

@section('title', 'Laporan Penyewaan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-laporan.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Laporan Penyewaan Mobil</h2>
    <p>Data ringkasan transaksi penyewaan yang telah dilakukan pengguna.</p>
  </div>

  {{-- 🔹 Tombol Cetak --}}
  <div class="laporan-actions">
    <a href="{{ route('admin.laporan.cetak') }}" target="_blank" class="btn-cetak">
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
          </tr>
        </thead>
        <tbody>
          @foreach($rentals as $rental)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rental->user->nama_lengkap }}</td>
            <td>{{ $rental->car->brand->nama_merek ?? '-' }} {{ $rental->car->model }}</td>
            <td>{{ $rental->tanggal_mulai }}</td>
            <td>{{ $rental->tanggal_selesai }}</td>
            <td>Rp{{ number_format($rental->total_biaya, 0, ',', '.') }}</td>
            <td>
              <span class="status {{ strtolower($rental->status_rental) }}">
                {{ ucfirst($rental->status_rental) }}
              </span>
            </td>
          </tr>
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
