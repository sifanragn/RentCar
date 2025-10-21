@extends('layouts.admin.app')

@section('title', 'Kelola Model Mobil')

@section('content')
<div class="admin-form-container">

  {{-- =====================================================
       FORM TAMBAH MODEL MOBIL
  ===================================================== --}}
  <div class="car-form">
    <h2 class="form-title">Kelola Model Mobil</h2>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
      <div class="alert-success">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    {{-- FORM TAMBAH MODEL --}}
    <form action="{{ route('cars.models.store') }}" method="POST" class="model-form">
      @csrf
      <div class="form-grid">

        {{-- ✅ CUSTOM DROPDOWN DENGAN LOGO --}}
        <div class="form-group">
          <label for="brand_input">Pilih Merek</label>

          {{-- Input hidden untuk kirim brand_id ke backend --}}
          <input type="hidden" name="brand_id" id="brand_input" required>

          {{-- Custom dropdown container --}}
          <div class="custom-select-brand" id="brandDropdown">
            <div class="selected-option" id="selectedBrand">
              <span class="placeholder">-- Pilih Merek --</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <ul class="options-list" id="brandOptions">
              @foreach($brands as $brand)
                <li data-id="{{ $brand->brand_id }}">
                  @if($brand->logo)
                    <img src="{{ asset('img/brand_logos/' . $brand->logo) }}" alt="{{ $brand->nama_merek }}">
                  @endif
                  <span>{{ $brand->nama_merek }}</span>
                </li>
              @endforeach
            </ul>
          </div>
        </div>

        {{-- INPUT NAMA MODEL --}}
        <div class="form-group">
          <label for="nama_model">Nama Model Baru</label>
          <input type="text" id="nama_model" name="nama_model" placeholder="Contoh: Avanza" required>
        </div>
      </div>

      {{-- TOMBOL --}}
      <div class="form-actions">
        <button type="submit" class="btn-submit">Tambah</button>
        <a href="{{ route('cars.create') }}" class="btn-cancel">Kembali</a>
      </div>
    </form>
  </div>

  {{-- =====================================================
       DAFTAR MODEL MOBIL
  ===================================================== --}}
  <div class="car-table-section" style="margin-top: 40px;">
    <h3 class="table-title">Daftar Model Mobil</h3>

    @if($models->count())
      <table class="model-table">
        <thead>
          <tr>
            <th>Merek</th>
            <th>Model</th>
          </tr>
        </thead>
        <tbody>
          @foreach($models as $model)
            <tr>
              <td class="brand-cell">
                @if($model->brand && $model->brand->logo)
                  <img src="{{ asset('img/brand_logos/' . $model->brand->logo) }}" 
                       alt="{{ $model->brand->nama_merek }}" 
                       class="brand-icon">
                @endif
                <span>{{ $model->brand->nama_merek ?? '-' }}</span>
              </td>
              <td>{{ $model->nama_model }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <p class="empty-text">Belum ada model mobil yang ditambahkan.</p>
    @endif
  </div>

</div>
@endsection
