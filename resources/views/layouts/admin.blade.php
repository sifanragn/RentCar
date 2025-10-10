<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Panel')</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      background: #f4f6f9;
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 230px;
      background: #0d6efd;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 20px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 6px;
      margin-bottom: 5px;
      display: block;
      transition: background 0.2s ease;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #0b5ed7;
    }

    /* Header */
    .header {
      background: white;
      padding: 15px 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .header h1 { font-size: 20px; color: #333; margin: 0; }
    .header .logout-form {
      display: inline;
    }
    .header button {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 8px 12px;
      cursor: pointer;
    }
    .header button:hover { background: #bb2d3b; }

    /* Main Content */
    .content {
      flex: 1;
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #0d6efd;
      color: white;
    }

    .alert {
      background: #d1e7dd;
      color: #0f5132;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">🏠 Dashboard</a>
    <a href="{{ route('cars.index') }}" class="{{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">🚘 Data Mobil</a>
    <a href="{{ route('admin.rentals.index') }}" class="{{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">🧾 Penyewaan</a>
    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👥 Verifikasi User</a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
      @csrf
      <button type="submit" style="margin-top:20px;width:100%;">Keluar</button>
    </form>
  </div>

  <div style="flex:1;display:flex;flex-direction:column;">
    <div class="header">
      <h1>@yield('page_title', 'Dashboard')</h1>
    </div>
    <div class="content">
      @yield('content')
    </div>
  </div>

</body>
</html>
