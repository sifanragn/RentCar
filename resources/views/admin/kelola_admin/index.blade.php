<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        a, button {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        a { background: #007bff; color: white; }
        a:hover { background: #0056b3; }
        button { background: #dc3545; color: white; }
        button:hover { background: #b52b39; }
        .header {
            display: flex; justify-content: space-between; align-items: center;
        }
        .logout-btn {
            background: gray; padding: 5px 10px; color: white; border-radius: 5px; text-decoration: none;
        }
        .logout-btn:hover { background: #333; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Kelola Admin</h2>
    </div>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('admin_role') === 'superadmin')
        <a href="{{ route('admin.manage.create') }}">Tambah Admin Baru</a>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $admin)
            <tr>
                <td>{{ $admin->nama_lengkap }}</td>
                <td>{{ $admin->username }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ ucfirst($admin->role) }}</td>
                <td>{{ ucfirst($admin->status) }}</td>
                <td>
                    @if(session('admin_role') === 'superadmin' && $admin->status === 'aktif')
                        <form action="{{ route('admin.manage.deactivate', $admin->admin_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Nonaktifkan</button>
                        </form>
                    @endif

                    @if(session('admin_role') === 'superadmin')
                        <a href="{{ route('admin.manage.edit', $admin->admin_id) }}">Edit</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Belum ada data admin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
