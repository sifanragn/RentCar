@extends('layouts.admin.app')

@section('title', 'Edit Driver')

@section('content')
<div class="driver-form-container">
  <h2>Edit Data Driver</h2>

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

      {{-- Harga per hari --}}
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
  </form>
</div>

<style>
.driver-form-container {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  max-width: 850px;
  margin: 25px auto;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.driver-form-container h2 {
  color: #333;
  margin-bottom: 20px;
  text-align: center;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full {
  grid-column: 1 / -1;
}

label {
  font-weight: 600;
  margin-bottom: 5px;
  color: #333;
}

input, textarea, select {
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
  font-family: inherit;
}

textarea {
  resize: none;
}

.btn-save {
  background: #0d6efd;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
}

.btn-save:hover {
  background: #0b5ed7;
}

.btn-cancel {
  background: #f3f4f6;
  color: #333;
  padding: 10px 14px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}
</style>
@endsection
