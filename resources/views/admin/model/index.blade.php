<h2>Kelola Model Mobil</h2>

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<form action="{{ route('cars.models.store') }}" method="POST">
    @csrf
    <label>Pilih Merek:</label>
    <select name="brand_id" required>
        <option value="">-- Pilih Merek --</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->brand_id }}">{{ $brand->nama_merek }}</option>
        @endforeach
    </select>

    <label>Nama Model Baru:</label>
    <input type="text" name="nama_model" required>

    <button type="submit">Tambah</button>
</form>

<h3>Daftar Model Mobil</h3>
<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Merek</th>
            <th>Model</th>
        </tr>
    </thead>
    <tbody>
        @forelse($models as $model)
            <tr>
                <td>{{ $model->brand->nama_merek ?? '-' }}</td>
                <td>{{ $model->nama_model }}</td>
            </tr>
        @empty
            <tr><td colspan="2" style="text-align:center;">Belum ada model mobil.</td></tr>
        @endforelse
    </tbody>
</table>

<a href="{{ route('cars.create') }}">← Kembali ke Tambah Mobil</a>
