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
      <p><strong>Tarif / Hari:</strong> Rp{{ number_format($driver->harga_per_hari, 0, ',', '.') }}</p>

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
          <button type="submit" class="btn-delete">🗑 Hapus</button>
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

{{-- ============================= STYLE ============================= --}}
<style>
/* === WRAPPER === */
.admin-content-wrapper {
  padding: 10px 30px 50px 30px;
  max-width: 1200px;
  margin: 0 auto;
}

/* === PAGE HEADER === */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding: 18px 24px;
  border-radius: 14px;
  background: #181c26;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
body.light-mode .page-header {
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.page-header h2 {
  font-size: 22px;
  font-weight: 700;
  color: #f5f7fa;
}
body.light-mode .page-header h2 { color: #222; }

.btn-secondary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  font-weight: 600;
  padding: 10px 18px;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.25s ease;
}
.btn-secondary:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

/* === CARD DETAIL === */
.driver-detail-card {
  display: flex;
  flex-wrap: wrap;
  gap: 25px;
  border-radius: 16px;
  padding: 30px;
  background: #141821;
  box-shadow: 0 3px 12px rgba(0,0,0,0.35);
  transition: all 0.3s ease;
}
body.light-mode .driver-detail-card {
  background: #ffffff;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

/* === FOTO DRIVER === */
.driver-detail-foto {
  width: 200px;
  height: 200px;
  border-radius: 14px;
  object-fit: cover;
  border: 2px solid rgba(255,255,255,0.1);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
body.light-mode .driver-detail-foto {
  border-color: #e5e5e5;
}

/* === INFO DRIVER === */
.driver-detail-info {
  flex: 1;
  min-width: 280px;
}
.driver-detail-info h3 {
  font-size: 22px;
  margin-bottom: 10px;
  color: #fff;
}
body.light-mode .driver-detail-info h3 { color: #222; }

.driver-detail-info p {
  margin: 6px 0;
  color: #d4d8e3;
  font-size: 15px;
}
body.light-mode .driver-detail-info p { color: #444; }

.driver-detail-info strong {
  color: #9fc8ff;
}
body.light-mode .driver-detail-info strong { color: #0d6efd; }

/* === BADGE === */
.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12.5px;
  font-weight: 600;
}
.badge.active { background: rgba(16,185,129,0.2); color: #10b981; }
.badge.inactive { background: rgba(239,68,68,0.2); color: #ef4444; }
.badge.verified { background: rgba(59,130,246,0.25); color: #3b82f6; }
.badge.pending { background: rgba(251,191,36,0.25); color: #fbbf24; }

/* === ACTIONS === */
.actions {
  margin-top: 18px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.btn-edit {
  background: #0d6efd;
  color: #fff;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  border: none;
  transition: all 0.25s ease;
}
.btn-edit:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}
.btn-delete {
  background: #e74c3c;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.25s ease;
}
.btn-delete:hover {
  transform: translateY(-2px);
  filter: brightness(1.15);
}

/* === FOTO DOKUMEN === */
.document-section {
  margin-top: 30px;
  background: #141821;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}
body.light-mode .document-section {
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.document-section h3 {
  margin-bottom: 15px;
  color: #f5f7fa;
}
body.light-mode .document-section h3 { color: #333; }

.document-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}
.doc-card {
  text-align: center;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  padding: 10px;
  background: #1d2430;
}
body.light-mode .doc-card {
  border-color: #eee;
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
