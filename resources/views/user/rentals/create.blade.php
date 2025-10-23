@extends('partials.container')

@section('title', 'Form Penyewaan')

@section('styles')
<style>
/* ===== Global & Wrapper ===== */
body { 
  font-family: 'Segoe UI', Arial, sans-serif; 
  background: #f8f9fa; 
  margin: 0; 
  padding: 0;
}

.container-wrapper {
  padding: 15px;
}

/* ===== Card ===== */
.card { 
  background: #fff; 
  border-radius: 10px; 
  padding: 25px; 
  max-width: 600px; 
  margin-left: -20px;
  margin-right: -20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
  margin-bottom: 15px;
}

h2 {
  font-size: 22px;
  color: #333;
  margin-bottom: 15px;
  text-align: center;
  font-weight: 700;
}

h3 {
  font-size: 20px;
  color: #444;
  margin-bottom: 6px;
}

p {
  color: #555;
  font-size: 15px;
}

/* ===== Label & Input ===== */
label {
  display: block;
  margin-top: 12px;
  font-weight: 550;
  color: #333;
}

input, select, textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1.5px solid #ccc;
  border-radius: 8px;
  margin-top: 5px;
  font-size: 14px;
  transition: all 0.2s ease;
  font-family: inherit;
}

input:focus, select:focus, textarea:focus {
  border-color: #70D972;
  box-shadow: 0 0 0 2px rgba(112,217,114,0.25);
  outline: none;
}

/* ===== Button ===== */
button { 
  background: #70D972;
  color: #000;
  font-weight: 600;
  padding: 12px 16px;
  border: none;
  border-radius: 8px;
  margin-top: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
}

button:hover {
  background: #5AC260;
  transform: scale(1.03);
}

/* ===== Back Link ===== */
.back-link {
  color: #000;
  text-decoration: none;
  font-weight: 500;
  font-size: 15px;
  display: inline-block;
  margin-bottom: 10px;
}

.back-link:hover {
  color: #000;
  text-decoration: underline;
}

/* ===== Info Box ===== */
.price-box {
  background: #f1fdf1;
  border-left: 4px solid #70D972;
  padding: 12px 15px;
  border-radius: 6px;
  margin-top: 15px;
  color: #333;
  font-size: 14px;
  height: 70px;
}

/* ===== Lokasi & Alamat Box ===== */
#lokasiRental, #alamatUser {
  background: #f9f9f9;
  border-radius: 10px;
  padding: 15px;
  border: 1px solid #e0e0e0;
}

/* ===== Popup Menunggu ===== */
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
  background: #fff;
  border-radius: 20px;
  padding: 30px 25px;
  width: 350px;
  text-align: center;
  position: relative;
  animation: fadeIn 0.4s ease;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

#popup-menunggu button.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  border: none;
  background: none;
  font-size: 22px;
  cursor: pointer;
  color: #888;
  transition: 0.2s;
}

#popup-menunggu button.close-btn:hover {
  color: #333;
}

/* ===== Popup Alert ===== */
#popup-alert {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(4px);
  justify-content: center;
  align-items: center;
  z-index: 10000;
}
#popup-alert .popup-box {
  background: #fff;
  border-radius: 20px;
  padding: 25px 20px;
  width: 330px;
  text-align: center;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
  animation: fadeIn 0.3s ease;
}
#popup-alert h3 {
  margin-bottom: 10px;
  color: #333;
  font-size: 18px;
}
#popup-alert p {
  font-size: 15px;
  color: #444;
  margin-bottom: 20px;
}
#popup-alert button {
  background: #70D972;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  transition: .2s;
}
#popup-alert button:hover { background:#5AC260; }

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}
</style>
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

  {{-- Link Kembali --}}
<a href="{{ route('user.cars.show', $car->car_id) }}" class="back-link" aria-label="Kembali">
  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
    <line x1="19" y1="12" x2="5" y2="12"/>
    <polyline points="12 19 5 12 12 5"/>
  </svg>
</a>

  <div class="card">
    <h2>Form Penyewaan Mobil</h2>

    <h3>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
    <p><strong>Harga per hari:</strong> Rp{{ number_format($car->harga_sewa_per_hari, 0, ',', '.') }}</p>

    <form id="rentalForm" action="{{ route('user.rentals.store', $car->car_id) }}" method="POST">
      @csrf
      <input type="hidden" name="redirect_back" value="{{ $redirectBack }}">

      <label for="tanggal_mulai">Tanggal & Jam Mulai</label>
      <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" required>

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

      <div id="lokasiRental" style="display:none; margin-top:10px;">
        <p>📍 Lokasi Rental Kami:</p>
        <p><strong>Jl. Melati No. 12, Bandung</strong></p>
        <a href="https://www.google.com/maps?q=-6.914744,107.609810" target="_blank" style="color:#0d6efd;">Lihat di Google Maps</a>
      </div>

      <div id="alamatUser" style="display:none; margin-top:10px;">
        <label for="alamat">Alamat Anda</label>
        <textarea id="alamat" name="alamat" placeholder="Masukkan alamat lengkap Anda..." rows="3"></textarea>
        <button type="button" id="cekOngkirBtn" style="margin-top:8px; height:45px;">Cek Ongkir</button>
        <p id="hasilOngkir" style="margin-top:8px; color:#333;"></p>
      </div>

      <div class="price-box">
        <p><strong>Catatan:</strong> Total biaya akan dihitung otomatis setelah pembayaran.</p>
      </div>

      <button type="submit">Konfirmasi Sewa</button>
    </form>
  </div>

  @include('partials.bottom-navbar')
</div>

