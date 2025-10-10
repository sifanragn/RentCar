<h2>Kelola Merek Mobil</h2>

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<form action="{{ route('cars.brands.store') }}" method="POST">
    @csrf
    <label>Nama Merek Baru:</label>
    <input type="text" name="nama_merek" required>
    <button type="submit">Tambah</button>
</form>

<h3>Daftar Merek</h3>
<ul>
    @forelse($brands as $brand)
        <li>{{ $brand->nama_merek }}</li>
    @empty
        <li>Belum ada merek mobil.</li>
    @endforelse
</ul>

<a href="{{ route('cars.create') }}">← Kembali ke Tambah Mobil</a>
