@extends('layouts.admin.app')

@section('title', 'Edit Driver')

@section('content')
<div class="admin-content-wrapper">
  {{-- HEADER --}}
  <div class="page-header">
    <h2>✏️ Edit Data Driver</h2>
  </div>

  {{-- CARD FORM --}}
  <div class="driver-form-card">
    @if ($errors->any())
      <div class="alert-error">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.drivers.update', $driver->driver_id) }}" method="POST" enctype="multipart/form-data" class="driver-form">
      @csrf
      @method('PUT')

      <div class="form-grid">
        {{-- Nama --}}
        <div class="form-group">
          <label for="nama">Nama Lengkap</label>
          <input type="text" name="nama" id="nama" value="{{ old('nama', $driver->nama) }}" required>
        </div>

        {{-- Nomor HP --}}
        <div class="form-group">
          <label for="no_hp">Nomor HP</label>
          <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $driver->no_hp) }}">
        </div>

        {{-- Email --}}
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="{{ old('email', $driver->email) }}">
        </div>

        {{-- Foto --}}
        <div class="form-group">
          <label for="foto">Foto Driver</label>
          @if ($driver->foto)
            <div class="driver-foto-preview">
              <img src="{{ asset('storage/' . $driver->foto) }}" alt="Foto {{ $driver->nama }}">
            </div>
          @endif
          <input type="file" name="foto" id="foto" accept="image/*">
        </div>

        {{-- Nomor SIM --}}
        <div class="form-group">
          <label for="sim_number">Nomor SIM</label>
          <input type="text" name="sim_number" id="sim_number" value="{{ old('sim_number', $driver->sim_number) }}">
        </div>

        {{-- Harga --}}
        <div class="form-group">
          <label for="harga_per_hari">Harga Sewa / Hari</label>
          <input type="number" name="harga_per_hari" id="harga_per_hari" value="{{ old('harga_per_hari', $driver->harga_per_hari) }}" required>
        </div>

        {{-- Pengalaman --}}
        <div class="form-group">
          <label for="pengalaman">Pengalaman Mengemudi</label>
          <input type="text" name="pengalaman" id="pengalaman" value="{{ old('pengalaman', $driver->pengalaman) }}">
        </div>

        {{-- Lokasi --}}
        <div class="form-group">
          <label for="lokasi">Lokasi</label>
          <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $driver->lokasi) }}">
        </div>

        {{-- Deskripsi --}}
        <div class="form-group full">
          <label for="deskripsi">Deskripsi</label>
          <textarea name="deskripsi" id="deskripsi" rows="4">{{ old('deskripsi', $driver->deskripsi) }}</textarea>
        </div>

        {{-- Status --}}
        <div class="form-group">
          <label for="status">Status Driver</label>
          <select name="status" id="status">
            <option value="aktif" {{ old('status', $driver->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $driver->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>

        {{-- Verifikasi --}}
        <div class="form-group">
          <label for="status_verifikasi">Status Verifikasi</label>
          <select name="status_verifikasi" id="status_verifikasi">
            <option value="disetujui" {{ old('status_verifikasi', $driver->status_verifikasi) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="menunggu" {{ old('status_verifikasi', $driver->status_verifikasi) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
            <option value="ditolak" {{ old('status_verifikasi', $driver->status_verifikasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
        <a href="{{ route('admin.drivers.index') }}" class="btn-cancel">← Kembali</a>
      </div>
<<<<<<< HEAD
    </form>
  </div>
=======

      {{-- Email --}}
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $driver->email) }}">
      </div>

      {{-- Foto --}}
      <div class="form-group">
        <label for="foto">Foto Driver</label>
        @if ($driver->foto)
          <div style="margin-bottom:8px;">
            <img src="{{ asset('storage/' . $driver->foto) }}" alt="Foto {{ $driver->nama }}" width="100" style="border-radius:8px;object-fit:cover;">
          </div>
        @endif
        <input type="file" name="foto" id="foto" accept="image/*">
      </div>

      {{-- Nomor SIM --}}
      <div class="form-group">
        <label for="sim_number">Nomor SIM</label>
        <input type="text" name="sim_number" id="sim_number" value="{{ old('sim_number', $driver->sim_number) }}">
      </div>

      {{-- Harga per jam --}}
      <div class="form-group">
        <label for="harga_per_jam">Tarif / Jam (Rp)</label>
        <input 
          type="number" 
          name="harga_per_jam" 
          id="harga_per_jam" 
          value="{{ old('harga_per_jam', $driver->harga_per_jam) }}" 
          placeholder="Masukkan tarif per jam, misal 25000"
          required>
      </div>

      {{-- Pengalaman --}}
      <div class="form-group">
        <label for="pengalaman">Pengalaman Mengemudi</label>
        <input type="text" name="pengalaman" id="pengalaman" value="{{ old('pengalaman', $driver->pengalaman) }}">
      </div>

      {{-- Lokasi --}}
      <div class="form-group">
        <label for="lokasi">Lokasi</label>
        <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $driver->lokasi) }}">
      </div>

      {{-- Deskripsi --}}
      <div class="form-group full">
        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" rows="4">{{ old('deskripsi', $driver->deskripsi) }}</textarea>
      </div>

      {{-- Status --}}
      <div class="form-group">
        <label for="status">Status Driver</label>
        <select name="status" id="status">
          <option value="aktif" {{ old('status', $driver->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
          <option value="nonaktif" {{ old('status', $driver->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      {{-- Verifikasi --}}
      <div class="form-group">
        <label for="status_verifikasi">Status Verifikasi</label>
        <select name="status_verifikasi" id="status_verifikasi">
          <option value="disetujui" {{ old('status_verifikasi', $driver->status_verifikasi) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
          <option value="menunggu" {{ old('status_verifikasi', $driver->status_verifikasi) == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
          <option value="ditolak" {{ old('status_verifikasi', $driver->status_verifikasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
      <a href="{{ route('admin.drivers.index') }}" class="btn-cancel">← Kembali</a>
    </div>
  </form>
>>>>>>> 2709c9b41fb578e552895bbfe01de383b7ed3013
</div>

<style>
/* === WRAPPER & HEADER === */
.admin-content-wrapper {
  padding: 10px 30px 50px 30px;
  max-width: 1100px;
  margin: 0 auto;
}
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding: 18px 24px;
  border-radius: 14px;
  background: #181c26;
  box-shadow: 0 2px 12px rgba(0,0,0,0.3);
}
body.light-mode .page-header {
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.page-header h2 {
  font-size: 24px;
  font-weight: 700;
  color: #f5f7fa;
}
body.light-mode .page-header h2 { color: #222; }

/* === BUTTON KEMBALI DI HEADER === */
.btn-back {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
.btn-back:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

/* === FORM CARD === */
.driver-form-card {
  background: #141821;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.35);
}
body.light-mode .driver-form-card {
  background: #ffffff;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/* === FORM GRID === */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 18px;
}
.form-group {
  display: flex;
  flex-direction: column;
}
.form-group.full { grid-column: 1 / -1; }

/* === INPUT === */
label {
  font-weight: 600;
  margin-bottom: 6px;
  color: #9fc8ff;
}
body.light-mode label { color: #333; }

input, textarea, select {
  padding: 9px 12px;
  border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.1);
  background: #1e2430;
  color: #e1e5ec;
  font-size: 15px;
  transition: 0.25s ease;
}
input:focus, textarea:focus, select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59,130,246,0.25);
}
body.light-mode input,
body.light-mode textarea,
body.light-mode select {
  background: #f9fafb;
  color: #222;
  border: 1px solid #ccc;
}

/* === FOTO PREVIEW === */
.driver-foto-preview img {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  object-fit: cover;
  border: 2px solid rgba(255,255,255,0.15);
  margin-bottom: 6px;
}
body.light-mode .driver-foto-preview img { border-color: #e5e5e5; }

/* === TOMBOL FORM === */
.btn-save {
  background: linear-gradient(135deg, #0d6efd, #2563eb);
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
.btn-save:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

body:not(.light-mode) .btn-cancel {
  background: rgba(255,255,255,0.1);
  color: #f5f5f5;
}
body:not(.light-mode) .btn-cancel:hover {
  background: rgba(255,255,255,0.2);
}

/* === FORM ACTIONS === */
.form-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 25px;
  flex-wrap: wrap;
}

/* === ALERT ERROR === */
.alert-error {
  background: rgba(239,68,68,0.15);
  color: #ef4444;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
  border: 1px solid rgba(239,68,68,0.25);
}
.alert-error ul { margin: 0; padding-left: 20px; }
</style>
@endsection
