<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - RentCar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      padding-bottom: 80px;
    }
    .container {
      max-width: 430px;
      margin: auto;
      padding: 25px 15px;
    }
    .profile-header img {
      width: 100px; height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ddd;
    }
    .profile-header {
  background: #000;
  border-radius: 12px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.profile-photo {
  width: 60px;
  height: 60px;
  border-radius: 8px; /* bukan bulat */
  object-fit: cover;
  border: 2px solid #222;
}

.profile-header h5 {
  font-weight: 600;
  color: #fff;
}

.profile-header .username {
  font-size: 13px;
  color: #ccc;
}

    .menu-section { margin-top: 25px; }
    .menu-item {
      background: #fff;
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      cursor: pointer;
      text-align: left;
      transition: transform .2s;
    }
    .menu-item:hover { transform: scale(1.01); }
    .menu-item span { display: block; font-weight: 600; font-size: 15px; color: #111; }
    .menu-item small { color: #777; font-size: 13px; }

    /* ========== RIWAYAT SEWA ========== */
    .rental-history {
      background: #fff;
      border-radius: 10px;
      padding: 16px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      margin-top: 20px;
    }
    .rental-item {
      border-bottom: 1px solid #eee;
      padding: 10px 0;
    }
    .rental-item:last-child { border-bottom: none; }
    .rental-item h6 { margin: 0; font-size: 15px; font-weight: 600; color: #0d6efd; }
    .rental-item small { display: block; color: #555; }
    .status {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 500;
      margin-top: 5px;
    }
    .status.menunggu { background:#fff3cd; color:#856404; }
    .status.berjalan { background:#d1e7dd; color:#0f5132; }
    .status.selesai { background:#cfe2ff; color:#084298; }
    .status.dibatalkan { background:#f8d7da; color:#842029; }

    /* ========== NAV BAWAH ========== */
    .bottom-nav {
      position: fixed; bottom: 0; left: 0; right: 0;
      background: #fff; border-top: 1px solid #ddd;
      display: flex; justify-content: space-around;
      padding: 8px 0;
      box-shadow: 0 -1px 6px rgba(0,0,0,0.1);
    }
    .bottom-nav .nav-item {
      text-align: center;
      color: #555;
      text-decoration: none;
      font-size: 12px;
    }
    .bottom-nav .nav-item i {
      font-size: 18px;
      display: block;
    }
    .bottom-nav .active { color: #0d6efd; }
    footer small { font-size: 12px; color: #888; }
  </style>
</head>
<body>

  <div class="container text-center">
    {{-- HEADER PROFILE --}}
    <div class="profile-header d-flex align-items-center justify-content-start text-start">
  <img src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : asset('img/default-user.png') }}" alt="Foto Profil" class="profile-photo">
  <div class="ms-3">
    <h5 class="mb-0 text-white">{{ $user->nama_lengkap }}</h5>
    <p class="mb-0 username text-light opacity-75">{{ '@' . $user->username }}</p>
  </div>
</div>


    {{-- MENU UTAMA --}}
    <div class="menu-section mt-4">
      <div class="menu-item" onclick="window.location='{{ route('user.profile.edit') }}'">
        <span>Edit Profil</span>
        <small>Perbarui data akun Anda</small>
      </div>
      <div class="menu-item" onclick="window.location='{{ route('user.payments.index') }}'">
        <span>Transaksi Saya</span>
        <small>Lihat riwayat pembayaran</small>
      </div>
    </div>

    <div class="menu-item" onclick="window.location='{{ route('user.rentals.index') }}'">
    <span>Riwayat Sewa Mobil</span>
    <small>Lihat daftar penyewaan Anda</small>
  </div>

    {{-- MENU LAIN --}}
    <div class="menu-section mt-4">
      <div class="menu-item" onclick="window.location='{{ route('user.kontak.index') }}'">
        <span>Hubungi Kami</span>
        <small>Punya pertanyaan?</small>
      </div>
      <div class="menu-item" onclick="window.location='{{ url('/tentang-kami') }}'">
        <span>Tentang Kami</span>
        <small>Kenali RentCar lebih jauh</small>
      </div>
      <div class="menu-item" onclick="confirmLogout()">
        <span>Log Out</span>
        <small>Keluar dari akun</small>
      </div>
    </div>

    <footer class="text-center mt-4">
      <img src="{{ asset('img/logo-rental.png') }}" width="100" alt="Logo RentCar"><br>
      <small>© 2025 RentCar</small>
    </footer>
  </div>

  {{-- NAVIGASI BAWAH --}}
  <div class="bottom-nav">
    <a href="{{ route('user.dashboard') }}" class="nav-item"><i class="bi bi-house"></i><span>Home</span></a>
    <a href="{{ route('user.rentals.index') }}" class="nav-item"><i class="bi bi-clock-history"></i><span>Riwayat</span></a>
    <a href="{{ route('user.cars.index') }}" class="nav-item center"><i class="bi bi-car-front-fill"></i><span>Mobil</span></a>
    <a href="{{ route('user.kontak.index') }}" class="nav-item"><i class="bi bi-telephone"></i><span>Kontak</span></a>
    <a href="{{ route('user.profile.index') }}" class="nav-item active"><i class="bi bi-person-circle"></i><span>Profil</span></a>
  </div>

  

  <script>
    function confirmLogout() {
      if (confirm('Yakin ingin keluar dari akun?')) {
        document.getElementById('logoutForm').submit();
      }
    }
  </script>
  <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
</body>
</html>
