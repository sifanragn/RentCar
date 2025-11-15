@extends('layouts.admin.app')

@section('title', 'Edit Nomor Darurat')

{{-- Tambah CSS --}}
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/emergency-edit.css?v=' . time()) }}">
@endsection

@section('content')
<div class="container mt-4">

  <h3 class="form-title">✏️ Edit Nomor Darurat</h3>

  <form action="{{ route('admin.emergency.update', $number->id) }}" method="POST" class="car-form">
    @csrf
    @method('PUT')

    <div class="form-grid">

      <div class="form-group">
        <label>Nama Kontak</label>
        <input 
          type="text" 
          name="nama" 
          value="{{ old('nama', $number->nama) }}" 
          placeholder="Contoh: Kantor HexaRent" 
          required>
      </div>

      <div class="form-group">
        <label>Keperluan</label>
        <input 
          type="text" 
          name="keperluan" 
          value="{{ old('keperluan', $number->keperluan) }}" 
          placeholder="Contoh: Bantuan Jalan / Kantor Pusat" 
          required>
      </div>

      <div class="form-group">
        <label>Nomor Telepon</label>
        <input 
          type="text" 
          name="nomor" 
          value="{{ old('nomor', $number->nomor) }}" 
          placeholder="Contoh: 0812xxxxxxx" 
          required>
      </div>

      <div class="form-group">
        <label>Pilih Icon</label>
        <select name="icon">
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

      <div class="form-group" style="grid-column: span 2;">
        <label>Keterangan</label>
        <textarea 
          name="keterangan" 
          rows="3" 
          placeholder="Contoh: Hubungi untuk bantuan derek atau mogok di jalan.">{{ old('keterangan', $number->keterangan) }}</textarea>
      </div>

    </div>

    <div class="form-actions">
      <a href="{{ route('admin.emergency.index') }}" class="btn-cancel">Batal</a>
      <button type="submit" class="btn-submit">Perbarui</button>
    </div>

  </form>
</div>
@endsection
