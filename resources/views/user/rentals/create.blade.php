@extends('partials.container')

@section('title', 'Form Penyewaan')

@section('styles')
<style>
/* ===== Body & Wrapper ===== */
body { 
  font-family:'Segoe UI',Arial,sans-serif; 
  background:#f8f9fa; 
  margin:0; 
  padding:0; /* nempel ke atas */
}
.container-wrapper {
  padding:20px; /* kontrol jarak kiri-kanan */
}

/* ===== Card ===== */
.card { 
  background:white; 
  border-radius:10px; 
  padding:20px; 
  max-width:600px; 
  margin:20px auto 0 auto; /* margin-top kecil supaya nempel ke atas */
  box-shadow:0 2px 6px rgba(0,0,0,0.1); 
  margin-left: -25px;
  margin-right: -25px;
}
h2 { font-size: 20px; color:#333; margin-bottom:20px; }
label { display:block; margin-top:10px; font-weight:600; color:#333; }
input, select { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; margin-top:5px; }
button { background:#70D972; color:black; padding:10px 14px; border:none; border-radius:6px; margin-top:15px; cursor:pointer; transition:all .3s; }
button:hover { background:#5AC260; transform:scale(1.02); }

/* ===== Link Kembali ===== */
.back-link {
  color: #000; /* warna hitam */
  text-decoration: none; /* hilangkan garis bawah */
  font-weight: 250; /* biar sedikit tegas */
  font-size: 15px;
  margin-left: -20px;
    margin-top: 5px; /* jarak ke alert di atas */
  margin-bottom: 8px; /* jarak ke card di bawah */
}

.back-link:hover {
  text-decoration: underline; /* efek hover halus */
}</style>
@endsection

@section('content')
@include('partials.verification-alert')

<div class="container-wrapper">
  @php
    $user = auth()->user()->refresh();
    $hasDocuments = $user->foto_ktp && $user->foto_kk;
    $isVerified   = $user->status_verifikasi === 'disetujui';
    $redirectBack = url()->previous();
  @endphp

  {{-- Tombol kembali --}}
  <a href="{{ route('user.cars.show', $car->car_id) }}" class="back-link">← Kembali ke Detail Mobil</a>

  {{-- Card Form --}}
  <div class="card">
    <h2>Form Penyewaan Mobil</h2>

    {{-- Info mobil --}}
    <h3>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
    <p><strong>Harga per hari:</strong> Rp{{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</p>

    {{-- Form Penyewaan --}}
    <form id="rentalForm" action="{{ route('user.rentals.store', $car->car_id) }}" method="POST">
      @csrf
      <input type="hidden" name="redirect_back" value="{{ $redirectBack }}">

      <label for="tanggal_mulai">Tanggal Mulai</label>
      <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>

      <label for="tanggal_selesai">Tanggal Selesai</label>
      <input type="date" name="tanggal_selesai" id="tanggal_selesai" required>

      <label for="driver">Butuh Driver?</label>
      <select name="driver" id="driver" required>
        <option value="tidak">Tidak</option>
        <option value="ya">Ya (+Rp150.000/hari)</option>
      </select>

      <label for="metode_pickup">Metode Pengambilan</label>
      <select name="metode_pickup" id="metode_pickup" required>
        <option value="ambil_sendiri">Ambil Sendiri di Lokasi</option>
        <option value="pickup_alamat">Antar ke Alamat Saya</option>
      </select>

      <div class="price-box">
        <p><strong>Catatan:</strong> Total biaya akan dihitung otomatis setelah pembayaran.</p>
      </div>

      <button type="submit">Konfirmasi Sewa</button>
    </form>
  </div>
@include('partials.bottom-navbar')

<script>
const form  = document.getElementById('rentalForm');
const popup = document.getElementById('popup-menunggu');
const csrf  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const hasDocuments = {{ $hasDocuments ? 'true' : 'false' }};
const isVerified   = {{ $isVerified ? 'true' : 'false' }};

form.addEventListener('submit', function(e) {
  e.preventDefault();

  // Dokumen belum diupload
  if (!hasDocuments) {
    alert('Silakan unggah KTP & KK terlebih dahulu.');
    window.location.href = "{{ route('user.verifikasi.index') }}";
    return;
  }

  // Dokumen sudah diupload tapi belum diverifikasi
  if (!isVerified) {
    popup.style.display = 'flex';
    return;
  }

  // Submit form via fetch
  fetch(this.action, {
    method: 'POST',
    body: new FormData(this),
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrf
    },
    credentials: 'same-origin'
  })
  .then(res => res.json())
  .then(data => {
    if (data.success && data.redirect_url) {
      window.location.href = data.redirect_url;
    } else {
      alert(data.message || 'Terjadi kesalahan.');
    }
  })
  .catch(() => {
    alert('Terjadi kesalahan koneksi.');
  });
});

// Close popup
document.getElementById('close-popup').addEventListener('click', () => {
  popup.style.display = 'none';
});
</script>
@endsection
