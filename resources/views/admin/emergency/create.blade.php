@extends('layouts.admin.app')

@section('title', 'Tambah Nomor Darurat')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/emergency-create.css?v=' . time()) }}">
@endsection

@section('content')
<div class="container mt-4">

  <div class="emergency-form">

    <h3 class="emergency-title">Tambah Nomor Darurat</h3>

    <form action="{{ route('admin.emergency.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label>Nama Kontak</label>
        <input type="text" name="nama" placeholder="Contoh: Kantor HexaRent" required>
      </div>

      <div class="form-group">
        <label>Keperluan</label>
        <input type="text" name="keperluan" placeholder="Contoh: Bantuan Jalan / Kantor Pusat" required>
      </div>

      <div class="form-group">
        <label>Nomor Telepon</label>
        <input type="text" name="nomor" placeholder="Contoh: 0812xxxxxxx" required>
      </div>

      <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" rows="3" placeholder="Contoh: Hubungi untuk bantuan derek atau mogok di jalan."></textarea>
      </div>

      <div class="form-group">
        <label>Pilih Icon</label>
        <select name="icon">
          <option value="">— Pilih Icon —</option>
          <option value="📞">📞 Telepon</option>
          <option value="🚓">🚓 Polisi / Keamanan</option>
          <option value="🚑">🚑 Ambulans</option>
          <option value="🚒">🚒 Pemadam</option>
          <option value="🛠️">🛠️ Bengkel</option>
          <option value="🧰">🧰 Layanan Perbaikan</option>
          <option value="🛞">🛞 Derek Mobil</option>
          <option value="💡">💡 Listrik / Teknis</option>
          <option value="📍">📍 Kantor / Lokasi</option>
        </select>
      </div>

      <div class="form-actions">
    <button type="submit" class="btn-save">Simpan</button>
    <a href="{{ route('admin.emergency.index') }}" class="btn-back">
        Kembali
    </a>
</div>
    </form>

  </div>

</div>
@endsection
