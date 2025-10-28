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

   <!-- ========================= DRIVER SECTION ========================= -->
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

.driver-card {
  display: flex;
  align-items: center;
  gap: 15px;
  background: #f9f9f9;
  border-radius: 10px;
  padding: 12px;
  transition: 0.3s;
  cursor: pointer;
  border: 1px solid transparent;
}
.driver-card:hover {
  background: #eef6ff;
  border-color: #0d6efd;
}

.driver-card img {
  width: 65px;
  height: 65px;
  border-radius: 10px;
  object-fit: cover;
}

.driver-info {
  flex: 1;
}

.driver-info h4 {
  margin: 0;
  color: #333;
  font-size: 16px;
  font-weight: 600;
}

.driver-info small {
  color: #666;
  font-size: 13px;
}

.driver-selected {
  display: none;
  margin-top: 12px;
  padding: 12px;
  border-radius: 8px;
  background: #eaf6ff;
  border-left: 4px solid #0d6efd;
}

.driver-selected strong {
  color: #0d6efd;
}

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

@keyframes fadeIn {
  from {opacity:0; transform:translateY(20px);}
  to {opacity:1; transform:translateY(0);}
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
      <a href="{{ route('user.verifikasi.index') }}" style="color:#0d6efd;">Klik di sini untuk verifikasi sekarang</a>
    </div>
  @endif

 {{-- Harga mobil --}}
<h3>{{ $car->brand->nama_merek ?? '-' }} {{ $car->model }}</h3>
<p><strong>Harga per jam:</strong> Rp{{ number_format($car->harga_sewa_per_jam ?? 0, 0, ',', '.') }}</p>

  <form id="rentalForm" action="{{ route('user.rentals.store', $car->car_id) }}" method="POST">
    @csrf

    {{-- 🔹 input tanggal + jam mulai --}}
    <label for="tanggal_mulai">Tanggal & Jam Mulai</label>
    <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" required 
          placeholder="Pilih tanggal dan jam mulai (min 6 jam)">
    <small style="color:#555;">Minimal sewa 6 jam.</small>

    <label for="tanggal_selesai">Tanggal & Jam Selesai</label>
    <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" required 
          placeholder="Pilih tanggal dan jam selesai">
          
    <div class="driver-section">
  <label for="driver">Butuh Driver?</label>
  <select name="driver" id="driver" required onchange="toggleDriverList(this)">
    <option value="tidak">Tidak</option>
    <option value="ya">Ya (+ otomatis sesuai tarif driver)</option>
  </select>

  <!-- Pilih driver -->
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
        data-harga="{{ number_format($driver->harga_per_jam ?? 0, 0, ',', '.') }}"
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

    <label for="metode_pickup">Metode Pengambilan</label>
    <select name="metode_pickup" id="metode_pickup" required>
      <option value="">-- Pilih Metode --</option>
      <option value="ambil_sendiri">Ambil di Tempat</option>
      <option value="pickup_alamat">Antar ke Alamat Saya</option>
    </select>

    <!-- Lokasi rental -->
    <div id="lokasiRental" style="display:none; margin-top:10px;">
      <p>📍 Lokasi Rental Kami:</p>
      <p><strong>Hexagon Inc</strong></p>
      <a href="https://maps.app.goo.gl/A2nueYrYoqiqduJs8" target="_blank"
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
    <a href="{{ route('user.verifikasi.index') }}"
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
<script>
function toggleDriverList(sel) {
  const list = document.getElementById('driverList');
  const selected = document.getElementById('driver_id');
// lalu ambil opt.dataset.harga untuk ditampilkan
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
  document.getElementById('modalDriverHarga').textContent = '💰 Tarif: Rp' + opt.dataset.harga + ' /jam';
  document.getElementById('modalDriverPengalaman').textContent = '🕓 Pengalaman: ' + opt.dataset.pengalaman;
  document.getElementById('modalDriverDeskripsi').textContent = opt.dataset.deskripsi;
}

function closeDriverModal() {
  document.getElementById('driverModal').style.display = 'none';
}
</script>
</body>
</html>
