@extends('layouts.admin.app')

@section('title', 'Tambah Nomor Darurat')

@section('content')
<div class="container mt-4">
  <h3 class="fw-bold mb-3">Tambah Nomor Darurat</h3>

  <form action="{{ route('admin.emergency.store') }}" method="POST">
    @csrf
    <div class="mb-3">
  <label class="form-label">Nama Kontak</label>
  <input type="text" name="nama" class="form-control" placeholder="Contoh: Kantor HexaRent" required>
</div>

    <div class="mb-3">
      <label class="form-label">Keperluan</label>
      <input type="text" name="keperluan" class="form-control" placeholder="Contoh: Bantuan Jalan / Kantor Pusat" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nomor Telepon</label>
      <input type="text" name="nomor" class="form-control" placeholder="Contoh: 0812xxxxxxx" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Keterangan</label>
      <textarea name="keterangan" rows="3" class="form-control" placeholder="Contoh: Hubungi untuk bantuan derek atau mogok di jalan."></textarea>
    </div>

    <div class="mb-3">
  <label class="form-label">Pilih Icon</label>
  <select name="icon" class="form-select">
    <option value="">— Pilih Icon —</option>
    <option value="📞">📞 Telepon</option>
    <option value="🚓">🚓 Polisi / Keamanan</option>
    <option value="🚑">🚑 Ambulans</option>
    <option value="🚒">🚒 Pemadam</option>
    <option value="🛠️">🛠️ Bengkel</option>
    <option value="🧰">🧰 Layanan Perbaikan</option>
    <option value="🛞">🛞 Derek Mobil</option>
    <option value="💡">💡 Listrik / Darurat Teknis</option>
    <option value="📍">📍 Kantor / Lokasi</option>
  </select>
</div>


    <button type="submit" class="btn btn-dark w-100">Simpan</button>
  </form>
</div>
@endsection
