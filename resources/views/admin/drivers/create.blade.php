@extends('layouts.admin.app')

@section('title', 'Tambah Driver Baru')

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>🚗 Tambah Driver Baru</h2>
    <p>Masukkan data lengkap driver yang akan digunakan untuk penyewaan mobil.</p>
  </div>

  @if ($errors->any())
    <div class="alert-error">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data" class="form-driver">
    @csrf

    <div class="form-grid">
      {{-- Nama Lengkap --}}
      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required>
      </div>

      {{-- Nomor HP --}}
      <div class="form-group">
        <label for="no_hp">Nomor HP</label>
        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
      </div>

      {{-- Email --}}
      <div class="form-group">
        <label for="email">Email (opsional)</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}">
      </div>

      {{-- Nomor SIM --}}
      <div class="form-group">
        <label for="sim_number">Nomor SIM</label>
        <input type="text" id="sim_number" name="sim_number" value="{{ old('sim_number') }}">
      </div>

      {{-- Harga per hari --}}
      <div class="form-group">
        <label for="harga_per_hari">Tarif / Hari (Rp)</label>
        <input type="number" id="harga_per_hari" name="harga_per_hari" value="{{ old('harga_per_hari', 150000) }}" min="0" required>
      </div>

      {{-- Lokasi --}}
      <div class="form-group">
        <label for="lokasi">Lokasi Driver</label>
        <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Bandung, Surabaya">
      </div>

      {{-- Pengalaman --}}
      <div class="form-group">
        <label for="pengalaman">Pengalaman</label>
        <input type="text" id="pengalaman" name="pengalaman" value="{{ old('pengalaman') }}" placeholder="Contoh: 5 tahun">
      </div>

      {{-- Status Verifikasi --}}
      <div class="form-group">
        <label for="status_verifikasi">Status Verifikasi</label>
        <select id="status_verifikasi" name="status_verifikasi">
          <option value="menunggu" {{ old('status_verifikasi')=='menunggu'?'selected':'' }}>Menunggu</option>
          <option value="disetujui" {{ old('status_verifikasi')=='disetujui'?'selected':'' }}>Disetujui</option>
          <option value="ditolak" {{ old('status_verifikasi')=='ditolak'?'selected':'' }}>Ditolak</option>
        </select>
      </div>

      {{-- Status --}}
      <div class="form-group">
        <label for="status">Status Aktif</label>
        <select id="status" name="status">
          <option value="aktif" {{ old('status')=='aktif'?'selected':'' }}>Aktif</option>
          <option value="nonaktif" {{ old('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
        </select>
      </div>

      {{-- Foto --}}
      <div class="form-group">
        <label for="foto">Foto Driver</label>
        <input type="file" id="foto" name="foto" accept="image/*" onchange="previewFoto(this)">
        <img id="previewFoto" src="#" alt="Preview" style="display:none;margin-top:10px;width:110px;height:110px;border-radius:10px;object-fit:cover;">
      </div>

      {{-- Deskripsi --}}
      <div class="form-group" style="grid-column:1 / span 2;">
        <label for="deskripsi">Deskripsi (opsional)</label>
        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Ramah, berpengalaman di rute luar kota.">{{ old('deskripsi') }}</textarea>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Simpan Driver</button>
      <a href="{{ route('admin.drivers.index') }}" class="btn-secondary">↩ Kembali</a>
    </div>
  </form>
</div>

<style>
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 18px;
}

.form-group label {
  font-weight: 600;
  color: #333;
  margin-bottom: 6px;
  display: block;
}

.form-group input, .form-group select, .form-group textarea {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  outline: none;
  transition: border-color .2s ease;
}

.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
  border-color: #0d6efd;
}

.form-actions {
  margin-top: 25px;
  display: flex;
  gap: 10px;
}

.btn-primary {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: .2s;
}

.btn-primary:hover { background: #0b5ed7; }

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 10px 16px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
}

.btn-secondary:hover { background: #5a6268; }

.alert-error {
  background: #ffe5e5;
  color: #b50000;
  border-left: 4px solid #dc3545;
  padding: 10px 15px;
  margin-bottom: 15px;
  border-radius: 8px;
}
</style>

<script>
function previewFoto(input) {
  const preview = document.getElementById('previewFoto');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    preview.style.display = 'none';
  }
}
</script>
@endsection
