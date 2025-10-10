<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - RentCar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f8fa;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            margin-bottom: 10px;
            color: #333;
        }
        p {
            color: #555;
        }
        .btn-container {
            margin-top: 25px;
            display: flex;
            gap: 15px;
        }
        a.btn, button.logout-btn {
            display: inline-block;
            text-decoration: none;
            background: #2c3e50;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }
        a.btn:hover, button.logout-btn:hover {
            background: #1a242f;
        }
        .logout-btn {
            background: #e74c3c;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Selamat datang, {{ session('admin_name') }}</h2>
        <p><strong>Role:</strong> {{ ucfirst(session('admin_role')) }}</p>

        <div class="btn-container">
            @if(session('admin_role') === 'superadmin')
                <a href="{{ route('admin.manage.index') }}" class="btn">Kelola Admin</a>
            @endif
            <a href="{{ route('cars.index') }}" class="btn">Kelola Mobil</a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="margin-top:30px;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
