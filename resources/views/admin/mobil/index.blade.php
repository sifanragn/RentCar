@extends('layouts.admin.app')

@section('title', 'Daftar Mobil')

@section('content')
<div class="cars-header">
  <h2>Daftar Mobil</h2>
  <div class="car-actions">
    <a href="{{ route('admin.cars.create') }}" class="btn btn-primary">+ Tambah Mobil Baru</a>
    <a href="{{ route('admin.cars.brands') }}" class="btn btn-success">+ Tambah Merek</a>
    <a href="{{ route('admin.cars.models') }}" class="btn btn-blue">+ Tambah Model</a>
  </div>
</div>

@if(session('success'))
  <div class="alert success">{{ session('success') }}</div>
@endif

<div class="cars-card">
  <div class="table-wrapper">
    <table class="car-table">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Merek</th>
          <th>Model</th>
          <th>Tahun</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($cars as $car)
        <tr>
          <td>
            @if($car->foto)
              <img src="{{ asset('storage/' . $car->foto) }}" alt="Foto Mobil" class="car-img">
            @else
              <small>-</small>
            @endif
          </td>
          <td>{{ $car->brand->nama_merek ?? '-' }}</td>
          <td>{{ $car->model }}</td>
          <td>{{ $car->tahun }}</td>
          <td class="car-actions-td">
            <button class="link detail" onclick='showCarDetail(@json($car))'>Detail</button>
            <a href="{{ route('admin.cars.edit', $car->car_id) }}" class="link edit">Edit</a>
            <form action="{{ route('admin.cars.destroy', $car->car_id) }}" method="POST" 
                  class="inline" onsubmit="return confirm('Yakin hapus mobil ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="link delete">Hapus</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="empty-text">Belum ada mobil terdaftar.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- === MODAL DETAIL === --}}
<div id="carDetailModal" class="modal">
  <div class="modal-content">
    {{-- FOTO HEADER & TITLE --}}
    <div class="modal-photo" id="carPhotoArea">
  <img id="carPhotoImg" src="/img/no-image.png" alt="Foto Mobil" class="car-detail-banner">
  <div class="modal-header">
    <h2><i class="bi bi-car-front-fill"></i> Detail Mobil</h2>
  </div>
</div>


    {{-- BODY --}}
    <div class="modal-body">
      <div id="carDetailBody" class="modal-body-inner">
        <p style="text-align:center;">Memuat data mobil...</p>
      </div>
    </div>
  </div>
</div>


@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ JS loaded sukses!");
});

function showCarDetail(car) {
  const modal = document.getElementById('carDetailModal');
  const body = document.getElementById('carDetailBody');
  const photo = document.getElementById('carPhotoArea');

  // Fallback foto utama
  const fotoUtama = car.foto ? `/storage/${car.foto}` : '/img/no-image.png';

  // === Header modal (tanpa redup dan tidak terpotong) ===
  photo.innerHTML = `
    <div class="main-photo-area">
      <img id="mainCarPhoto" src="${fotoUtama}" alt="Foto Mobil" 
           class="car-detail-banner" style="
             width: 100%;
             height: auto;
             object-fit: contain;
             background: transparent;
             display: block;
             margin: 0 auto;
           ">
      <button class="close" onclick="closeDetailModal()">&times;</button>
      <div class="modal-header">
        <h2><i class="bi bi-car-front-fill"></i> Detail Mobil</h2>
      </div>
    </div>
  `;

  // === Kumpulkan semua foto: utama + tambahan ===
  let allPhotos = [];
  if (car.foto) allPhotos.push({ path: car.foto });
  if (car.photos && car.photos.length > 0) {
    allPhotos = allPhotos.concat(car.photos);
  }

  // === Galeri foto ===
  let galleryHTML = '';
  if (allPhotos.length > 0) {
    galleryHTML = `
      <div class="photo-gallery">
        ${allPhotos.map((p, i) => `
          <img 
            src="/storage/${p.path}" 
            class="thumb-photo ${i === 0 ? 'active' : ''}" 
            onclick="changeMainPhoto('/storage/${p.path}', this)">
        `).join('')}
      </div>
    `;
  }

  // === Isi detail mobil ===
  const brand = car.brand?.nama_merek ?? '-';
  const capacity = car.capacity?.jumlah_orang ?? '-';
  const harga = new Intl.NumberFormat('id-ID').format(car.harga_sewa_per_hari);

  body.innerHTML = `
    ${galleryHTML}
    <div class="car-detail-grid">
      <div class="car-detail-item"><div class="car-detail-label">Merek</div><div class="car-detail-value">${brand}</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Model</div><div class="car-detail-value">${car.model}</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Tahun</div><div class="car-detail-value">${car.tahun}</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Warna</div><div class="car-detail-value">${car.warna}</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Transmisi</div><div class="car-detail-value">${car.tipe_transmisi}</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Kapasitas</div><div class="car-detail-value">${capacity} Orang</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Bahan Bakar</div><div class="car-detail-value">${car.bahan_bakar}</div></div>
      <div class="car-detail-item">
      <div class="car-detail-label">Harga Sewa</div>
      <div class="car-detail-value">
        Rp{{ number_format($car->harga_sewa_per_jam ?? 0, 0, ',', '.') }}
      </div>
    </div>
      <div class="car-detail-item"><div class="car-detail-label">Status</div><div class="car-detail-value">${car.status}</div></div>
      <div class="car-detail-item"><div class="car-detail-label">Lokasi</div><div class="car-detail-value">${car.lokasi}</div></div>
      <div class="car-detail-item" style="grid-column: 1 / -1">
        <div class="car-detail-label">Deskripsi</div>
        <div class="car-detail-value">${car.deskripsi ?? '-'}</div>
      </div>
    </div>
  `;

  modal.classList.add('active');
}


function changeMainPhoto(src, el) {
  document.getElementById('mainCarPhoto').src = src;
  document.querySelectorAll('.thumb-photo').forEach(img => img.classList.remove('active'));
  el.classList.add('active');
}

function closeDetailModal() {
  document.getElementById('carDetailModal').classList.remove('active');
}
</script>

@endpush
