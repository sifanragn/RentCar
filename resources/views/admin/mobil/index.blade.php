<h2>Daftar Mobil</h2>

<div style="margin-bottom: 15px;">
    <a href="{{ route('cars.create') }}" 
       style="background: #2c3e50; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none;">
        + Tambah Mobil Baru
    </a>

    <a href="{{ route('cars.brands') }}" 
       style="background: #27ae60; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none;">
        + Tambah Merek Mobil
    </a>

    <a href="{{ route('cars.models') }}" 
       style="background: #2980b9; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none;">
        + Tambah Model Mobil
    </a>
</div>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead style="background:#f5f5f5;">
        <tr>
            <th>Merek</th>
            <th>Model</th>
            <th>Tahun</th>
            <th>Warna</th>
            <th>Transmisi</th>
            <th>Kapasitas</th>
            <th>Bahan Bakar</th>
            <th>Harga / Hari</th>
            <th>Status</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cars as $car)
        <tr>
            {{-- Ambil merek dari relasi --}}
            <td>{{ $car->brand->nama_merek ?? '-' }}</td>

            {{-- Model disimpan langsung di tabel cars --}}
            <td>{{ $car->model }}</td>

            <td>{{ $car->tahun }}</td>
            <td>{{ $car->warna }}</td>
            <td>{{ ucfirst($car->tipe_transmisi) }}</td>

            {{-- Kapasitas dari relasi car_capacities --}}
            <td>
                {{ $car->capacity && $car->capacity->jumlah_orang 
                    ? $car->capacity->jumlah_orang . ' Orang' 
                    : '-' }}
            </td>

            <td>{{ ucfirst($car->bahan_bakar) }}</td>
            <td>Rp{{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</td>
            <td>{{ ucfirst($car->status) }}</td>

            {{-- Foto mobil --}}
            <td>
                @if($car->foto)
                    <img src="{{ asset('storage/' . $car->foto) }}" alt="Foto Mobil" width="100" style="border-radius: 6px;">
                @else
                    <small>Tidak ada</small>
                @endif
            </td>

            {{-- Aksi edit, hapus, dan show --}}
            <td>
                <a href="{{ route('cars.show', $car->car_id) }}"
                   style="color: #16a085; text-decoration:none; margin-right:8px;">
                    Detail
                </a>

                <a href="{{ route('cars.edit', $car->car_id) }}"
                   style="color: blue; text-decoration:none; margin-right:8px;">
                    Edit
                </a>

                <form action="{{ route('cars.destroy', $car->car_id) }}" 
                      method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Yakin hapus mobil ini?')"
                            style="color:red; border:none; background:none; cursor:pointer;">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" style="text-align:center;">Belum ada mobil terdaftar.</td>
        </tr>
        @endforelse
    </tbody>
</table>
