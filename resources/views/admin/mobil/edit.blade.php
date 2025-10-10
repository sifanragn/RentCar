<h2>Edit Data Mobil</h2>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('cars.update', $car->car_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Merek Mobil --}}
    <label>Merek Mobil:</label>
    <select id="brand" name="brand_id" onchange="loadModels()" required>
        <option value="">-- Pilih Merek --</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}" 
                {{ old('brand_id', $car->brand_id) == $brand->brand_id ? 'selected' : '' }}>
                {{ $brand->nama_merek }}
            </option>
        @endforeach
    </select>
    <a href="{{ route('cars.brands') }}">+ Tambah Merek</a>
    <br><br>

    {{-- Model Mobil --}}
    <label>Model Mobil:</label>
    <select id="model" name="model" required>
        <option value="">-- Pilih Model --</option>
        @foreach($car->brand->models ?? [] as $m)
            <option value="{{ $m->nama_model }}" 
                {{ old('model', $car->model) == $m->nama_model ? 'selected' : '' }}>
                {{ $m->nama_model }}
            </option>
        @endforeach
    </select>
    <a href="{{ route('cars.models') }}">+ Tambah Model</a>
    <br><br>

    <label>Tahun:</label>
    <input type="number" name="tahun" value="{{ old('tahun', $car->tahun) }}" required><br><br>

    <label>Warna:</label>
    <input type="text" name="warna" value="{{ old('warna', $car->warna) }}" required><br><br>

    <label>Tipe Transmisi:</label>
    <select name="tipe_transmisi" required>
        <option value="manual" {{ old('tipe_transmisi', $car->tipe_transmisi) == 'manual' ? 'selected' : '' }}>Manual</option>
        <option value="otomatis" {{ old('tipe_transmisi', $car->tipe_transmisi) == 'otomatis' ? 'selected' : '' }}>Otomatis</option>
    </select><br><br>

    {{-- Kapasitas --}}
    <label>Kapasitas Orang:</label>
    <select name="capacity_id" required>
        <option value="">-- Pilih Kapasitas --</option>
        @foreach($capacities as $cap)
            <option value="{{ $cap->capacity_id }}" 
                {{ old('capacity_id', $car->capacity_id) == $cap->capacity_id ? 'selected' : '' }}>
                {{ $cap->jumlah_orang }} Orang
            </option>
        @endforeach
    </select><br><br>

    <label>Bahan Bakar:</label>
    <select name="bahan_bakar" required>
        <option value="bensin" {{ old('bahan_bakar', $car->bahan_bakar) == 'bensin' ? 'selected' : '' }}>Bensin</option>
        <option value="diesel" {{ old('bahan_bakar', $car->bahan_bakar) == 'diesel' ? 'selected' : '' }}>Diesel</option>
        <option value="hybrid" {{ old('bahan_bakar', $car->bahan_bakar) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
    </select><br><br>

    <label>Harga Sewa per Hari (Rp):</label>
    <input type="number" step="0.01" name="harga_sewa_per_hari" 
           value="{{ old('harga_sewa_per_hari', $car->harga_sewa_per_hari) }}" required><br><br>

    <label>Lokasi:</label>
    <input type="text" name="lokasi" value="{{ old('lokasi', $car->lokasi) }}" required><br><br>

    <label>Kilometer:</label>
    <input type="number" name="kilometer" value="{{ old('kilometer', $car->kilometer) }}" required><br><br>

    <label>Kapasitas Tangki (Liter):</label>
    <input type="number" name="liter_tangki" value="{{ old('liter_tangki', $car->liter_tangki) }}" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi">{{ old('deskripsi', $car->deskripsi) }}</textarea><br><br>

    {{-- Foto Mobil --}}
    <label>Foto Saat Ini:</label><br>
    @if($car->foto)
        <img src="{{ asset('storage/' . $car->foto) }}" width="120"><br>
    @else
        <small>Tidak ada foto</small><br>
    @endif
    <label>Ganti Foto (opsional):</label>
    <input type="file" name="foto"><br><br>

    <button type="submit">Perbarui</button>
</form>

<a href="{{ route('cars.index') }}">Kembali</a>

{{-- Script untuk model dinamis --}}
<script>
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
            data.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.nama_model;
                opt.textContent = m.nama_model;
                modelSelect.appendChild(opt);
            });
        })
        .catch(err => {
            console.error(err);
            modelSelect.innerHTML = '<option value="">Gagal memuat model</option>';
        });
}
</script>
