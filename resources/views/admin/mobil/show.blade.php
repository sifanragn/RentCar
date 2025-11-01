<h2>Detail Mobil</h2>

{{-- Debug sementara --}}
{{-- <pre>{{ print_r($car->photos->pluck('path')->toArray(), true) }}</pre> --}}

<table border="0" cellpadding="6">
    <tr><td><strong>Merek:</strong></td><td>{{ $car->brand->nama_merek ?? '-' }}</td></tr>
    <tr><td><strong>Model:</strong></td><td>{{ $car->model }}</td></tr>
    <tr><td><strong>Tahun:</strong></td><td>{{ $car->tahun }}</td></tr>
    <tr><td><strong>Warna:</strong></td><td>{{ $car->warna }}</td></tr>
    <tr><td><strong>Transmisi:</strong></td><td>{{ ucfirst($car->tipe_transmisi) }}</td></tr>
    <tr><td><strong>Kapasitas:</strong></td><td>{{ $car->capacity->jumlah_orang ?? '-' }} Orang</td></tr>
    <tr><td><strong>Bahan Bakar:</strong></td><td>{{ ucfirst($car->bahan_bakar) }}</td></tr>
    <tr>
    <td><strong>Harga Sewa per Jam:</strong></td>
    <td>Rp{{ number_format($car->harga_sewa_per_jam, 0, ',', '.') }}</td>
    </tr>
    <tr><td><strong>Status:</strong></td><td>{{ ucfirst($car->status) }}</td></tr>
    <tr><td><strong>Lokasi:</strong></td><td>{{ $car->lokasi }}</td></tr>
    <tr><td><strong>Kilometer:</strong></td><td>{{ $car->kilometer }}</td></tr>
    <tr><td><strong>Kapasitas Tangki:</strong></td><td>{{ $car->liter_tangki }} Liter</td></tr>
    <tr><td><strong>Deskripsi:</strong></td><td>{{ $car->deskripsi ?? '-' }}</td></tr>

    <tr>
        <td><strong>Foto Utama:</strong></td>
        <td>
            @if($car->foto)
                <img src="{{ asset('storage/' . $car->foto) }}" width="200" style="border-radius:10px;">
            @else
                <small>Tidak ada foto</small>
            @endif
        </td>
    </tr>

    <tr>
        <td><strong>Foto Tambahan:</strong></td>
        <td>
            @if($car->photos->count() > 0)
                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                    @foreach($car->photos as $photo)
                        <img src="{{ asset('storage/' . $photo->path) }}" 
                             width="120" 
                             style="border-radius:8px; object-fit:cover;">
                    @endforeach
                </div>
            @else
                <small>Tidak ada foto tambahan</small>
            @endif
        </td>
    </tr>
</table>

<br>
<a href="{{ route('admin.cars.index') }}" 
   style="background:#555; color:#fff; padding:8px 14px; text-decoration:none; border-radius:6px;">
   ← Kembali ke Daftar Mobil
</a>
