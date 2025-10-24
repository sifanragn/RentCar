@extends('layouts.admin.app')

@section('title', 'Tambah Mobil Baru')

@section('content')

<h2 class="form-title">Tambah Mobil Baru</h2>

@if ($errors->any())
  <div class="alert-error">
      <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
@endif

<form action="{{ route('admin.cars.store') }}" method="POST" enctype="multipart/form-data" class="car-form">
  @csrf

  <div class="form-grid">

    {{-- MEREK MOBIL --}}
    <div class="form-group">
      <label for="brand">Merek Mobil</label>
      <div class="input-row">
        <select id="brand" name="brand_id" required>
          <option value="">-- Pilih Merek --</option>
          @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}">
              {{ ucfirst($brand->nama_merek) }}
            </option>
          @endforeach
        </select>
        <a href="{{ route('admin.cars.brands') }}" class="link-add">+ Kelola Merek</a>
      </div>
    </div>

    {{-- MODEL MOBIL --}}
    <div class="form-group">
      <label for="model">Model Mobil</label>
      <div class="input-row">
        <select id="model" name="model" required>
          <option value="">-- Pilih Model --</option>
        </select>
        <a href="{{ route('admin.cars.models') }}" class="link-add">+ Tambah Model</a>
      </div>
    </div>

    <div class="form-group">
      <label>Tahun</label>
      <input type="number" name="tahun" value="{{ old('tahun') }}" required>
    </div>

    <div class="form-group">
      <label>Warna</label>
      <input type="text" name="warna" value="{{ old('warna') }}" required>
    </div>

    <div class="form-group">
      <label>Tipe Transmisi</label>
      <select name="tipe_transmisi" required>
        <option value="manual">Manual</option>
        <option value="otomatis">Otomatis</option>
      </select>
    </div>

    <div class="form-group">
      <label>Kapasitas Orang</label>
      <select name="capacity_id" required>
        <option value="">-- Pilih Kapasitas --</option>
        @foreach($capacities as $cap)
          <option value="{{ $cap->capacity_id }}">{{ $cap->jumlah_orang }} Orang</option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label>Bahan Bakar</label>
      <select name="bahan_bakar" required>
        <option value="bensin">Bensin</option>
        <option value="diesel">Diesel</option>
        <option value="hybrid">Hybrid</option>
      </select>
    </div>

    <div class="form-group">
      <label>Harga Sewa per Hari (Rp)</label>
      <input type="number" step="0.01" name="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari') }}" required>
    </div>

    <div class="form-group">
      <label>Lokasi</label>
      <input type="text" name="lokasi" value="{{ old('lokasi') }}" required>
    </div>

    <div class="form-group">
      <label>Kilometer</label>
      <input type="number" name="kilometer" value="{{ old('kilometer') }}" required>
    </div>

    <div class="form-group">
      <label>Kapasitas Tangki (Liter)</label>
      <input type="number" name="liter_tangki" value="{{ old('liter_tangki') }}" required>
    </div>

    <div class="form-group form-wide">
      <label>Deskripsi</label>
      <textarea name="deskripsi">{{ old('deskripsi') }}</textarea>
    </div>

    {{-- FOTO UTAMA --}}
    <div class="form-group">
      <label>Foto Utama Mobil</label>
      <div class="upload-box" id="mainUploadBox">
        <input type="file" name="foto" id="foto" accept="image/*" required hidden>
        <div class="upload-content" onclick="document.getElementById('foto').click()">
          <i class="bi bi-cloud-arrow-up"></i>
          <p>Tarik & lepaskan gambar ke sini<br><span>atau klik untuk memilih file</span></p>
          <small>Format: JPG, PNG — Maksimal 2 MB</small>
        </div>
      </div>
      <div id="preview-main" class="preview-container"></div>
    </div>

    {{-- FOTO TAMBAHAN --}}
    <div class="form-group form-wide">
      <label>Foto Tambahan Mobil</label>
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
    <button type="submit" class="btn-submit">Simpan</button>
    <a href="{{ route('admin.cars.index') }}" class="btn-cancel">Kembali</a>
  </div>
</form>

{{-- SCRIPT --}}
<script>
// ===========================
// 🔹 LOAD MODEL BERDASARKAN MEREK
// ===========================
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
          opt.value = m.id ?? m.model_id ?? m.nama_model;
          opt.textContent = m.nama_model;
          modelSelect.appendChild(opt);
        });
      }
    })
    .catch(err => {
      console.error(err);
      modelSelect.innerHTML = '<option value="">Gagal memuat model</option>';
    });
}

// ===========================
// 🔹 PREVIEW & DRAG-DROP UPLOAD
// ===========================
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

function setupUploadBox(boxId, inputId, previewId, multiple = false) {
  const box = document.getElementById(boxId);
  const input = document.getElementById(inputId);

  box.addEventListener('click', () => input.click());

  box.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.stopPropagation();
    box.classList.add('dragover');
  });

  box.addEventListener('dragleave', (e) => {
    e.preventDefault();
    e.stopPropagation();
    box.classList.remove('dragover');
  });

  box.addEventListener('drop', (e) => {
    e.preventDefault();
    e.stopPropagation();
    box.classList.remove('dragover');

    const files = e.dataTransfer.files;
    if (!files.length) return;

    const dt = new DataTransfer();
    if (multiple) {
      [...input.files, ...files].forEach(f => dt.items.add(f));
    } else {
      dt.items.add(files[0]);
    }
    input.files = dt.files;
    previewFiles(inputId, previewId);
  });

  input.addEventListener('change', () => previewFiles(inputId, previewId));
}

// ===========================
// 🔹 INIT
// ===========================
document.addEventListener('DOMContentLoaded', () => {
  setupUploadBox('mainUploadBox', 'foto', 'preview-main', false);
  setupUploadBox('galleryUploadBox', 'gallery', 'preview-container', true);

  // 🔥 event listener untuk dropdown merek
  document.getElementById('brand').addEventListener('change', loadModels);
});
</script>

@endsection