{{-- Popup Menunggu Verifikasi --}}
<div id="popup-menunggu">
  <div class="popup-content">
    <button class="close-btn" id="close-popup">✖</button>
    <h3>Menunggu Verifikasi</h3>
    <p>Dokumen Anda sedang diperiksa oleh admin. Silakan tunggu sampai proses verifikasi selesai sebelum melanjutkan penyewaan.</p>
  </div>
</div>

{{-- Popup Alert --}}
<div id="popup-alert">
  <div class="popup-box">
    <h3>Peringatan</h3>
    <p id="alert-message"></p>
    <button id="alert-ok">Oke</button>
  </div>
</div>

<script>
window.addEventListener('load', function() {
  const form = document.getElementById('rentalForm');
  const popup = document.getElementById('popup-menunggu');
  const metodePickup = document.getElementById('metode_pickup');
  const lokasiRental = document.getElementById('lokasiRental');
  const alamatUser = document.getElementById('alamatUser');
  const cekOngkirBtn = document.getElementById('cekOngkirBtn');
  const hasilOngkir = document.getElementById('hasilOngkir');
  const mulai = document.getElementById('tanggal_mulai');
  const selesai = document.getElementById('tanggal_selesai');

  const hasDocuments = {{ $hasDocuments ? 'true' : 'false' }};
  const isVerified   = {{ $isVerified ? 'true' : 'false' }};

  // ===== Modal Alert Function =====
  function showAlert(message, redirectUrl = null) {
    const popupAlert = document.getElementById('popup-alert');
    const msg = document.getElementById('alert-message');
    msg.innerHTML = message;
    popupAlert.style.display = 'flex';
    document.getElementById('alert-ok').onclick = () => {
      popupAlert.style.display = 'none';
      if (redirectUrl) window.location.href = redirectUrl;
    };
  }

  // ===== BATASI TANGGAL (minimal hari ini & minimal 24 jam sewa) =====
  const now = new Date();
  const localNow = now.toISOString().slice(0, 16);
  mulai.min = localNow;
  selesai.min = localNow;

  // Ketika user ubah tanggal mulai
  mulai.addEventListener('change', function () {
    if (!mulai.value) return;

    const startDate = new Date(mulai.value);
    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000); // +24 jam
    const formattedMin = minEndDate.toISOString().slice(0, 16);
    selesai.min = formattedMin;

    // Reset kalau tanggal selesai lebih kecil dari minimum
    if (selesai.value && new Date(selesai.value) < minEndDate) {
      selesai.value = '';
    }
  });

  // ===== TOGGLE PICKUP =====
  function togglePickup() {
    const val = metodePickup.value;
    if (val === 'ambil_sendiri') {
      lokasiRental.style.display = 'block';
      alamatUser.style.display = 'none';
      hasilOngkir.textContent = '';
    } else if (val === 'pickup_alamat') {
      lokasiRental.style.display = 'none';
      alamatUser.style.display = 'block';
    } else {
      lokasiRental.style.display = 'none';
      alamatUser.style.display = 'none';
      hasilOngkir.textContent = '';
    }
  }

  togglePickup();
  metodePickup.addEventListener('change', togglePickup);

  // ===== CEK ONGKIR =====
  cekOngkirBtn.addEventListener('click', async () => {
    const alamat = document.getElementById('alamat').value.trim();
    if (!alamat) return showAlert('Masukkan alamat Anda terlebih dahulu.');

    hasilOngkir.textContent = 'Menghitung jarak...';
    try {
      const res = await fetch("{{ route('user.pickup.distance') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ alamat }),
        credentials: 'same-origin'
      });
      const data = await res.json();
      hasilOngkir.innerHTML = data.success
        ? `📏 Jarak: <b>${data.distance_text}</b> — Ongkir: <b>Rp${data.ongkir.toLocaleString('id-ID')}</b>`
        : 'Gagal menghitung jarak.';
    } catch {
      hasilOngkir.textContent = 'Terjadi kesalahan koneksi.';
    }
  });

  // ===== VALIDASI JAM SEWA (antara 08:00–22:00) =====
  function validateTimeRange(input) {
    if (!input.value) return;
    const hour = new Date(input.value).getHours();
    if (hour < 8 || hour > 22) {
      showAlert('⚠️ Jam penyewaan hanya diperbolehkan antara 08:00 hingga 22:00 WIB.');
      input.value = '';
    }
  }

  mulai.addEventListener('change', e => validateTimeRange(e.target));
  selesai.addEventListener('change', e => validateTimeRange(e.target));

  // ===== SUBMIT FORM =====
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    if (!hasDocuments) {
      return showAlert('Silakan unggah KTP & KK terlebih dahulu.', "{{ route('user.verifikasi.index') }}");
    }

    if (!isVerified) {
      return popup.style.display = 'flex';
    }

    const mulaiVal = mulai.value;
    const selesaiVal = selesai.value;

    // Validasi tanggal selesai minimal 24 jam setelah mulai
    const startDate = new Date(mulaiVal);
    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
    if (new Date(selesaiVal) < minEndDate) {
      return showAlert('Durasi sewa minimal 24 jam dari waktu mulai.');
    }

    if (new Date(mulaiVal) >= new Date(selesaiVal)) {
      return showAlert('Tanggal selesai harus lebih besar dari tanggal mulai.');
    }

    fetch(this.action, {
      method: 'POST',
      body: new FormData(this),
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.redirect_url) {
        window.location.href = data.redirect_url;
      } else {
        showAlert(data.message || 'Terjadi kesalahan.');
      }
    })
    .catch(() => showAlert('Terjadi kesalahan koneksi.'));
  });

  // ===== TUTUP POPUP VERIFIKASI =====
  document.getElementById('close-popup').addEventListener('click', () => {
    popup.style.display = 'none';
  });
});
</script>

@endsection
