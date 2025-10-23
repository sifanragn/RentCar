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
      <div class="form-group full-width">
        <label for="deskripsi">Deskripsi (opsional)</label>
        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Ramah, berpengalaman di rute luar kota.">{{ old('deskripsi') }}</textarea>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Simpan Driver</button>
      <a href="{{ route('admin.drivers.index') }}" class="btn-cancel">↩ Kembali</a>
    </div>
  </form>
</div>

{{-- ========================= STYLE ========================= --}}
<style>
.admin-content-wrapper {
  padding: 10px 30px 50px 30px;
  max-width: 1100px;
  margin: 0 auto;
}

/* === HEADER === */
.page-header {
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
  color: #f5f7fa;
  font-size: 22px;
  font-weight: 700;
}
body.light-mode .page-header h2 { color: #222; }
.page-header p {
  color: #d4d8e3;
  margin-top: 6px;
}
body.light-mode .page-header p { color: #555; }

/* === FORM WRAPPER === */
.form-driver {
  background: #141821;
  border-radius: 16px;
  padding: 40px 36px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.35);
  transition: all 0.3s ease;
}
body.light-mode .form-driver {
  background: #ffffff;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

/* === GRID === */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 26px 32px; /* 🔹 Lebih lega */
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

/* === LABEL === */
label {
  font-weight: 600;
  color: #d4d8e3;
  font-size: 15px;
}
body.light-mode label { color: #333; }

/* === INPUTS === */
input, select, textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #444;
  border-radius: 8px;
  background: #1e2430;
  color: #e9ecef;
  font-size: 15px;
  transition: 0.2s;
}
body.light-mode input,
body.light-mode select,
body.light-mode textarea {
  background: #f9fafb;
  color: #222;
  border: 1px solid #ccc;
}

input:focus, select:focus, textarea:focus {
  border-color: #0d6efd;
  outline: none;
}

/* === BUTTON AREA === */
.form-actions {
  margin-top: 40px;
  display: flex;
  gap: 14px;
}

.btn-primary {
  background: #0d6efd;
  color: #fff;
  border: none;
  padding: 12px 22px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.25s ease;
}
.btn-primary:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

/* === KEMBALI BUTTON (gradien biru sama kayak detail) === */
.btn-cancel {
  background: linear-gradient(135deg, #0d6efd, #2563eb);
  color: #fff;
  padding: 12px 22px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.25s ease;
}
.btn-cancel:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

/* === ALERT === */
.alert-error {
  background: rgba(255, 99, 99, 0.15);
  color: #e74c3c;
  border-left: 4px solid #e74c3c;
  padding: 12px 15px;
  margin-bottom: 22px;
  border-radius: 8px;
}
body.light-mode .alert-error {
  background: #ffeaea;
  color: #b30000;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .form-driver {
    padding: 25px 20px;
  }
  .form-actions {
    flex-direction: column;
  }
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
