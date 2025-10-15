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
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Form Penyewaan Mobil</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 20px;
    }
    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    h2 { color: #333; margin-bottom: 20px; }
    label { display: block; margin-top: 10px; font-weight: 600; color: #333; }
    input, select, textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-top: 5px;
      font-family: inherit;
    }
    button {
      background: #0d6efd;
      color: white;
      padding: 10px 14px;
      border: none;
      border-radius: 6px;
      margin-top: 15px;
      cursor: pointer;
      transition: all .3s;
    }
    button:hover { background: #0b5ed7; transform: scale(1.02); }
    .alert {
      background: #fff3cd;
      color: #856404;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
    .price-box {
      background: #e9ecef;
      padding: 10px;
      border-radius: 6px;
      margin-top: 10px;
    }
    #popup-menunggu {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    #popup-menunggu .popup-content {
      background: white;
      border-radius: 20px;
      padding: 30px;
      width: 350px;
      text-align: center;
      position: relative;
      animation: fadeIn .4s ease;
    }
    #popup-menunggu button.close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      border: none;
      background: none;
      font-size: 20px;
      cursor: pointer;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>
</head>
<body>

<div class="card">
  <h2>Form Penyewaan Mobil</h2>

  {{-- ✅ Alert hanya muncul kalau belum verifikasi --}}
  @if(auth()->user()->status_verifikasi !== 'disetujui')
    <div class="alert">
      ⚠️ Penyewaan hanya bisa dilakukan setelah <b>admin memverifikasi KTP & KK Anda.</b><br>
      <a href="{{ route('user.profile') }}" style="color:#0d6efd;">Klik di sini untuk verifikasi sekarang</a>
    </div>
  @endif

  <h3>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
  <p><strong>Harga per hari:</strong> Rp{{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</p>

  <form id="rentalForm" action="{{ route('user.rentals.store', $car->car_id) }}" method="POST">
    @csrf

    {{-- 🔹 input tanggal + jam mulai --}}
    <label for="tanggal_mulai">Tanggal & Jam Mulai</label>
    <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" required>

    {{-- 🔹 input tanggal + jam selesai --}}
    <label for="tanggal_selesai">Tanggal & Jam Selesai</label>
    <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" required>

    <label for="driver">Butuh Driver?</label>
    <select name="driver" id="driver" required>
      <option value="tidak">Tidak</option>
      <option value="ya">Ya (+Rp150.000/hari)</option>
    </select>

    <label for="metode_pickup">Metode Pengambilan</label>
    <select name="metode_pickup" id="metode_pickup" required>
      <option value="">-- Pilih Metode --</option>
      <option value="ambil_sendiri">Ambil di Tempat</option>
      <option value="pickup_alamat">Antar ke Alamat Saya</option>
    </select>

    <!-- Lokasi rental -->
    <div id="lokasiRental" style="display:none; margin-top:10px;">
      <p>📍 Lokasi Rental Kami:</p>
      <p><strong>Jl. Melati No. 12, Bandung</strong></p>
      <a href="https://www.google.com/maps?q=-6.914744,107.609810" target="_blank"
         style="color:#0d6efd;">Lihat di Google Maps</a>
    </div>

    <!-- Input alamat user -->
    <div id="alamatUser" style="display:none; margin-top:10px;">
      <label for="alamat">Alamat Anda</label>
      <textarea id="alamat" name="alamat"
                placeholder="Masukkan alamat lengkap Anda..."
                rows="3"
                style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;"></textarea>
      <button type="button" id="cekOngkirBtn" style="margin-top:10px;">Cek Ongkir</button>
      <p id="hasilOngkir" style="margin-top:8px; color:#333;"></p>
    </div>

    <div class="price-box">
      <p><strong>Catatan:</strong> Total biaya akan dihitung otomatis setelah pembayaran.</p>
    </div>

    <button type="submit">Ajukan Penyewaan</button>
  </form>

  <a href="{{ route('user.cars.show', $car->car_id) }}"
     style="display:inline-block; margin-top:15px; color:#0d6efd; text-decoration:none;">← Kembali</a>
</div>

{{-- Popup jika belum verifikasi --}}
<div id="popup-menunggu">
  <div class="popup-content">
    <button class="close-btn" id="close-popup">✖</button>
    <div style="font-size:40px; animation:spin 1s linear infinite;">⏳</div>
    <img src="{{ asset('img/waiting-illustration.png') }}" alt="Menunggu"
         style="width:150px; margin:10px auto;">
    <h3>Belum Diverifikasi</h3>
    <p>Anda harus mengunggah KTP & KK terlebih dahulu sebelum melanjutkan penyewaan.</p>
    <a href="{{ route('user.profile') }}"
       style="display:inline-block;background:#0d6efd;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none;">Unggah Sekarang</a>
  </div>
</div>

<script>
  const form  = document.getElementById('rentalForm');
  const popup = document.getElementById('popup-menunggu');
  const csrf  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const isVerified = "{{ auth()->user()->status_verifikasi }}" === "disetujui";
  const metodePickup = document.getElementById('metode_pickup');
  const lokasiRental = document.getElementById('lokasiRental');
  const alamatUser = document.getElementById('alamatUser');
  const cekOngkirBtn = document.getElementById('cekOngkirBtn');
  const hasilOngkir = document.getElementById('hasilOngkir');

  // tampilkan lokasi atau input alamat
  metodePickup.addEventListener('change', () => {
    if (metodePickup.value === 'ambil_sendiri') {
      lokasiRental.style.display = 'block';
      alamatUser.style.display = 'none';
      hasilOngkir.textContent = '';
    } else if (metodePickup.value === 'pickup_alamat') {
      lokasiRental.style.display = 'none';
      alamatUser.style.display = 'block';
    } else {
      lokasiRental.style.display = 'none';
      alamatUser.style.display = 'none';
      hasilOngkir.textContent = '';
    }
  });

  // 🔹 Hitung ongkir ke backend
  cekOngkirBtn.addEventListener('click', async () => {
    const alamat = document.getElementById('alamat').value.trim();
    if (!alamat) return alert('Masukkan alamat Anda terlebih dahulu.');

    hasilOngkir.textContent = 'Menghitung jarak...';

    try {
      const res = await fetch("{{ route('user.pickup.distance') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
        },
        body: JSON.stringify({ alamat })
      });

      const data = await res.json();
      if (data.success) {
        hasilOngkir.innerHTML = `📏 Jarak: <b>${data.distance_text}</b> — Ongkir: <b>Rp${data.ongkir.toLocaleString('id-ID')}</b>`;
      } else {
        hasilOngkir.textContent = 'Gagal menghitung jarak.';
      }
    } catch {
      hasilOngkir.textContent = 'Terjadi kesalahan koneksi.';
    }
  });

  // 🔒 Validasi jam 08:00–22:00 WIB
  const minHour = 8, minMinute = 0, maxHour = 22, maxMinute = 0;
  const inputMulai = document.getElementById('tanggal_mulai');
  const inputSelesai = document.getElementById('tanggal_selesai');

  function validateTimeRange(input) {
    if (!input.value) return;
    const date = new Date(input.value);
    const hour = date.getHours();
    const minute = date.getMinutes();
    const totalMinutes = hour * 60 + minute;
    const minTotal = minHour * 60 + minMinute;
    const maxTotal = maxHour * 60 + maxMinute;

    if (totalMinutes < minTotal || totalMinutes > maxTotal) {
      alert('⚠️ Jam penyewaan hanya diperbolehkan antara 08:00 hingga 22:00 WIB.');
      if (totalMinutes < minTotal) date.setHours(minHour, minMinute);
      else if (totalMinutes > maxTotal) date.setHours(maxHour, maxMinute);
      input.value = date.toISOString().slice(0, 16);
    }
  }
  [inputMulai, inputSelesai].forEach(el => el.addEventListener('change', () => validateTimeRange(el)));

  // submit utama
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!isVerified) {
      popup.style.display = 'flex';
      return;
    }
    const mulai = document.getElementById('tanggal_mulai').value;
    const selesai = document.getElementById('tanggal_selesai').value;
    if (new Date(mulai) >= new Date(selesai)) {
      alert('Tanggal selesai harus lebih besar dari tanggal mulai.');
      return;
    }
    const fd = new FormData(this);
    fd.set('tanggal_mulai', mulai);
    fd.set('tanggal_selesai', selesai);
    fetch(this.action, {
      method: 'POST',
      body: fd,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf
      },
      credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.redirect_url) window.location.href = data.redirect_url;
      else alert(data.message || 'Terjadi kesalahan.');
    })
    .catch(() => alert('Terjadi kesalahan koneksi.'));
  });
</script>
</body>
</html>
