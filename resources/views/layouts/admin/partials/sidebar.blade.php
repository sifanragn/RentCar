<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <h2>RentCar</h2>

    {{-- Tombol collapse / buka sidebar --}}
    <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>

  <nav>
    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard.index') }}" 
       class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>

    {{-- Daftar Mobil --}}
    <a href="{{ route('cars.index') }}" 
       class="{{ request()->routeIs('cars.*') ? 'active' : '' }}">
      <i class="bi bi-car-front"></i>
      <span>Daftar Mobil</span>
    </a>

    {{-- Penyewaan --}}
    <a href="{{ route('admin.rentals.index') }}" 
       class="{{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">
      <i class="bi bi-calendar-check"></i>
      <span>Penyewaan</span>
    </a>

    {{-- Pengguna --}}
    <a href="{{ route('admin.users.index') }}" 
       class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i class="bi bi-people"></i>
      <span>Pengguna</span>
    </a>

    {{-- Laporan --}}
    <a href="{{ route('admin.laporan.index') }}" 
       class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
      <i class="bi bi-graph-up"></i>
      <span>Laporan</span>
    </a>

    {{-- Invoice --}}
    
  </nav>
</aside>
