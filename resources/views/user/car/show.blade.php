<h2>Detail Mobil</h2>

@php
  $isUnavailable = \App\Models\Rental::where('car_id', $car->car_id)
      ->whereIn('status_rental', ['verifikasi_diperlukan', 'menunggu', 'berjalan'])
      ->exists();
@endphp

<div style="display:flex; gap:30px;">
    <div>
        @if($car->foto)
            <img src="{{ asset('storage/' . $car->foto) }}" alt="Mobil" width="350" style="border-radius:10px;">
        @else
            <img src="{{ asset('img/no-image.png') }}" width="350">
        @endif
        @if($isUnavailable)
          <p style="margin-top:10px;color:#dc3545;font-weight:bold;">🚫 Mobil ini sedang disewa / menunggu konfirmasi</p>
        @endif
    </div>

    <div>
        <h3>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
        <p><strong>Tahun:</strong> {{ $car->tahun }}</p>
        <p><strong>Warna:</strong> {{ $car->warna }}</p>
        <p><strong>Transmisi:</strong> {{ ucfirst($car->tipe_transmisi) }}</p>
        <p><strong>Kapasitas:</strong> {{ $car->capacity->jumlah_orang ?? '-' }} Orang</p>
        <p><strong>Bahan Bakar:</strong> {{ ucfirst($car->bahan_bakar) }}</p>
        <p><strong>Harga:</strong> Rp{{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }} / Hari</p>
        <p><strong>Lokasi:</strong> {{ $car->lokasi }}</p>
        <p><strong>Deskripsi:</strong><br>{{ $car->deskripsi ?? '-' }}</p>

        <div style="margin-top:15px;">
            <a href="{{ route('user.cars.index') }}" 
                style="display:inline-block;background:#555;color:#fff;padding:6px 10px;border-radius:5px;text-decoration:none;">
                ← Kembali ke daftar
            </a>

            @if(!$isUnavailable)
              <a href="{{ url('user/rentals/create/' . $car->car_id) }}" 
                style="display:inline-block;background:#0d6efd;color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none;margin-left:10px;">
                🚗 Sewa Sekarang
              </a>
            @else
              <button disabled style="background:#6c757d;color:#fff;padding:8px 14px;border:none;border-radius:6px;margin-left:10px;">
                Tidak Tersedia
              </button>
            @endif
        </div>
    </div>
</div>
