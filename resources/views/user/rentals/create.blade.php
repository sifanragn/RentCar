<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Form Penyewaan Mobil</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif; background:#f8f9fa; margin:0; padding:20px; }
    .card { background:white; border-radius:10px; padding:20px; max-width:600px; margin:auto; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { color:#333; margin-bottom:20px; }
    label { display:block; margin-top:10px; font-weight:600; color:#333; }
    input, select { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; margin-top:5px; }
    button { background:#0d6efd; color:white; padding:10px 14px; border:none; border-radius:6px; margin-top:15px; cursor:pointer; transition:all .3s; }
    button:hover { background:#0b5ed7; transform:scale(1.02); }
    .alert { background:#fff3cd; color:#856404; padding:10px; border-radius:6px; margin-bottom:15px; }
    .price-box { background:#e9ecef; padding:10px; border-radius:6px; margin-top:10px; }
    #popup-menunggu { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); justify-content:center; align-items:center; z-index:9999; }
    #popup-menunggu .popup-content { background:white; border-radius:20px; padding:30px; width:350px; text-align:center; position:relative; animation:fadeIn .4s ease; }
    #popup-menunggu button.close-btn { position:absolute; top:10px; right:15px; border:none; background:none; font-size:20px; cursor:pointer; }
    @keyframes spin { 100% { transform: rotate(360deg); } }
    @keyframes fadeIn { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
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

      <button type="submit">Ajukan Penyewaan</button>
    </form>

    <a href="{{ route('user.cars.show', $car->car_id) }}" style="display:inline-block; margin-top:15px; color:#0d6efd; text-decoration:none;">← Kembali</a>
  </div>

  {{-- Popup jika belum verifikasi --}}
  <div id="popup-menunggu">
    <div class="popup-content">
      <button class="close-btn" id="close-popup">✖</button>
      <div style="font-size:40px; animation:spin 1s linear infinite;">⏳</div>
      <img src="{{ asset('img/waiting-illustration.png') }}" alt="Menunggu" style="width:150px; margin:10px auto;">
      <h3>Belum Diverifikasi</h3>
      <p>Anda harus mengunggah KTP & KK terlebih dahulu sebelum melanjutkan penyewaan.</p>
      <a href="{{ route('user.profile') }}" style="display:inline-block;background:#0d6efd;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none;">Unggah Sekarang</a>
    </div>
  </div>

  <script>
    const form  = document.getElementById('rentalForm');
    const popup = document.getElementById('popup-menunggu');
    const csrf  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const isVerified = "{{ auth()->user()->status_verifikasi }}" === "disetujui";

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      // 🔹 Jika belum terverifikasi, tampilkan popup
      if (!isVerified) {
        popup.style.display = 'flex';
        return;
      }

      // 🔹 Jika sudah terverifikasi, langsung ke proses pembayaran
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
          window.location.href = data.redirect_url; // langsung ke halaman payment gateway
        } else {
          alert(data.message || 'Terjadi kesalahan.');
        }
      })
      .catch(() => {
        alert('Terjadi kesalahan koneksi.');
      });
    });
  </script>

</body>
</html>
