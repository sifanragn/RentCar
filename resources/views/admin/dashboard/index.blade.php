<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - RentCar</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
</head>
<body>
  <header class="topbar">
    <div class="container">
      <h1>RentCar Admin</h1>

      <div class="right-controls">
        <div class="toggle-wrapper">
          <label class="switch">
            <input type="checkbox" id="modeToggle">
            <span class="slider"></span>
          </label>
          <span class="mode-text">Dark Mode</span>
        </div>

        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn-logout">Logout</button>
        </form>
      </div>
    </div>
  </header>

  <div class="dashboard-container">
  <div class="dashboard-header">
    <h2>Dashboard Admin</h2>
    <p>Kelola seluruh sistem dan data admin dari satu tempat.</p>
  </div>

  <div class="menu-grid">
    <a href="#" class="menu-item">
      <img src="{{ asset('img/icons/car.png') }}" class="menu-icon" alt="">
      <div class="menu-text">
        <h3>Kelola Mobil</h3>
        <p>Tambah, ubah, dan hapus data mobil.</p>
      </div>
    </a>

    <a href="#" class="menu-item">
      <img src="{{ asset('img/icons/rent.png') }}" class="menu-icon" alt="">
      <div class="menu-text">
        <h3>Data Penyewaan</h3>
        <p>Kelola seluruh transaksi penyewaan.</p>
      </div>
    </a>

    <a href="#" class="menu-item">
      <img src="{{ asset('img/icons/invoice.png') }}" class="menu-icon" alt="">
      <div class="menu-text">
        <h3>Invoice</h3>
        <p>Lihat dan kelola tagihan pelanggan.</p>
      </div>
    </a>

    <a href="#" class="menu-item">
      <img src="{{ asset('img/icons/users.png') }}" class="menu-icon" alt="">
      <div class="menu-text">
        <h3>Data User</h3>
        <p>Manajemen akun pelanggan & admin.</p>
      </div>
    </a>
  </div>
</div>

<footer>© 2025 RentCar Admin — All Rights Reserved</footer>


  <script>
    const toggle = document.getElementById("modeToggle");
    const body = document.body;
    const modeText = document.querySelector(".mode-text");

    toggle.addEventListener("change", () => {
      body.classList.toggle("light-mode");
      modeText.textContent = body.classList.contains("light-mode") ? "Light Mode" : "Dark Mode";
    });
  </script>
</body>
</html>
