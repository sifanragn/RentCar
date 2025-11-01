@extends('layouts.admin.app')

@section('title', 'Kelola Merek Mobil')

@section('content')
<div class="admin-form-container">
  <div class="car-form">
    <h2 class="form-title">Kelola Merek Mobil</h2>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
      <div class="alert-success">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    {{-- FORM TAMBAH MEREK --}}
    <form action="{{ route('admin.cars.brands.store') }}" method="POST" class="brand-form">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Nama Merek Baru</label>
          <input type="text" name="nama_merek" placeholder="Contoh: Toyota" required>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">Tambah</button>
        <a href="{{ route('admin.cars.create') }}" class="btn-cancel">Kembali</a>
      </div>
    </form>
  </div>

 {{-- DAFTAR MEREK --}}
<div class="car-form" style="margin-top: 30px;">
  <h3 class="form-title" style="font-size: 22px;">Daftar Merek</h3>

  @if($brands->count())
    <div class="brand-list">
      @foreach($brands as $brand)
        <div class="brand-item brand-{{ strtolower($brand->nama_merek) }}">
          <img 
            src="{{ asset('img/brand_logos/' . $brand->logo) }}" 
            alt="{{ $brand->nama_merek }}" 
            class="brand-logo"
          >
          <span>{{ ucfirst($brand->nama_merek) }}</span>

          {{-- Tombol hapus (muncul saat hover) --}}
          <form 
            action="{{ route('admin.cars.brands.destroy', $brand->brand_id) }}" 
            method="POST" 
            class="delete-brand-form"
            onsubmit="return confirm('Hapus merek {{ $brand->nama_merek }}?')"
          >
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-btn" title="Hapus">
              <i class="bi bi-trash-fill"></i>
            </button>
          </form>
        </div>
      @endforeach
    </div>
  @else
    <p class="empty-text">Belum ada merek mobil yang ditambahkan.</p>
  @endif
</div>
@endsection