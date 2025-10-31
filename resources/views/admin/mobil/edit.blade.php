@extends('layouts.admin.app')

@section('title', 'Edit Data Mobil')

@section('content')

<h2 class="form-title">Edit Data Mobil</h2>

@if ($errors->any())
  <div class="alert-error">
      <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
@endif

<form action="{{ route('admin.cars.update', $car->car_id) }}" method="POST" enctype="multipart/form-data" class="car-form">
  @csrf
  @method('PUT')

  <div class="form-grid">

    {{-- Merek Mobil --}}
    <div class="form-group">
      <label>Merek Mobil</label>
      <div class="input-row">
        <select id="brand" name="brand_id" onchange="loadModels()" required>
          <option value="">-- Pilih Merek --</option>
          @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}" {{ old('brand_id', $car->brand_id) == $brand->brand_id ? 'selected' : '' }}>
              {{ $brand->nama_merek }}
            </option>
          @endforeach
        </select>
        <a href="{{ route('admin.cars.brands') }}" class="link-add">+ Tambah Merek</a>
      </div>
    </div>

    {{-- Model Mobil --}}
    <div class="form-group">
      <label>Model Mobil</label>
      <div class="input-row">
        <select id="model" name="model" required>
          <option value="">-- Pilih Model --</option>
          @foreach($car->brand->models ?? [] as $m)
            <option value="{{ $m->nama_model }}" {{ old('model', $car->model) == $m->nama_model ? 'selected' : '' }}>
              {{ $m->nama_model }}
            </option>
          @endforeach
        </select>
        <a href="{{ route('admin.cars.models') }}" class="link-add">+ Tambah Model</a>
      </div>
    </div>

    {{-- Data Umum Mobil --}}
    <div class="form-group"><label>Tahun</label><input type="number" name="tahun" value="{{ old('tahun', $car->tahun) }}" required></div>
    <div class="form-group"><label>Warna</label><input type="text" name="warna" value="{{ old('warna', $car->warna) }}" required></div>

    <div class="form-group">
      <label>Tipe Transmisi</label>
      <select name="tipe_transmisi" required>
        <option value="manual" {{ old('tipe_transmisi', $car->tipe_transmisi) == 'manual' ? 'selected' : '' }}>Manual</option>
        <option value="otomatis" {{ old('tipe_transmisi', $car->tipe_transmisi) == 'otomatis' ? 'selected' : '' }}>Otomatis</option>
      </select>
    </div>

    <div class="form-group">
      <label>Kapasitas Orang</label>
      <select name="capacity_id" required>
        <option value="">-- Pilih Kapasitas --</option>
        @foreach($capacities as $cap)
          <option value="{{ $cap->capacity_id }}" {{ old('capacity_id', $car->capacity_id) == $cap->capacity_id ? 'selected' : '' }}>
            {{ $cap->jumlah_orang }} Orang
          </option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label>Bahan Bakar</label>
      <select name="bahan_bakar" required>
        <option value="bensin" {{ old('bahan_bakar', $car->bahan_bakar) == 'bensin' ? 'selected' : '' }}>Bensin</option>
        <option value="diesel" {{ old('bahan_bakar', $car->bahan_bakar) == 'diesel' ? 'selected' : '' }}>Diesel</option>
        <option value="hybrid" {{ old('bahan_bakar', $car->bahan_bakar) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
      </select>
    </div>

    {{-- 🔹 Harga Sewa per Jam --}}
    <div class="form-group">
      <label>Harga Sewa per Jam (Rp)</label>
      <input 
      type="number" 
      step="0.01" 
      name="harga_sewa_per_jam" 
      value="{{ old('harga_sewa_per_jam', $car->harga_sewa_per_jam) }}" 
      required>
    <small class="hint-text">Minimal sewa 6 jam.</small>
    </div>

    <div class="form-group"><label>Lokasi</label><input type="text" name="lokasi" value="{{ old('lokasi', $car->lokasi) }}" required></div>
    <div class="form-group"><label>Kilometer</label><input type="number" name="kilometer" value="{{ old('kilometer', $car->kilometer) }}" required></div>
    <div class="form-group"><label>Kapasitas Tangki (Liter)</label><input type="number" name="liter_tangki" value="{{ old('liter_tangki', $car->liter_tangki) }}" required></div>

    <div class="form-group form-wide">
      <label>Deskripsi</label>
      <textarea name="deskripsi">{{ old('deskripsi', $car->deskripsi) }}</textarea>
    </div>

    {{-- FOTO UTAMA --}}
    <div class="form-group">
      <label>Foto Utama Saat Ini</label>
      @if($car->foto)
        <div class="preview-container">
          <div class="preview-wrapper">
            <img src="{{ asset('storage/' . $car->foto) }}" class="preview-img" alt="Foto Utama">
          </div>
        </div>
      @else
        <small>Tidak ada foto utama.</small>
      @endif

      <label>Ganti Foto Utama</label>
      <div class="upload-box" id="mainUploadBox">
        <input type="file" name="foto" id="foto" accept="image/*" hidden>
        <div class="upload-content" onclick="document.getElementById('foto').click()">
          <i class="bi bi-cloud-arrow-up"></i>
          <p>Tarik & lepaskan gambar baru<br><span>atau klik untuk memilih file</span></p>
          <small>Format: JPG, PNG — Maksimal 2 MB</small>
        </div>
      </div>
      <div id="preview-main" class="preview-container"></div>
    </div>

    {{-- FOTO TAMBAHAN --}}
    <div class="form-group form-wide">
      <label>Foto Tambahan Saat Ini</label>
      @if($car->photos && count($car->photos))
        <div class="preview-container">
          @foreach($car->photos as $p)
            <div class="preview-wrapper">
              <img src="{{ asset('storage/' . $p->path) }}" class="preview-img" alt="Foto Tambahan">
            </div>
          @endforeach
        </div>
      @else
        <small>Tidak ada foto tambahan.</small>
      @endif

      <label>Tambah / Ganti Foto Tambahan</label>
      <div class="upload-box" id="galleryUploadBox">
        <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple hidden>
        <div class="upload-content" onclick="document.getElementById('gallery').click()">
          <i class="bi bi-images"></i>
          <p>Tarik & lepaskan beberapa gambar<br><span>atau klik untuk upload</span></p>
          <small>Format: JPG, PNG — Maksimal 2 MB per file</small>
        </div>
      </div>
      <div id="preview-container" class="preview-container"></div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn-submit">Perbarui</button>
    <a href="{{ route('admin.cars.index') }}" class="btn-cancel">Kembali</a>
  </div>
</form>

{{-- Script tetap sama --}}
<script>
 {{-- ===================== SCRIPT ===================== --}}
// === LOAD MODEL DARI BRAND ===
function loadModels() {
  const brandSelect = document.getElementById('brand');
  const modelSelect = document.getElementById('model');
  const brandId = brandSelect.value;
  modelSelect.innerHTML = '<option value="">Memuat...</option>';

  if (!brandId) {
    modelSelect.innerHTML = '<option value="">-- Pilih Model --</option>';
    return;
  }

  fetch(`/admin/api/models/${brandId}`)
    .then(res => res.json())
    .then(data => {
      modelSelect.innerHTML = '<option value="">-- Pilih Model --</option>';
      if (data.length === 0) {
        modelSelect.innerHTML = '<option value="">Tidak ada model tersedia</option>';
      } else {
        data.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m.nama_model;
          opt.textContent = m.nama_model;
          modelSelect.appendChild(opt);
        });
      }
    })
    .catch(() => {
      modelSelect.innerHTML = '<option value="">Gagal memuat model</option>';
    });
}

