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

/* 🚗===== DRIVER SECTION =====*/
.driver-section {
  margin-top: 25px;
  background: #ffffff;
  border-radius: 14px;
  padding: 22px 20px;
  border: 1.5px solid #e0e0e0;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}
.driver-section:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); }

.driver-section label {
  font-weight: 600;
  color: #222;
  margin-bottom: 8px;
  font-size: 15px;
}

/* Animasi muncul driver list */
.driver-list {
  margin-top: 15px;
  display: none;
  opacity: 0;
  transform: translateY(-5px);
  transition: all 0.25s ease;
}
.driver-list.show {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

/* === Custom dropdown driver === */
.driver-dropdown {
  position: relative;
  width: 100%;
  user-select: none;
}
.driver-dropdown .selected {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px;
  border: 1.5px solid #ccc;
  border-radius: 8px;
  background-color: #fafafa;
  cursor: pointer;
  transition: 0.2s;
}
.driver-dropdown .selected:hover { background-color: #f5fff5; }
.driver-dropdown .selected span img {
  width: 35px;
  height: 35px;
  border-radius: 8px;
  object-fit: cover;
  border: 1.5px solid #70D972;
  margin-right: 8px;
  vertical-align: middle;
}
.driver-dropdown .options {
  position: absolute;
  width: 100%;
  background: #fff;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  margin-top: 6px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  list-style: none;
  padding: 8px 0;
  max-height: 250px;
  overflow-y: auto;
  display: none;
  z-index: 99;
}
.driver-dropdown.open .options { display: block; }
.driver-dropdown .option {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  cursor: pointer;
  transition: 0.2s;
}
.driver-dropdown .option:hover { background: #f3fdf3; }
.driver-dropdown .option img {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  object-fit: cover;
  margin-right: 10px;
  border: 1.5px solid #70D972;
}

.driver-selected {
  display: none;
  margin-top: 8px; /* ⬅ dari 18px jadi 8px biar lebih dekat */
  padding-top: 0;  /* ⬅ hapus jarak dalam atas */
  text-align: center;
  background: transparent;
  border: none;
  box-shadow: none;
}

.driver-selected button {
  background: #007bff;
  color: #fff;
  font-weight: 600;
  padding: 8px 14px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.25s ease;
}
.driver-selected button:hover {
  background: #0069d9;
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* ===== Modal Detail Driver (Final Clean & Modern) ===== */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-box {
  position: relative;
  background: #fff;
  border-radius: 18px;
  padding: 32px 25px 35px;
  width: 90%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0,0,0,0.18);
  animation: fadeIn 0.3s ease;
  font-family: 'Segoe UI', Arial, sans-serif;
  border: 1px solid #eaeaea;
  overflow: hidden;
}

.modal-box .close-btn {
  position: absolute;
  top: -15px;
  right: 14px;
  background: transparent;
  color: #333;
  border: none;
  font-size: 22px;
  font-weight: 500;
  cursor: pointer;
  line-height: 1;
  transition: color 0.2s ease;
  margin-right: -175px;
}
.modal-box .close-btn:hover {
  color: #000;
}

/* Foto Driver */
.modal-box img {
  display: block;
  width: 110px;
  height: 110px;
  border-radius: 14px;
  object-fit: cover;
  border: 2px solid #70D972;
  margin: 10px auto 14px auto;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

/* Nama Driver */
.modal-box h3 {
  margin-bottom: 16px;
  font-size: 20px;
  color: #111;
  font-weight: 700;
}

/* Detail teks */
.modal-box p {
  margin: 8px 0;
  color: #333;
  line-height: 1.5;
  font-size: 15px;
  text-align: left;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #f0f0f0;
  background: #fafafa;
}

/* Spasi antar item */
.modal-box p + p {
  margin-top: 6px;
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
          <option value="tidak" selected>Tidak</option>
          <option value="ya">Ya (+ otomatis sesuai tarif driver)</option>
        </select>

        <div id="driverList" class="driver-list">
          <label for="driver_id" style="margin-top:10px;">Pilih Driver</label>
          <div class="driver-dropdown" id="driverDropdown">
            <div class="selected" onclick="toggleDropdown()">
              <span>Pilih Driver...</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
            <ul class="options">
              @foreach($drivers as $driver)
                <li class="option" 
                    data-id="{{ $driver->driver_id }}"
                    data-foto="{{ asset('storage/' . $driver->foto) }}"
                    data-nama="{{ $driver->nama }}"
                    data-lokasi="{{ $driver->lokasi ?? 'Tidak diketahui' }}"
                    data-harga="{{ number_format($driver->harga_per_hari,0,',','.') }}"
                    data-pengalaman="{{ $driver->pengalaman ?? 'Tidak diketahui' }}"
                    data-verifikasi="{{ ucfirst($driver->status_verifikasi) }}"
                    data-deskripsi="{{ $driver->deskripsi ?? 'Tidak ada deskripsi.' }}"
                    onclick="selectDriver(this)">
                  <img src="{{ asset('storage/' . $driver->foto) }}" alt="{{ $driver->nama }}">
                  <span>{{ $driver->nama }}</span>
                </li>
              @endforeach
            </ul>
            <input type="hidden" name="driver_id" id="driver_id">
          </div>
        </div>

        {{-- ⛔ Disembunyikan total sampai driver diklik --}}
<div id="driverSelected" class="driver-selected" style="display:none;">
  <button type="button" id="lihatDetailBtn" style="display:none;" onclick="showDriverDetail()">Lihat Detail Driver</button>
</div>
      </div>

      {{-- Modal Detail --}}
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

  /* ==================== ALERT FUNCTION ==================== */
  function showAlert(message, redirectUrl = null) {
    const popupAlert = document.getElementById('popup-alert');
    document.getElementById('alert-message').innerHTML = message;
    popupAlert.style.display = 'flex';
    document.getElementById('alert-ok').onclick = () => {
      popupAlert.style.display = 'none';
      if (redirectUrl) window.location.href = redirectUrl;
    };
  }

  /* ==================== VALIDASI TANGGAL ==================== */
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

  /* ==================== PICKUP TOGGLE ==================== */
  function togglePickup() {
    const val = metodePickup.value;
    lokasiRental.style.display = val === 'ambil_sendiri' ? 'block' : 'none';
    alamatUser.style.display   = val === 'pickup_alamat' ? 'block' : 'none';
    if (val !== 'pickup_alamat') hasilOngkir.textContent = '';
  }
  togglePickup();
  metodePickup.addEventListener('change', togglePickup);

  /* ==================== CEK ONGKIR ==================== */
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

  /* ==================== VALIDASI JAM SEWA ==================== */
  function validateTimeRange(input) {
    const hour = new Date(input.value).getHours();
    if (hour < 8 || hour > 22) {
      showAlert('⚠️ Jam penyewaan hanya diperbolehkan antara 08:00 hingga 22:00 WIB.');
      input.value = '';
    }
  }
  mulai.addEventListener('change', e => validateTimeRange(e.target));
  selesai.addEventListener('change', e => validateTimeRange(e.target));

  /* ==================== DRIVER SECTION ==================== */
  function toggleDropdown() {
    document.getElementById('driverDropdown').classList.toggle('open');
  }

  function selectDriver(el) {
    const dropdown = document.getElementById('driverDropdown');
    const selected = dropdown.querySelector('.selected span');
    const hiddenInput = document.getElementById('driver_id');
    const driverSelectedBox = document.getElementById('driverSelected');
    const lihatDetailBtn = document.getElementById('lihatDetailBtn');

    // ✅ tampilkan foto + nama di pilihan
    selected.innerHTML = `<img src="${el.dataset.foto}" style="width:30px;height:30px;border-radius:8px;object-fit:cover;margin-right:6px;vertical-align:middle;border:1px solid #70D972;"> ${el.dataset.nama}`;
    hiddenInput.value = el.dataset.id;

    driverSelectedBox.style.display = 'flex';
    lihatDetailBtn.style.display = 'inline-block';
    dropdown.classList.remove('open');
  }

  function toggleDriverList(sel) {
    const list = document.getElementById('driverList');
    const driverSelectedBox = document.getElementById('driverSelected');
    const hiddenInput = document.getElementById('driver_id');
    const lihatDetailBtn = document.getElementById('lihatDetailBtn');

    if (sel.value === 'ya') {
      list.classList.add('show');
    } else {
      list.classList.remove('show');
      hiddenInput.value = '';
      driverSelectedBox.style.display = 'none';
      lihatDetailBtn.style.display = 'none';
      document.querySelector('#driverDropdown .selected span').innerHTML = 'Pilih Driver...';
    }
  }

  function showDriverDetail() {
    const id = document.getElementById('driver_id').value;
    if (!id) return;
    const opt = document.querySelector(`.option[data-id="${id}"]`);
    if (!opt) return;

    document.getElementById('driverModal').style.display = 'flex';
    document.getElementById('modalDriverFoto').src = opt.dataset.foto;
    document.getElementById('modalDriverNama').textContent = opt.dataset.nama;
    document.getElementById('modalDriverVerifikasi').textContent = '✅ ' + opt.dataset.verifikasi;
    document.getElementById('modalDriverLokasi').textContent = '📍 ' + opt.dataset.lokasi;
    document.getElementById('modalDriverHarga').textContent = '💰 Rp' + opt.dataset.harga + '/hari';
    document.getElementById('modalDriverPengalaman').textContent = '🕓 ' + opt.dataset.pengalaman;
    document.getElementById('modalDriverDeskripsi').textContent = opt.dataset.deskripsi;
  }

  function closeDriverModal() {
    document.getElementById('driverModal').style.display = 'none';
  }

  // biar fungsi dipanggil global dari HTML
  window.toggleDropdown = toggleDropdown;
  window.selectDriver = selectDriver;
  window.toggleDriverList = toggleDriverList;
  window.showDriverDetail = showDriverDetail;
  window.closeDriverModal = closeDriverModal;

  /* ==================== SUBMIT FORM ==================== */
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
    .then(data => {
      if (data.success && data.redirect_url) {
        window.location.href = data.redirect_url;
      } else {
        showAlert(data.message || 'Terjadi kesalahan.');
      }
    })
    .catch(() => showAlert('Terjadi kesalahan koneksi.'));
  });

  /* ==================== CLOSE POPUP ==================== */
  document.getElementById('close-popup').addEventListener('click', () => popup.style.display = 'none');
});
</script>

@endsection
