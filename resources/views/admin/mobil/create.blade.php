<h2>Tambah Mobil Baru</h2>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Merek Mobil --}}
    <label>Merek Mobil:</label>
    <select id="brand" name="brand_id" onchange="loadModels()" required>
        <option value="">-- Pilih Merek --</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}">{{ $brand->nama_merek }}</option>
        @endforeach
    </select>
    <a href="{{ route('cars.brands') }}">+ Tambah Merek</a>
    <br><br>

    {{-- Model Mobil --}}
    <label>Model Mobil:</label>
    <select id="model" name="model" required>
        <option value="">-- Pilih Model --</option>
    </select>
    <a href="{{ route('cars.models') }}">+ Tambah Model</a>
    <br><br>

    <label>Tahun:</label>
    <input type="number" name="tahun" value="{{ old('tahun') }}" required><br><br>

    <label>Warna:</label>
    <input type="text" name="warna" value="{{ old('warna') }}" required><br><br>

    <label>Tipe Transmisi:</label>
    <select name="tipe_transmisi" required>
        <option value="manual">Manual</option>
        <option value="otomatis">Otomatis</option>
    </select><br><br>

    <label>Kapasitas Orang:</label>
<select name="capacity_id" required>
    <option value="">-- Pilih Kapasitas --</option>
    @foreach($capacities as $cap)
        <option value="{{ $cap->capacity_id }}">{{ $cap->jumlah_orang }} Orang</option>
    @endforeach
</select><br><br>

    <label>Bahan Bakar:</label>
    <select name="bahan_bakar" required>
        <option value="bensin">Bensin</option>
        <option value="diesel">Diesel</option>
        <option value="hybrid">Hybrid</option>
    </select><br><br>

    <label>Harga Sewa per Hari (Rp):</label>
    <input type="number" step="0.01" name="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari') }}" required><br><br>

    <label>Lokasi:</label>
    <input type="text" name="lokasi" value="{{ old('lokasi') }}" required><br><br>

    <label>Kilometer:</label>
    <input type="number" name="kilometer" value="{{ old('kilometer') }}" required><br><br>

    <label>Kapasitas Tangki (Liter):</label>
    <input type="number" name="liter_tangki" value="{{ old('liter_tangki') }}" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi">{{ old('deskripsi') }}</textarea><br><br>

    <label>Foto Mobil:</label>
    <input type="file" name="foto" required><br><br>

    <button type="submit">Simpan</button>
</form>

<a href="{{ route('cars.index') }}">Kembali</a>

{{-- SCRIPT UNTUK LOAD MODEL DINAMIS --}}
<script>
function loadModels() {
    const brandSelect = document.getElementById('brand');
    const modelSelect = document.getElementById('model');
    const brandId = brandSelect.value; // PAKAI ID SEKARANG
    modelSelect.innerHTML = '<option value="">Memuat...</option>';

    if (!brandId) {
        modelSelect.innerHTML = '<option value="">-- Pilih Model --</option>';
        return;
    }

    // Fetch model berdasarkan ID merek
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
        .catch(err => {
            console.error(err);
            modelSelect.innerHTML = '<option value="">Gagal memuat model</option>';
        });
}
</script>
