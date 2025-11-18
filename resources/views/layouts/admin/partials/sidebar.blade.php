<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <h2>RentCar</h2>
    <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>

  {{-- 💰 Total Pendapatan --}}
  @php
    $penambahan = $penambahan ?? 0;
    $totalPendapatan = $totalPendapatan ?? 0;
  @endphp

  <div class="sidebar-stats {{ $penambahan >= 0 ? 'up' : 'down' }}">
    <p class="stats-label">Total Pendapatan</p>
    <h4 class="stats-value">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</h4>

    @if($penambahan != 0)
      <span class="stats-growth">
        {{ $penambahan >= 0 ? '+' : '-' }}Rp{{ number_format(abs($penambahan), 0, ',', '.') }}
        {{ $penambahan >= 0 ? '↑' : '↓' }}
      </span>
    @endif
  </div>

  <nav>
    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>

    {{-- 🔽 Dropdown Mobil --}}
    <div class="nav-dropdown {{ request()->routeIs('admin.cars.*') ? 'open' : '' }}">
      <button class="dropdown-toggle">
        <i class="bi bi-car-front"></i>
        <span>Mobil</span>
        <i class="bi bi-chevron-down arrow"></i>
      </button>
      <div class="dropdown-inner">
        <a href="{{ route('admin.cars.brands') }}">Merek Mobil</a>
        <a href="{{ route('admin.cars.models') }}">Model Mobil</a>
        <a href="{{ route('admin.cars.index') }}">Daftar Mobil</a>
      </div>
    </div>

    {{-- 🚗 Drivers --}}
    <div class="nav-dropdown {{ request()->routeIs('admin.drivers.*') ? 'open' : '' }}">
      <button class="dropdown-toggle">
        <i class="bi bi-person-badge"></i>
        <span>Drivers</span>
        <i class="bi bi-chevron-down arrow"></i>
      </button>
      <div class="dropdown-inner">
        <a href="{{ route('admin.drivers.index') }}">Daftar Driver</a>
        <a href="{{ route('admin.drivers.create') }}">Tambah Driver</a>
      </div>
    </div>

    {{-- Penyewaan --}}
    <a href="{{ route('admin.rentals.index') }}" class="{{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">
      <i class="bi bi-calendar-check"></i><span>Penyewaan</span>
    </a>

    {{-- Pengguna --}}
    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i class="bi bi-people"></i><span>Pengguna</span>
    </a>

    {{-- Laporan --}}
    <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
      <i class="bi bi-graph-up"></i><span>Laporan</span>
    </a>

    {{-- Invoice --}}
    <a href="{{ route('admin.invoices.index') }}" class="{{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
      <i class="bi bi-receipt"></i><span>Invoice</span>
    </a>

     {{-- 🚨 Nomor Darurat --}}
    <a href="{{ route('admin.emergency.index') }}" class="{{ request()->routeIs('admin.emergency.*') ? 'active' : '' }}">
      <i class="bi bi-telephone-outbound"></i><span>Nomor Darurat</span>
    </a>

    <a href="{{ route('admin.social.index') }}" class="{{ request()->routeIs('admin.social-media.*') ? 'active' : '' }}">
      <i class="bi bi-share"></i><span>Social Media</span>
    </a>

  </nav>
</aside>
