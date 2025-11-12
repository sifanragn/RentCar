@extends('layouts.admin.app')

@section('title', 'Edit Nomor Darurat')

@section('content')
<div class="container mt-4">
  <h3 class="fw-bold mb-3">Edit Nomor Darurat</h3>

  <form action="{{ route('admin.emergency.update', $number->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label class="form-label">Nama Kontak</label>
      <input 
        type="text" 
        name="nama" 
        class="form-control" 
        value="{{ old('nama', $number->nama) }}" 
        placeholder="Contoh: Kantor HexaRent" 
        required>
    </div>

    <div class="mb-3">
      <label class="form-label">Keperluan</label>
      <input 
        type="text" 
        name="keperluan" 
        class="form-control" 
        value="{{ old('keperluan', $number->keperluan) }}" 
        placeholder="Contoh: Bantuan Jalan / Kantor Pusat" 
        required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nomor Telepon</label>
      <input 
        type="text" 
        name="nomor" 
        class="form-control" 
        value="{{ old('nomor', $number->nomor) }}" 
        placeholder="Contoh: 0812xxxxxxx" 
        required>
    </div>

    <div class="mb-3">
      <label class="form-label">Keterangan</label>
      <textarea 
        name="keterangan" 
        rows="3" 
        class="form-control" 
        placeholder="Contoh: Hubungi untuk bantuan derek atau mogok di jalan.">{{ old('keterangan', $number->keterangan) }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Pilih Icon</label>
      <select name="icon" class="form-select">
        <option value="">— Pilih Icon —</option>
        <option value="📞" {{ $number->icon === '📞' ? 'selected' : '' }}>📞 Telepon</option>
        <option value="🚓" {{ $number->icon === '🚓' ? 'selected' : '' }}>🚓 Polisi / Keamanan</option>
        <option value="🚑" {{ $number->icon === '🚑' ? 'selected' : '' }}>🚑 Ambulans</option>
        <option value="🚒" {{ $number->icon === '🚒' ? 'selected' : '' }}>🚒 Pemadam</option>
        <option value="🛠️" {{ $number->icon === '🛠️' ? 'selected' : '' }}>🛠️ Bengkel</option>
        <option value="🧰" {{ $number->icon === '🧰' ? 'selected' : '' }}>🧰 Layanan Perbaikan</option>
        <option value="🛞" {{ $number->icon === '🛞' ? 'selected' : '' }}>🛞 Derek Mobil</option>
        <option value="💡" {{ $number->icon === '💡' ? 'selected' : '' }}>💡 Listrik / Teknis</option>
        <option value="📍" {{ $number->icon === '📍' ? 'selected' : '' }}>📍 Kantor / Lokasi</option>
      </select>
    </div>

    <button type="submit" class="btn btn-dark w-100">Perbarui</button>
  </form>
</div>
@endsection
