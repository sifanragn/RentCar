@extends('layouts.admin.app')

@section('title', 'Detail Driver')

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>👨‍✈️ Detail Driver</h2>
    <a href="{{ route('admin.drivers.index') }}" class="btn-secondary">← Kembali</a>
  </div>

  <div class="driver-detail-card">
    <img src="{{ $driver->foto_url }}" alt="Driver Foto" class="driver-detail-foto">

    <div class="driver-detail-info">
      <h3>{{ $driver->nama }}</h3>
      <p><strong>Nomor HP:</strong> {{ $driver->no_hp ?? '-' }}</p>
      <p><strong>Email:</strong> {{ $driver->email ?? '-' }}</p>
      <p><strong>Nomor SIM:</strong> {{ $driver->sim_number ?? '-' }}</p>
      <p><strong>Lokasi:</strong> {{ $driver->lokasi ?? '-' }}</p>
      <p><strong>Pengalaman:</strong> {{ $driver->pengalaman_formatted }}</p>
      <p><strong>Tarif / Hari:</strong> {{ $driver->harga_formatted }}</p>
      <p><strong>Status Verifikasi:</strong> 
        <span class="badge {{ $driver->status_verifikasi == 'disetujui' ? 'verified' : 'pending' }}">
          {{ ucfirst($driver->status_verifikasi) }}
        </span>
      </p>
      <p><strong>Status Aktif:</strong> 
        <span class="badge {{ $driver->status == 'aktif' ? 'active' : 'inactive' }}">
          {{ ucfirst($driver->status) }}
        </span>
      </p>
      <p><strong>Deskripsi:</strong> {{ $driver->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
      <p><strong>Dibuat oleh:</strong> {{ $driver->admin?->nama_admin ?? 'Admin' }}</p>

      <div class="actions">
        <a href="{{ route('admin.drivers.edit', $driver->driver_id) }}" class="btn-edit">✏ Edit</a>
        <form action="{{ route('admin.drivers.destroy', $driver->driver_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus driver ini?')" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-danger">🗑 Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.driver-detail-card {
  display: flex;
  flex-wrap: wrap;
  gap: 25px;
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.driver-detail-foto {
  width: 180px;
  height: 180px;
  border-radius: 12px;
  object-fit: cover;
  border: 3px solid #e5e5e5;
}

.driver-detail-info {
  flex: 1;
  min-width: 250px;
}

.driver-detail-info p {
  margin: 6px 0;
  color: #444;
}

.actions {
  margin-top: 15px;
}

.btn-edit {
  background: #ffc107;
  color: #000;
  padding: 8px 12px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  margin-right: 8px;
}

.btn-danger {
  background: #dc3545;
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
}
.badge.active { background: #d1e7dd; color: #0f5132; }
.badge.inactive { background: #f8d7da; color: #842029; }
.badge.verified { background: #cfe2ff; color: #084298; }
.badge.pending { background: #fff3cd; color: #664d03; }
</style>
@endsection
