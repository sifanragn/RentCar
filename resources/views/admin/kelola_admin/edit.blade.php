<h2>Edit Data Admin</h2>

<form method="POST" action="{{ route('admin.manage.update', $admin->admin_id) }}">
    @csrf
    <input type="text" name="nama_lengkap" value="{{ $admin->nama_lengkap }}" required>
    <input type="email" name="email" value="{{ $admin->email }}" required>
    <input type="text" name="no_hp" value="{{ $admin->no_hp }}" required>
    <button type="submit">Perbarui</button>
</form>
