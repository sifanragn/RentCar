@extends('layouts.admin.app')

@section('title', 'Kelola Driver')

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>👨‍✈️ Daftar Driver</h2>
    <a href="{{ route('admin.drivers.create') }}" class="btn-primary">➕ Tambah Driver</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  @if($drivers->isEmpty())
    <p>Belum ada driver yang terdaftar.</p>
  @else
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Foto</th>
          <th>Nama</th>
          <th>Nomor HP</th>
          <th>Lokasi</th>
          <th>Tarif / Hari</th>
          <th>Status</th>
          <th>Verifikasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($drivers as $i => $driver)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>
            <img src="{{ $driver->foto_url }}" alt="foto" class="driver-thumb">
          </td>
          <td>{{ $driver->nama }}</td>
          <td>{{ $driver->no_hp ?? '-' }}</td>
          <td>{{ $driver->lokasi ?? '-' }}</td>
          <td>{{ $driver->harga_formatted }}</td>
          <td>
            <span class="badge {{ $driver->status == 'aktif' ? 'active' : 'inactive' }}">
              {{ ucfirst($driver->status) }}
            </span>
          </td>
          <td>
            <span class="badge {{ $driver->status_verifikasi == 'disetujui' ? 'verified' : 'pending' }}">
              {{ ucfirst($driver->status_verifikasi) }}
            </span>
          </td>
          <td>
            <a href="{{ route('admin.drivers.show', $driver->driver_id) }}" class="btn-sm">👁 Lihat</a>
            <a href="{{ route('admin.drivers.edit', $driver->driver_id) }}" class="btn-sm btn-edit">✏ Edit</a>
            <form action="{{ route('admin.drivers.destroy', $driver->driver_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus driver ini?')" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-sm btn-danger">🗑 Hapus</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

<style>
.admin-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.admin-table th, .admin-table td {
  padding: 12px 14px;
  border-bottom: 1px solid #eee;
  text-align: left;
}

.admin-table th {
  background: #0d6efd;
  color: #fff;
}

.driver-thumb {
  width: 55px;
  height: 55px;
  border-radius: 8px;
  object-fit: cover;
}

.btn-sm {
  display: inline-block;
  padding: 6px 10px;
  border-radius: 6px;
  background: #0d6efd;
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  margin-right: 4px;
  transition: .2s;
}

.btn-edit { background: #ffc107; color: #000; }
.btn-danger { background: #dc3545; color: #fff; border:none; cursor:pointer; }
.btn-sm:hover { opacity: .85; }

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

.alert-success {
  background: #d1e7dd;
  color: #0f5132;
  padding: 10px 14px;
  border-radius: 8px;
  margin-bottom: 15px;
}
</style>
@endsection
