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

.container-wrapper { padding: 15px; }

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

h2 { font-size: 22px; color: #333; margin-bottom: 15px; text-align: center; font-weight: 700; }
h3 { font-size: 20px; color: #444; margin-bottom: 6px; }
p  { color: #555; font-size: 15px; }

/* ===== Label & Input ===== */
label { display: block; margin-top: 12px; font-weight: 550; color: #333; }

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
button:hover { background: #5AC260; transform: scale(1.03); }

/* ===== Back Link ===== */
.back-link {
  color: #000; text-decoration: none; font-weight: 500;
  font-size: 15px; display: inline-block; margin-bottom: 10px;
}
.back-link:hover { color: #000; text-decoration: underline; }

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

/* ========================= DRIVER SECTION ========================= */
.driver-section {
  margin-top: 25px;
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.driver-section label {
  font-weight: 600;
  display: block;
  margin-bottom: 6px;
  color: #333;
}

.driver-section select {
  width: 100%;
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 15px;
  outline: none;
  transition: .2s;
}

.driver-section select:focus {
  border-color: #0d6efd;
}

.driver-list {
  margin-top: 15px;
  display: none;
}

.driver-selected {
  display: none;
  margin-top: 12px;
  padding: 12px;
  border-radius: 8px;
  background: #eaf6ff;
  border-left: 4px solid #0d6efd;
}
.driver-selected strong { color: #0d6efd; }

/* === Modal Popup === */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-box {
  background: #fff;
  border-radius: 16px;
  padding: 25px;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
  position: relative;
  animation: fadeIn .3s ease;
}

.modal-box img {
  width: 90px;
  height: 90px;
  border-radius: 12px;
  object-fit: cover;
  display: block;
  margin: auto;
}

.modal-box h3 {
  text-align: center;
  margin: 12px 0 4px;
}

.modal-box p {
  color: #555;
  font-size: 14px;
  margin: 3px 0;
  text-align: center;
}

.modal-box button.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  border: none;
  background: none;
  font-size: 22px;
  cursor: pointer;
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
#popup-menunggu button.close-btn:hover { color: #333; }

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
#popup-alert h3 { margin-bottom: 10px; color: #333; font-size: 18px; }
#popup-alert p { font-size: 15px; color: #444; margin-bottom: 20px; }
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

  {{-- 🔙 Link Kembali --}}
  <a href="{{ route('user.cars.show', $car->car_id) }}" class="back-link" aria-label="Kembali">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

      {{-- ==================== DRIVER SECTION ==================== --}}
      <div class="driver-section">
        <label for="driver">Butuh Driver?</label>
        <select name="driver" id="driver" required onchange="toggleDriverList(this)">
          <option value="tidak">Tidak</option>
          <option value="ya">Ya (+ otomatis sesuai tarif driver)</option>
        </select>

        <div id="driverList" class="driver-list">
          <label for="driver_id" style="margin-top:10px;">Pilih Driver</label>
          <select id="driver_id" name="driver_id" onchange="showDriverCard(this)">
            <option value="">-- Pilih Driver --</option>
            @foreach($drivers as $driver)
              <option 
                value="{{ $driver->driver_id }}"
                data-foto="{{ asset('storage/' . $driver->foto) }}"
                data-nama="{{ $driver->nama }}"
                data-lokasi="{{ $driver->lokasi ?? 'Tidak diketahui' }}"
                data-harga="{{ number_format($driver->harga_per_hari,0,',','.') }}"
                data-pengalaman="{{ $driver->pengalaman ?? 'Tidak diketahui' }}"
                data-verifikasi="{{ ucfirst($driver->status_verifikasi) }}"
                data-deskripsi="{{ $driver->deskripsi ?? 'Tidak ada deskripsi.' }}">
                {{ $driver->nama }}
              </option>
            @endforeach
          </select>

          <div id="driverSelected" class="driver-selected">
            <strong id="selectedDriverName"></strong><br>
            <button type="button" class="btn btn-sm" style="margin-top:6px;background:#0d6efd;color:#fff;padding:6px 10px;border:none;border-radius:6px;" onclick="showDriverDetail()">Lihat Detail Driver</button>
          </div>
        </div>
      </div>

      <!-- Modal detail driver -->
      <div id="driverModal" class="modal-overlay">
        <div class="modal-box">
          <button class="close-btn" onclick="closeDriverModal()">×</button>
          <img id="modalDriverFoto" src="">
          <h3 id="modalDriverNama"></h3>
          <p id="modalDriverVerifikasi"></p>
          <p id="modalDriverLokasi"></p>
          <p id="modalDriverHarga"></p>
          <p id="modalDriverPengalaman"></p>
          <p id="modalDriverDeskripsi"></p>
        </div>
      </div>

      {{-- ==================== PICKUP SECTION ==================== --}}
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

{{-- 🕐 Popup Menunggu Verifikasi --}}
<div id="popup-menunggu">
  <div class="popup-content">
    <button class="close-btn" id="close-popup">✖</button>
    <img src="{{ asset('img/waiting-illustration.png') }}" alt="Menunggu" style="width:150px; margin:10px auto;">
    <h3>Belum Diverifikasi</h3>
    <p>Anda harus mengunggah KTP & KK terlebih dahulu sebelum melanjutkan penyewaan.</p>
    <a href="{{ route('user.verifikasi.index') }}"
       style="display:inline-block;background:#0d6efd;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none;">Unggah Sekarang</a>
  </div>
</div>

{{-- ⚠️ Popup Alert --}}
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

  function showAlert(message, redirectUrl = null) {
    const popupAlert = document.getElementById('popup-alert');
    document.getElementById('alert-message').innerHTML = message;
    popupAlert.style.display = 'flex';
    document.getElementById('alert-ok').onclick = () => {
      popupAlert.style.display = 'none';
      if (redirectUrl) window.location.href = redirectUrl;
    };
  }

  // BATASI TANGGAL (minimal hari ini & minimal 24 jam sewa)
  const now = new Date();
  const localNow = now.toISOString().slice(0, 16);
  mulai.min = localNow;
  selesai.min = localNow;

  mulai.addEventListener('change', function () {
    const startDate = new Date(mulai.value);
    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
    selesai.min = minEndDate.toISOString().slice(0, 16);
    if (selesai.value && new Date(selesai.value) < minEndDate) selesai.value = '';
  });

  // TOGGLE PICKUP
  function togglePickup() {
    const val = metodePickup.value;
    lokasiRental.style.display = val === 'ambil_sendiri' ? 'block' : 'none';
    alamatUser.style.display   = val === 'pickup_alamat' ? 'block' : 'none';
    if (val !== 'pickup_alamat') hasilOngkir.textContent = '';
  }
  togglePickup();
  metodePickup.addEventListener('change', togglePickup);

  // CEK ONGKIR
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

  // VALIDASI JAM SEWA (antara 08:00–22:00)
  function validateTimeRange(input) {
    const hour = new Date(input.value).getHours();
    if (hour < 8 || hour > 22) {
      showAlert('⚠️ Jam penyewaan hanya diperbolehkan antara 08:00 hingga 22:00 WIB.');
      input.value = '';
    }
  }
  mulai.addEventListener('change', e => validateTimeRange(e.target));
  selesai.addEventListener('change', e => validateTimeRange(e.target));

  // SUBMIT FORM
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!hasDocuments) return showAlert('Silakan unggah KTP & KK terlebih dahulu.', "{{ route('user.verifikasi.index') }}");
    if (!isVerified) return popup.style.display = 'flex';

    const startDate = new Date(mulai.value);
    const endDate = new Date(selesai.value);
    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
    if (endDate < minEndDate) return showAlert('Durasi sewa minimal 24 jam dari waktu mulai.');
    if (startDate >= endDate) return showAlert('Tanggal selesai harus lebih besar dari tanggal mulai.');

    fetch(this.action, {
      method: 'POST',
      body: new FormData(this),
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => data.success && data.redirect_url 
      ? window.location.href = data.redirect_url
      : showAlert(data.message || 'Terjadi kesalahan.'))
    .catch(() => showAlert('Terjadi kesalahan koneksi.'));
  });

  document.getElementById('close-popup').addEventListener('click', () => popup.style.display = 'none');
});

// ==================== DRIVER FUNCTIONS ====================
function toggleDriverList(sel) {
  const list = document.getElementById('driverList');
  list.style.display = (sel.value === 'ya') ? 'block' : 'none';
  document.getElementById('driverSelected').style.display = 'none';
  document.getElementById('driver_id').value = '';
}

function showDriverCard(sel) {
  const opt = sel.options[sel.selectedIndex];
  if (!opt.value) {
    document.getElementById('driverSelected').style.display = 'none';
    return;
  }
  document.getElementById('driverSelected').style.display = 'block';
  document.getElementById('selectedDriverName').textContent = opt.dataset.nama;
}

function showDriverDetail() {
  const sel = document.getElementById('driver_id');
  const opt = sel.options[sel.selectedIndex];
  if (!opt.value) return;

  document.getElementById('driverModal').style.display = 'flex';
  document.getElementById('modalDriverFoto').src = opt.dataset.foto;
  document.getElementById('modalDriverNama').textContent = opt.dataset.nama;
  document.getElementById('modalDriverVerifikasi').textContent = '✅ ' + opt.dataset.verifikasi;
  document.getElementById('modalDriverLokasi').textContent = '📍 Lokasi: ' + opt.dataset.lokasi;
  document.getElementById('modalDriverHarga').textContent = '💰 Tarif: Rp' + opt.dataset.harga + ' /hari';
  document.getElementById('modalDriverPengalaman').textContent = '🕓 Pengalaman: ' + opt.dataset.pengalaman;
  document.getElementById('modalDriverDeskripsi').textContent = opt.dataset.deskripsi;
}

function closeDriverModal() {
  document.getElementById('driverModal').style.display = 'none';
}
</script>
@endsection
