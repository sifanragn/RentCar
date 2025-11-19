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

    {{-- ✅ PILIH MEREK MOBIL (CUSTOM DROPDOWN DENGAN LOGO) --}}
    <div class="form-group">
      <label for="brand_input">Pilih Merek</label>

      {{-- Hidden input untuk kirim brand_id ke backend --}}
      <input type="hidden" name="brand_id" id="brand_input" required>

      {{-- Custom dropdown merek --}}
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
      <a href="{{ route('admin.cars.brands') }}" class="link-add">+ Kelola Merek</a>
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

    {{-- TAHUN --}}
    <div class="form-group">
      <label>Tahun</label>
      <input type="number" name="tahun" value="{{ old('tahun') }}" required>
    </div>

    {{-- WARNA DENGAN COLOR PICKER --}}
    <div class="form-group">
      <label>Warna Mobil</label>
      <div class="color-input-wrapper">
        <input type="color" id="colorPicker" value="#445677">
        <input 
          type="text" 
          name="warna" 
          id="colorCode" 
          value="{{ old('warna', '#445677') }}" 
          maxlength="7"
          required
        >
        <div class="color-preview" id="colorPreview"></div>
      </div>
      <small class="hint-text">Pilih atau ketik warna (contoh: <code>#ff6600</code>)</small>
    </div>

    {{-- TRANSMISI --}}
    <div class="form-group">
      <label>Tipe Transmisi</label>
      <select name="tipe_transmisi" required>
        <option value="manual">Manual</option>
        <option value="otomatis">Otomatis</option>
      </select>
    </div>

    {{-- KAPASITAS ORANG --}}
    <div class="form-group">
      <label>Kapasitas Orang</label>
      <select name="capacity_id" required>
        <option value="">-- Pilih Kapasitas --</option>
        @foreach($capacities as $cap)
          <option value="{{ $cap->capacity_id }}">{{ $cap->jumlah_orang }} Orang</option>
        @endforeach
      </select>
    </div>

    {{-- BAHAN BAKAR --}}
    <div class="form-group">
      <label>Bahan Bakar</label>
      <select name="bahan_bakar" required>
        <option value="bensin">Bensin</option>
        <option value="diesel">Diesel</option>
        <option value="hybrid">Hybrid</option>
      </select>
    </div>

    {{-- HARGA --}}
    <div class="form-group">
  <label>Harga Sewa per Jam (Rp)</label>
  <input type="number" step="0.01" name="harga_sewa_per_jam" required>
  <small class="hint-text">Minimal sewa 6 jam.</small>
</div>

    {{-- LOKASI --}}
    <div class="form-group">
      <label>Lokasi</label>
      <input type="text" name="lokasi" value="{{ old('lokasi') }}" required>
    </div>

    {{-- KILOMETER --}}
    <div class="form-group">
      <label>Kilometer</label>
      <input type="number" name="kilometer" value="{{ old('kilometer') }}" required>
    </div>

    {{-- KAPASITAS TANGKI --}}
    <div class="form-group">
      <label for="liter_tangki">Kapasitas Tangki (Liter)</label>
      <input 
        type="number" 
        name="liter_tangki" 
        id="liter_tangki" 
        value="{{ old('liter_tangki') }}" 
        placeholder="Masukkan kapasitas tangki"
        required
      >
      <small class="hint-text" style="color:#666;">
        Otomatis terisi sesuai merek mobil, bisa disesuaikan manual.
      </small>
    </div>

    {{-- DESKRIPSI --}}
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

  {{-- ACTION BUTTONS --}}
  <div class="form-actions">
    <button type="submit" class="btn-submit">Simpan</button>
    <a href="{{ route('admin.cars.index') }}" class="btn-cancel">Kembali</a>
  </div>
</form>

{{-- ====================== SCRIPTS ====================== --}}

<script>
// 🔹 DRAG & DROP PREVIEW
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

    // ❗ INI YANG BENAR — JANGAN pakai innerHTML lagi
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

// 🔹 INIT UPLOAD
document.addEventListener('DOMContentLoaded', () => {
  setupUploadBox('mainUploadBox', 'foto', 'preview-main', false);
  setupUploadBox('galleryUploadBox', 'gallery', 'preview-container', true);
});
</script>

<script>
// 🔹 CUSTOM DROPDOWN BRAND + AUTO ISI MODEL & KAPASITAS TANGKI
document.addEventListener("DOMContentLoaded", () => {
  const brandDropdown = document.getElementById("brandDropdown");
  const selectedBrand = document.getElementById("selectedBrand");
  const brandOptions = document.getElementById("brandOptions");
  const brandInput = document.getElementById("brand_input");
  const modelSelect = document.getElementById("model");
  const tankInput = document.getElementById("liter_tangki");

  const defaultTankCapacities = {
    "Toyota": 45, "Honda": 42, "Mitsubishi": 60, "Daihatsu": 40,
    "Suzuki": 43, "Nissan": 55, "Hyundai": 50, "Kia": 48,
    "Wuling": 52, "Mazda": 47, "BMW": 65, "Mercedes": 70,
    "Porsche": 64, "Lamborghini": 85, "Ferrari": 78
  };

  selectedBrand.addEventListener("click", () => {
    brandOptions.classList.toggle("show");
  });

  brandOptions.querySelectorAll("li").forEach(option => {
    option.addEventListener("click", () => {
      const brandId = option.getAttribute("data-id");
      const brandName = option.querySelector("span").textContent.trim();
      const brandImg = option.querySelector("img")?.src;

      selectedBrand.innerHTML = `
        ${brandImg ? <img src="${brandImg}" style="width:22px;height:22px;object-fit:contain;margin-right:6px;vertical-align:middle;"> : ''}
        <span>${brandName}</span>
        <i class="bi bi-chevron-down"></i>
      `;
      brandInput.value = brandId;
      brandOptions.classList.remove("show");
      loadModelsByBrand(brandId);

      const kapasitas = defaultTankCapacities[brandName];
      if (kapasitas) {
        tankInput.value = kapasitas;
        tankInput.style.backgroundColor = "#e8f9e9";
        setTimeout(() => (tankInput.style.backgroundColor = ""), 800);
      } else {
        tankInput.value = "";
      }
    });
  });

  document.addEventListener("click", (e) => {
    if (!brandDropdown.contains(e.target)) brandOptions.classList.remove("show");
  });

  function loadModelsByBrand(brandId) {
    modelSelect.innerHTML = '<option value="">Memuat...</option>';
    fetch(/admin/api/models/${brandId})
      .then(res => res.json())
      .then(data => {
        modelSelect.innerHTML = '<option value="">-- Pilih Model --</option>';
        data.forEach(m => {
          const opt = document.createElement("option");
          opt.value = m.nama_model;
          opt.textContent = m.nama_model;
          modelSelect.appendChild(opt);
        });
      })
      .catch(() => {
        modelSelect.innerHTML = '<option value="">Gagal memuat model</option>';
      });
  }
});
</script>

<script>
// 🔹 COLOR PICKER
document.addEventListener("DOMContentLoaded", () => {
  const picker = document.getElementById("colorPicker");
  const codeInput = document.getElementById("colorCode");
  const preview = document.getElementById("colorPreview");

  const updatePreview = (color) => {
    preview.style.backgroundColor = color;
    codeInput.value = color;
  };

  picker.addEventListener("input", (e) => updatePreview(e.target.value));

  codeInput.addEventListener("input", (e) => {
    const val = e.target.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
      preview.style.backgroundColor = val;
      picker.value = val;
    }
  });

  updatePreview(codeInput.value);
});
</script>

@endsection