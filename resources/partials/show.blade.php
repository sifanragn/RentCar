<h2>Detail Mobil</h2>

<table border="0" cellpadding="6">
  <tr><td><strong>Merek:</strong></td><td>{{ $car->brand->nama_merek ?? '-' }}</td></tr>
  <tr><td><strong>Model:</strong></td><td>{{ $car->model }}</td></tr>
  <tr><td><strong>Tahun:</strong></td><td>{{ $car->tahun }}</td></tr>
  <tr><td><strong>Warna:</strong></td><td>{{ $car->warna }}</td></tr>
  <tr><td><strong>Transmisi:</strong></td><td>{{ ucfirst($car->tipe_transmisi) }}</td></tr>
  <tr><td><strong>Kapasitas:</strong></td><td>{{ $car->capacity->jumlah_orang ?? '-' }} Orang</td></tr>
  <tr><td><strong>Bahan Bakar:</strong></td><td>{{ ucfirst($car->bahan_bakar) }}</td></tr>
  <tr><td><strong>Harga Sewa:</strong></td>
      <td>Rp{{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</td></tr>
  <tr><td><strong>Status:</strong></td><td>{{ ucfirst($car->status) }}</td></tr>
  <tr><td><strong>Lokasi:</strong></td><td>{{ $car->lokasi }}</td></tr>
  <tr><td><strong>Kilometer:</strong></td><td>{{ $car->kilometer }}</td></tr>
  <tr><td><strong>Kapasitas Tangki:</strong></td><td>{{ $car->liter_tangki }} L</td></tr>
  <tr><td><strong>Deskripsi:</strong></td><td>{{ $car->deskripsi ?? '-' }}</td></tr>
  <tr>
    <td><strong>Foto Mobil:</strong></td>
    <td>
      @if($car->foto)
        <img src="{{ asset('storage/' . $car->foto) }}" alt="Foto Mobil" width="200" style="border-radius:10px;">
      @else
        <small>Tidak ada foto</small>
      @endif
    </td>
  </tr>
</table>