// === PREVIEW FOTO ===
function previewFiles(inputId, previewId) {
  const input = document.getElementById(inputId);
  const container = document.getElementById(previewId);
  container.innerHTML = '';

  [...input.files].forEach((file, index) => {
    const wrapper = document.createElement('div');
    wrapper.classList.add('preview-wrapper');

    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.classList.add('preview-img');

    const del = document.createElement('button');
    del.classList.add('delete-btn');
    del.innerHTML = '&times;';

    del.addEventListener('click', (e) => {
      e.stopPropagation();
      const dt = new DataTransfer();
      [...input.files].forEach((f, i) => {
        if (i !== index) dt.items.add(f);
      });
      input.files = dt.files;
      wrapper.remove();
    });

    wrapper.appendChild(img);
    wrapper.appendChild(del);
    container.appendChild(wrapper);
  });
}

// === DRAG & DROP ===
function setupUploadBox(boxId, inputId, previewId, multiple = false) {
  const box = document.getElementById(boxId);
  const input = document.getElementById(inputId);

  box.addEventListener('dragover', (e) => { e.preventDefault(); box.classList.add('dragover'); });
  box.addEventListener('dragleave', (e) => { e.preventDefault(); box.classList.remove('dragover'); });
  box.addEventListener('drop', (e) => {
    e.preventDefault();
    box.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (!files.length) return;
    const dt = new DataTransfer();
    if (multiple) [...input.files, ...files].forEach(f => dt.items.add(f));
    else dt.items.add(files[0]);
    input.files = dt.files;
    previewFiles(inputId, previewId);
  });
  input.addEventListener('change', () => previewFiles(inputId, previewId));
}

// === FULLSCREEN PREVIEW ===
const modal = document.createElement('div');
modal.id = 'imageModal';
modal.innerHTML = `
  <div class="image-modal-content">
    <span class="close-modal">&times;</span>
    <img id="modalImage" src="" alt="Preview">
  </div>`;
document.body.appendChild(modal);

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('preview-img')) {
    document.getElementById('modalImage').src = e.target.src;
    modal.classList.add('active');
  }
});
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('close-modal') || e.target.id === 'imageModal')
    modal.classList.remove('active');
});

// === INIT ===
document.addEventListener('DOMContentLoaded', () => {
  setupUploadBox('mainUploadBox', 'foto', 'preview-main', false);
  setupUploadBox('galleryUploadBox', 'gallery', 'preview-container', true);
});
</script>
@endsection
