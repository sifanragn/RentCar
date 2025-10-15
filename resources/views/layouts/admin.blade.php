<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Panel')</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
      margin: 0;
      background: #f4f6f9;
      display: flex;
      min-height: 100vh;
    }

    /* === SIDEBAR === */
    .sidebar {
      width: 240px;
      background: #0d6efd;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 25px 20px;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 25px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: background 0.25s, padding-left 0.2s;
      font-weight: 500;
    }
    .sidebar a:hover {
      background: #0b5ed7;
      padding-left: 18px;
    }
    .sidebar a.active {
      background: #084298;
      font-weight: 600;
    }
    .sidebar a span.icon {
      width: 22px;
      text-align: center;
    }

    /* === HEADER === */
    .header {
      background: white;
      padding: 15px 25px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .header h1 {
      font-size: 20px;
      color: #333;
      margin: 0;
      font-weight: 600;
    }
    .header button {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 8px 14px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .header button:hover { background: #bb2d3b; }

    /* === CONTENT === */
    .content {
      flex: 1;
      padding: 25px;
    }

    /* === TABLE === */
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,.06);
    }
    th, td {
      padding: 12px 14px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #0d6efd;
      color: white;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: .3px;
    }

    /* === ALERTS === */
    .alert {
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }
    .alert-success { background: #d1e7dd; color: #0f5132; }
    .alert-danger  { background: #f8d7da; color: #842029; }
    .alert-warning { background: #fff3cd; color: #664d03; }

    /* === BUTTONS === */
    .btn {
      display: inline-block;
      padding: 8px 12px;
      border-radius: 6px;
      text-decoration: none;
      color: white;
      background: #0d6efd;
      border: none;
      cursor: pointer;
      transition: background .2s ease;
      font-size: 14px;
    }
    .btn:hover { background: #0b5ed7; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #b02a37; }

    /* === RESPONSIVE === */
    @media (max-width: 900px) {
      .sidebar {
        width: 65px;
        padding: 15px 10px;
        align-items: center;
      }
      .sidebar h2 { display: none; }
      .sidebar a { justify-content: center; padding: 10px; }
      .sidebar a span.text { display: none; }
      .content { padding: 15px; }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
      <span class="icon">🏠</span> <span class="text">Dashboard</span>
    </a>

    <a href="{{ route('cars.index') }}" class="{{ request()->routeIs('cars.*') ? 'active' : '' }}">
      <span class="icon">🚘</span> <span class="text">Data Mobil</span>
    </a>

    <a href="{{ route('admin.rentals.index') }}" class="{{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">
      <span class="icon">🧾</span> <span class="text">Penyewaan</span>
    </a>

    <a href="{{ route('admin.invoices.index') }}" class="{{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
      <span class="icon">📄</span> <span class="text">Invoice</span>
    </a>

    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <span class="icon">👥</span> <span class="text">User</span>
    </a>

    <a href="{{ route('admin.manage.index') }}" class="{{ request()->routeIs('admin.manage.*') ? 'active' : '' }}">
      <span class="icon">🛠️</span> <span class="text">Kelola Admin</span>
    </a>

    <a href="{{ route('admin.payments.refresh', 1) }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
      <span class="icon">💰</span> <span class="text">Refresh Pembayaran</span>
    </a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
      @csrf
      <button type="submit" style="margin-top:15px;width:100%;padding:10px 0;border-radius:8px;font-weight:600;">
        🚪 Keluar
      </button>
    </form>
  </div>

  <div style="flex:1;display:flex;flex-direction:column;">
    <div class="header">
      <h1>@yield('page_title', 'Dashboard')</h1>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
      </form>
    </div>

    <div class="content">
      @yield('content')
    </div>
  </div>

</body>
</html>