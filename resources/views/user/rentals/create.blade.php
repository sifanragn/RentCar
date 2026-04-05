@extends('partials.container')

@section('title', 'Form Penyewaan')


@section('styles')
<style>
/* ===== Global & Wrapper ===== */
body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
.container-wrapper { padding: 15px; }

/* ===== Card ===== */
.card { background: #fff; border-radius: 10px; padding: 25px; max-width: 600px;
  margin-left: -20px; margin-right: -20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 15px; }

h2 { font-size: 22px; color: #333; margin-bottom: 15px; text-align: center; font-weight: 700; }
h3 { font-size: 20px; color: #444; margin-bottom: 6px; }
p  { color: #555; font-size: 15px; }

label { display: block; margin-top: 12px; font-weight: 550; color: #333; }

input, select, textarea {
  width: 100%; padding: 10px 12px; border: 1.5px solid #ccc; border-radius: 8px;
  margin-top: 5px; font-size: 14px; transition: 0.2s; font-family: inherit;
}
input:focus, select:focus, textarea:focus {
  border-color: #000; box-shadow: 0 0 0 2px rgba(0,0,0,0.25); outline: none;
}

/* ===== Button ===== */
button { background: #000; color: #fff; font-weight: 600; padding: 12px 16px;
  border: none; border-radius: 8px; margin-top: 18px; cursor: pointer;
  transition: 0.3s; width: 100%; }
button:hover { background: #222; transform: scale(1.03); }

/* ===== Back Link ===== */
.back-link {
  position: relative !important;
  z-index: 999999 !important;
  pointer-events: auto !important;
}
.back-link:hover { text-decoration: underline; }

/* ===== Info Box ===== */
.price-box {
  background: #f1f1f1; border-left: 4px solid #000; padding: 12px 15px;
  border-radius: 6px; margin-top: 15px; color: #333; font-size: 14px; height: 70px;
}

/* ===== Lokasi & Alamat Box ===== */
#lokasiRental, #alamatUser { background: #f9f9f9; border-radius: 10px; padding: 15px; border: 1px solid #e0e0e0; }

/* 🚗 ===== DRIVER SECTION ===== */
.driver-section {
  margin-top: 25px; background: #fff; border-radius: 14px;
  padding: 22px 20px; border: 1.5px solid #e0e0e0;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}
.driver-section:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); }
.driver-section label { font-weight: 600; color: #222; margin-bottom: 8px; font-size: 15px; }

.driver-list { margin-top: 15px; display: none; opacity: 0; transform: translateY(-5px); transition: all 0.25s ease; }
.driver-list.show { display: block; opacity: 1; transform: translateY(0); }

.driver-dropdown { position: relative; width: 100%; user-select: none; z-index: 10; }
.driver-dropdown .selected {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 12px; border: 1.5px solid #ccc; border-radius: 8px;
  background: #fafafa; cursor: pointer;
}
.driver-dropdown .options {
  position: absolute; width: 100%; background: #fff; border: 1.5px solid #ddd;
  border-radius: 8px; margin-top: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  list-style: none; padding: 8px 0; max-height: 250px; overflow-y: auto;
  display: none; z-index: 99;
}
.driver-dropdown.open .options { display: block; }
.driver-dropdown .option { display: flex; align-items: center; padding: 8px 12px; cursor: pointer; transition: 0.2s; }
.driver-dropdown .option:hover { background: #f2f2f2; }
.driver-dropdown .option img {
  width: 40px; height: 40px; border-radius: 10px; object-fit: cover; margin-right: 10px; border: 1.5px solid #000;
}

.driver-selected { display: none; margin-top: 8px; text-align: center; }
.driver-selected button {
  background: #000; color: #fff; font-weight: 600; padding: 8px 14px;
  border-radius: 8px; border: none; cursor: pointer; transition: 0.25s;
}
.driver-selected button:hover { background: #222; transform: translateY(-1px); }

.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.6);
  display: none; justify-content: center; align-items: center;
  z-index: 99999; overflow-y: auto;
}
.modal-box {
  position: relative; background: #fff; border-radius: 18px; padding: 32px 25px 35px;
  width: 90%; max-width: 400px; text-align: center;
  box-shadow: 0 10px 30px rgba(0,0,0,0.18); animation: fadeIn 0.3s ease;
}
.modal-box img {
  display: block; width: 110px; height: 110px; border-radius: 14px; object-fit: cover;
  border: 2px solid #000; margin: 10px auto 14px auto; box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.modal-box .close-btn {
  position: absolute; top: 10px; right: 15px; border: none; background: none;
  font-size: 22px; cursor: pointer; color: #888; margin-right: -150px; margin-top: -5px
}
.modal-box .close-btn:hover { color: #000; }
.modal-box p { margin: 6px 0; font-size: 15px; color: #333; background: #fafafa; padding: 6px 10px; border-radius: 6px; text-align: left; }

.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: none; justify-content: center; align-items: center;
  backdrop-filter: blur(4px);
  z-index: 99999;
}

.modal-box {
  border-radius: 20px;
  background:#fff;
  animation: fadeIn .25s ease;
}

.modal-close-btn {
  position: absolute;
  top: 5px;
  right: 10px;
  width: 32px;
  height: 32px;
  background: #f2f2f2;
  border: none;
  border-radius: 50%;
  font-size: 18px;
  color: #333;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: .2s;
}

.modal-close-btn:hover {
  background: #e0e0e0;
  transform: scale(1.1);
}

/* ===== Car Info Header ===== */
.car-info-box {
  background: #ffffff;
  padding: 16px 18px;
  border-radius: 14px;
  border: 2px solid #e7e7e7;
  margin-bottom: 18px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.04);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.car-text-block {
  flex: 1;
}

.car-img-box {
  width: 80px; /* dulu 50px — ditambah biar lebih keliatan */
  height: auto;
  object-fit: contain;
  border-radius: 10px;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
}

.car-img-box img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.car-title {
  font-size: 18px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
  color: #111;
}

.brand-tag {
  background: #000;
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  padding: 3px 6px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: .3px;
}

.car-price {
  font-size: 17px;
  font-weight: 700;
  color: #0d6efd;
  margin-top: 4px;
}

.car-price span {
  font-size: 13px;
  font-weight: 500;
  color: #666;
}

@keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

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

<a href="{{ route('user.cars.show', $car->car_id) }}" class="back-link" id="btnBack">
</a>


  <div class="card">
    <h2>Form Penyewaan Mobil</h2>
<div class="car-info-box">
  
  <div class="car-text-block">
    <div class="car-title">
      <span class="brand-tag">{{ $car->brand->nama_merek }}</span>
      {{ $car->model }}
    </div>

    <div class="car-price">
      Rp{{ number_format($car->harga_sewa_per_jam, 0, ',', '.') }}
      <span>/ jam</span>
    </div>
  </div>

<div class="car-img-box">
  <img 
    src="{{ $car->foto ? asset('storage/' . $car->foto) : asset('/images/default-car.png') }}"
    alt="Car Image"
  >
</div>

</div>

    <form id="rentalForm" action="{{ route('user.rentals.store', $car->car_id) }}" method="POST">
      @csrf
      <input type="hidden" name="redirect_back" value="{{ $redirectBack }}">

<label for="tanggal_mulai">Tanggal & Jam Mulai</label>
<small style="color:#666; font-size:12px; display:block; line-height:1.45; margin:6px 0 2px;">
  • Mulai minimal <b>5 menit</b> dari sekarang<br>
  • Minimal sewa <b>6 jam</b><br>
  • Jam operasional: <b>08:00 — 22:00 WIB</b>
</small>
<input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" required>

      <label for="tanggal_selesai">Tanggal & Jam Selesai</label>
      <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" required>

      {{-- DRIVER --}}
      <div class="driver-section">
        <label for="driver">Butuh Driver?</label>
        <select name="driver" id="driver" onchange="toggleDriverList(this)">
          <option value="tidak" selected>Tidak</option>
          <option value="ya">Ya (+ tarif driver otomatis)</option>
        </select>

        <div id="driverList" class="driver-list">
          <label for="driver_id">Pilih Driver</label>
          <div class="driver-dropdown" id="driverDropdown">
            <div class="selected" onclick="toggleDropdown()">
              <span>Pilih Driver...</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#333" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <ul class="options">
              @foreach($drivers as $driver)
                <li class="option"
                    data-id="{{ $driver->driver_id }}"
                    data-foto="{{ asset('storage/' . $driver->foto) }}"
                    data-nama="{{ $driver->nama }}"
                    data-lokasi="{{ $driver->lokasi ?? 'Tidak diketahui' }}"
                    data-harga="{{ number_format($driver->harga_per_jam ?? 0, 0, ',', '.') }}"
                    data-pengalaman="{{ $driver->pengalaman ?? 'Tidak diketahui' }}"
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

        <div id="driverSelected" class="driver-selected">
          <button type="button" id="lihatDetailBtn" onclick="showDriverDetail()">Lihat Detail Driver</button>
        </div>
      </div>

      {{-- ✅ METODE PICKUP DARI FILE SATU --}}
      <label for="metode_pickup" style="margin-top:18px;">Metode Pengambilan</label>
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
        <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap Anda..."></textarea>
        <button type="button" id="cekOngkirBtn" style="margin-top:8px; height:45px;">Cek Ongkir</button>
        <p id="hasilOngkir" style="margin-top:8px; color:#333;"></p>
      </div>
      {{-- ✅ END METODE PICKUP --}}
      
      <div class="price-box">
        <p><strong>Catatan:</strong> Total biaya akan dihitung otomatis berdasarkan jam sewa.</p>
      </div>

      <button type="submit">Konfirmasi Sewa</button>

<!-- ✅ MODAL WAJIB VERIFIKASI -->
<div id="modalVerifikasi" class="modal-overlay" style="display:none;">
  <div class="modal-box" style="max-width:350px; text-align:center; padding:25px; position:relative;">

    <!-- ✅ Close Button -->
    <button id="btnCloseVerify" class="modal-close-btn">✕</button>

    <!-- Icon -->
    <div style="
      width:70px; height:70px; background:#e8f0ff; 
      border-radius:50%; display:flex; align-items:center; justify-content:center;
      margin:0 auto 12px auto; font-size:32px; color:#0d6efd;">
      🔒
    </div>

    <h3 style="margin:6px 0 8px; font-size:19px; font-weight:700; color:#111;">
      Belum Diverifikasi
    </h3>

    <p style="
      font-size:14px; color:#444; background:#f8f8f8; padding:12px;
      border-radius:10px; margin-bottom:18px;">
      Anda harus mengunggah KTP & KK terlebih dahulu sebelum melanjutkan penyewaan.
    </p>

    <button id="btnUnggahNow" style="
      background:#0d6efd; color:#fff; width:100%; padding:12px; font-weight:600;
      border:none; border-radius:10px; cursor:pointer; margin-bottom:10px;">
      Unggah Sekarang
    </button>

  </div>
</div>
<!-- ✅ MODAL WAJIB TAUTKAN SOSMED -->
<div id="modalSosmed" class="modal-overlay" style="display:none;">
  <div class="modal-box" style="max-width:350px; text-align:center; padding:25px; position:relative;">

    <button id="btnCloseSosmed" class="modal-close-btn">✕</button>

    <div style="
      width:70px; height:70px; background:#fff4e6; 
      border-radius:50%; display:flex; align-items:center; justify-content:center;
      margin:0 auto 12px auto; font-size:32px; color:#ff9800;">
      📱
    </div>

    <h3 style="margin:6px 0 8px; font-size:19px; font-weight:700; color:#111;">
      Sosial Media Belum Tertaut
    </h3>

    <p style="
      font-size:14px; color:#444; background:#f8f8f8; padding:12px;
      border-radius:10px; margin-bottom:18px;">
      Anda harus menautkan minimal 1 akun sosial media untuk melanjutkan penyewaan.
    </p>

    <button id="btnSosmedNow" style="
      background:#ff9800; color:#fff; width:100%; padding:12px; font-weight:600;
      border:none; border-radius:10px; cursor:pointer; margin-bottom:10px;">
      Hubungkan Sekarang
    </button>

  </div>
</div>


    </form>
  </div>
</div>

{{-- Modal Driver --}}
<div id="driverModal" class="modal-overlay">
  <div class="modal-box">
    <button class="close-btn" onclick="closeDriverModal()">×</button>
    <img id="modalDriverFoto" src="">
    <h3 id="modalDriverNama"></h3>
    <p id="modalDriverHarga"></p>
    <p id="modalDriverLokasi"></p>
    <p id="modalDriverPengalaman"></p>
    <p id="modalDriverDeskripsi"></p>
  </div>

</div>

<script>
window.addEventListener('load', () => {
  const mulai = document.getElementById('tanggal_mulai');
  const selesai = document.getElementById('tanggal_selesai');
  const priceBox = document.querySelector('.price-box p');
  const hargaPerJam = {{ $car->harga_sewa_per_jam }};

  // 🕓 Set minimal waktu mulai
  const now = new Date();
  const adjustedNow = new Date(now.getTime() + 5 * 60000);
  const localNow = new Date(adjustedNow.getTime() - adjustedNow.getTimezoneOffset() * 60000)
                    .toISOString().slice(0,16);

  // ✅ Fix error null
  if (mulai && selesai) {
      mulai.min = localNow;
      selesai.min = localNow;
  }

  mulai?.addEventListener('change', () => {
    const startDate = new Date(mulai.value);
    if (isNaN(startDate)) return;

    const minEndDate = new Date(startDate.getTime() + 6 * 60 * 60 * 1000);
    selesai.min = minEndDate.toISOString().slice(0,16);

    if (selesai.value && new Date(selesai.value) < startDate) selesai.value = '';
    hitungTotal();
  });

  function hitungTotal() {
    if (!mulai?.value || !selesai?.value) return;
    const start = new Date(mulai.value);
    const end = new Date(selesai.value);
    const diff = (end - start) / (1000 * 60 * 60);

    if (diff <= 0) {
      priceBox.innerHTML = `<strong>⚠️ Jam selesai harus lebih besar dari jam mulai</strong>`;
      return;
    }

    if (diff + 0.01 < 6) {
      priceBox.innerHTML = `<strong>❗Minimal sewa 6 jam</strong>`;
      return;
    }

    const total = hargaPerJam * diff;
    priceBox.innerHTML = `<strong>Total Estimasi:</strong> Rp${total.toLocaleString('id-ID')}<br>
      <small>(${diff.toFixed(1)} jam × Rp${hargaPerJam.toLocaleString('id-ID')}/jam)</small>`;
  }

  mulai?.addEventListener('change', hitungTotal);
  selesai?.addEventListener('change', hitungTotal);

  /* === Driver Dropdown === */
  window.toggleDropdown = ()=>document.getElementById('driverDropdown').classList.toggle('open');
  window.selectDriver = el=>{
    document.querySelector('#driverDropdown .selected span').innerHTML =
      `<img src="${el.dataset.foto}" style="width:30px;height:30px;border-radius:8px;margin-right:6px;"> ${el.dataset.nama}`;
    document.getElementById('driver_id').value = el.dataset.id;
    document.getElementById('driverSelected').style.display = 'block';
    document.getElementById('driverDropdown').classList.remove('open');
  };
  window.toggleDriverList = sel=>document.getElementById('driverList').classList.toggle('show', sel.value==='ya');

  /* === Driver Modal === */
  window.showDriverDetail = ()=>{
    const id=document.getElementById('driver_id').value;
    const el=document.querySelector(`.option[data-id="${id}"]`);
    if (!el) return;
    document.getElementById('driverModal').style.display='flex';
    document.getElementById('modalDriverFoto').src=el.dataset.foto;
    document.getElementById('modalDriverNama').textContent=el.dataset.nama;
    document.getElementById('modalDriverHarga').textContent='💰 Rp'+el.dataset.harga+'/jam';
    document.getElementById('modalDriverLokasi').textContent='📍 '+el.dataset.lokasi;
    document.getElementById('modalDriverPengalaman').textContent='🕓 '+el.dataset.pengalaman;
    document.getElementById('modalDriverDeskripsi').textContent=el.dataset.deskripsi;
  };
  window.closeDriverModal=()=>document.getElementById('driverModal').style.display='none';

  /* === Pickup Logic === */
  const metodePickup = document.getElementById('metode_pickup');
  const lokasiRental = document.getElementById('lokasiRental');
  const alamatUser = document.getElementById('alamatUser');
  const cekOngkirBtn = document.getElementById('cekOngkirBtn');
  const hasilOngkir = document.getElementById('hasilOngkir');

  function togglePickup() {
    lokasiRental.style.display = metodePickup.value === 'ambil_sendiri' ? 'block' : 'none';
    alamatUser.style.display   = metodePickup.value === 'pickup_alamat' ? 'block' : 'none';
    if (metodePickup.value !== 'pickup_alamat') hasilOngkir.textContent = '';
  }
  metodePickup.addEventListener('change', togglePickup);
  togglePickup();

  // Cek Ongkir
  cekOngkirBtn?.addEventListener('click', async () => {
    const alamat = document.getElementById('alamat').value.trim();
    if (!alamat) {
      alert('Masukkan alamat Anda terlebih dahulu.');
      return;
    }
    hasilOngkir.textContent = 'Menghitung jarak...';
    
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
  });
  
 // === Modal Verify Elements ===
const modalVerify       = document.getElementById('modalVerifikasi');
const btnUnggahNow      = document.getElementById('btnUnggahNow');
const btnCloseVerify    = document.getElementById('btnCloseVerify');
const formRental        = document.getElementById('rentalForm'); // ✅ harus ada

// ✅ Jika server kirim session('warning') → tampilkan modal
@if(session('warning'))
setTimeout(() => {
    if (modalVerify) modalVerify.style.display = "flex";
}, 300);
@endif

// ✅ Tombol "Unggah Sekarang"
btnUnggahNow.onclick = () => {
    window.location.href = "{{ route('user.verifikasi.index') }}";
};

// ✅ Tombol tutup modal
btnCloseVerify.onclick = () => {
    modalVerify.style.display = "none";
};
// === Modal Sosmed ===
const modalSosmed    = document.getElementById('modalSosmed');
const btnSosmedNow   = document.getElementById('btnSosmedNow');
const btnCloseSosmed = document.getElementById('btnCloseSosmed');

// Jika server kirim session('warning_sosmed') → tampilkan modal
@if(session('warning_sosmed'))
setTimeout(() => {
    if (modalSosmed) modalSosmed.style.display = "flex";
}, 300);
@endif

// Tombol ke halaman sosial media
btnSosmedNow.onclick = () => {
    window.location.href = "{{ route('user.social.index') }}";
};

// Tutup modal
btnCloseSosmed.onclick = () => {
    modalSosmed.style.display = "none";
};

});

</script>
  @include('partials.bottom-navbar')
@endsection
