@extends('layouts.admin.app')

@section('title', 'Detail Driver')

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>👨‍✈️ Detail Driver</h2>
    <a href="{{ route('admin.drivers.index') }}" class="btn-secondary">← Kembali</a>
  </div>

  <div class="driver-detail-card">
    {{-- FOTO UTAMA --}}
    <img src="{{ $driver->foto ? asset('storage/' . $driver->foto) : asset('img/default-user.png') }}" 
         alt="Driver Foto" class="driver-detail-foto">

    <div class="driver-detail-info">
      <h3>{{ $driver->nama }}</h3>
      <p><strong>Nomor HP:</strong> {{ $driver->no_hp ?? '-' }}</p>
      <p><strong>Email:</strong> {{ $driver->email ?? '-' }}</p>
      <p><strong>Nomor SIM:</strong> {{ $driver->sim_number ?? '-' }}</p>
      <p><strong>Lokasi:</strong> {{ $driver->lokasi ?? '-' }}</p>
      <p><strong>Pengalaman:</strong> {{ $driver->pengalaman ?? '-' }}</p>
      <p><strong>Tarif / Jam:</strong> Rp{{ number_format($driver->harga_per_jam, 0, ',', '.') }}</p>
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

      <div class="actions">
        <a href="{{ route('admin.drivers.edit', $driver->driver_id) }}" class="btn-edit">✏ Edit</a>
        <form action="{{ route('admin.drivers.destroy', $driver->driver_id) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menghapus driver ini?')" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-danger">🗑 Hapus</button>
        </form>
      </div>
    </div>
  </div>

  {{-- 🔹 FOTO DOKUMEN --}}
  <div class="document-section">
    <h3>📎 Dokumen Identitas</h3>
    <div class="document-grid">
      {{-- Foto SIM --}}
      <div class="doc-card">
        <p><strong>Foto SIM</strong></p>
        @if($driver->foto_sim)
          <img src="{{ asset('storage/' . $driver->foto_sim) }}" alt="Foto SIM">
        @else
          <p class="no-doc">Belum diunggah</p>
        @endif
      </div>

      {{-- Foto KTP --}}
      <div class="doc-card">
        <p><strong>Foto KTP</strong></p>
        @if($driver->foto_ktp)
          <img src="{{ asset('storage/' . $driver->foto_ktp) }}" alt="Foto KTP">
        @else
          <p class="no-doc">Belum diunggah</p>
        @endif
      </div>

      {{-- Foto KK --}}
      <div class="doc-card">
        <p><strong>Foto KK</strong></p>
        @if($driver->foto_kk)
          <img src="{{ asset('storage/' . $driver->foto_kk) }}" alt="Foto KK">
        @else
          <p class="no-doc">Belum diunggah</p>
        @endif
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

/* === FOTO DOKUMEN === */
.document-section {
  margin-top: 30px;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.document-section h3 {
  margin-bottom: 15px;
  color: #333;
}

.document-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.doc-card {
  text-align: center;
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 10px;
  background: #fafafa;
}

.doc-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
  margin-top: 6px;
  border: 2px solid #e0e0e0;
}

.no-doc {
  font-size: 13px;
  color: #999;
  font-style: italic;
  margin-top: 8px;
}
</style>
@endsection
