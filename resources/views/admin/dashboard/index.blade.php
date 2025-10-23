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

  {{-- GALERI MOBIL SHOWCASE --}}
<div class="car-gallery-showcase">
  <h2 class="gallery-title">Cars Gallery</h2>

  <div class="car-scroll-wrapper" id="carScrollWrapper">
    <div class="car-scroll-track">
      @foreach($cars as $car)
        <div class="car-card">
          <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->nama_mobil }}">
          <div class="car-info">
            <h3>{{ $car->nama_mobil }}</h3>
            <p>{{ $car->brand->nama_merek ?? '-' }}</p>
          </div>
        </div>
      @endforeach
      {{-- Duplikat buat efek infinite loop --}}
      @foreach($cars as $car)
        <div class="car-card">
          <img src="{{ asset('storage/' . $car->foto) }}" alt="{{ $car->nama_mobil }}">
          <div class="car-info">
            <h3>{{ $car->nama_mobil }}</h3>
            <p>{{ $car->brand->nama_merek ?? '-' }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>


  {{-- MENU DASHBOARD --}}
  <div class="menu-grid">
    @if(session('admin_role') === 'superadmin')
      <a href="{{ route('admin.manage.index') }}" class="menu-item">
        <img src="{{ asset('img/icons/manage.png') }}" class="menu-icon" alt="Kelola Admin">
        <div class="menu-text">
          <h3>Kelola Admin</h3>
          <p>Tambah, ubah, dan nonaktifkan akun admin.</p>
        </div>
      </a>
    @endif

<a href="{{ route('admin.cars.index') }}" class="menu-item">
      <img src="{{ asset('img/icons/car.png') }}" class="menu-icon" alt="Kelola Mobil">
      <div class="menu-text">
        <h3>Kelola Mobil</h3>
        <p>Tambah, ubah, dan hapus data mobil.</p>
      </div>
    </a>

    <a href="{{ route('admin.rentals.index') }}" class="menu-item">
      <img src="{{ asset('img/icons/rent.png') }}" class="menu-icon" alt="Data Penyewaan">
      <div class="menu-text">
        <h3>Data Penyewaan</h3>
        <p>Kelola seluruh transaksi penyewaan mobil.</p>
      </div>
    </a>

    <a href="{{ route('admin.invoices.index') }}" class="menu-item">
      <img src="{{ asset('img/icons/invoice.png') }}" class="menu-icon" alt="Invoice">
      <div class="menu-text">
        <h3>Invoice</h3>
        <p>Lihat, buat, dan kelola tagihan pelanggan.</p>
      </div>
    </a>

    <a href="{{ route('admin.users.index') }}" class="menu-item">
      <img src="{{ asset('img/icons/users.png') }}" class="menu-icon" alt="Data User">
      <div class="menu-text">
        <h3>Data User</h3>
        <p>Verifikasi dan kelola akun pelanggan.</p>
      </div>
    </a>
  </div>
</div>

  <footer>© {{ date('Y') }} RentCar Admin — All Rights Reserved</footer>

  <script>
    const toggle = document.getElementById("modeToggle");
    const body = document.body;
    const modeText = document.querySelector(".mode-text");

    toggle.addEventListener("change", () => {
      body.classList.toggle("light-mode");
      modeText.textContent = body.classList.contains("light-mode") ? "Light Mode" : "Dark Mode";
    });

    // Scroll horizontal drag effect
const scrollContainer = document.querySelector('.car-scroll-container');
let isDown = false;
let startX;
let scrollLeft;

scrollContainer.addEventListener('mousedown', e => {
  isDown = true;
  startX = e.pageX - scrollContainer.offsetLeft;
  scrollLeft = scrollContainer.scrollLeft;
});
scrollContainer.addEventListener('mouseleave', () => isDown = false);
scrollContainer.addEventListener('mouseup', () => isDown = false);
scrollContainer.addEventListener('mousemove', e => {
  if(!isDown) return;
  e.preventDefault();
  const x = e.pageX - scrollContainer.offsetLeft;
  const walk = (x - startX) * 1.4; // speed factor
  scrollContainer.scrollLeft = scrollLeft - walk;
});

  </script>
  
</body>
</html>
