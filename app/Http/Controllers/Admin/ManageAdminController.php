<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageAdminController extends Controller
{
    // Constructor untuk membatasi akses hanya admin yang sudah login
    public function __construct()
    {
        // ❗ Jangan blokir artisan command (misal: route:list)
        if (app()->runningInConsole()) {
            return;
        }

        // Cek apakah admin sudah login
        if (!session('admin_logged_in')) {
            abort(403, 'Akses ditolak');
        }
    }

    // Menampilkan daftar semua admin
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get(); // urutkan dari terbaru
        return view('admin.kelola_admin.index', compact('admins'));
    }

    // Menampilkan form tambah admin (hanya superadmin)
    public function create()
    {
        // Cek role superadmin
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menambah admin baru.');
        }

        return view('admin.kelola_admin.create');
    }

    // Menyimpan data admin baru
    public function store(Request $request)
    {
        // Hanya superadmin yang boleh menambah admin
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        // ================= VALIDASI INPUT =================
        $request->validate([
            'username' => 'required|unique:admins',
            'email' => 'required|email|unique:admins',
            'password' => 'required|confirmed|min:6',
            'nama_lengkap' => 'required',
            'no_hp' => 'required'
        ]);

        // ================= SIMPAN DATA ADMIN =================
        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // enkripsi password
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        return redirect()->route('admin.manage.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    // Menampilkan form edit admin
    public function edit($id)
    {
        // Hanya superadmin yang boleh edit
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengedit admin.');
        }

        $admin = Admin::findOrFail($id);
        return view('admin.kelola_admin.edit', compact('admin'));
    }

    // Mengupdate data admin
    public function update(Request $request, $id)
    {
        // Hanya superadmin
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        $admin = Admin::findOrFail($id);

        // ================= VALIDASI =================
        $request->validate([
            // unique email kecuali untuk admin yang sedang diedit
            'email' => 'required|email|unique:admins,email,' . $admin->admin_id . ',admin_id',
            'nama_lengkap' => 'required',
            'no_hp' => 'required'
        ]);

        // ================= UPDATE DATA =================
        $admin->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.manage.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    // Menonaktifkan admin (soft action via status)
    public function deactivate($id)
    {
        // Hanya superadmin
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menonaktifkan admin.');
        }

        $admin = Admin::findOrFail($id);

        // Ubah status menjadi nonaktif
        $admin->update(['status' => 'nonaktif']);

        return back()->with('success', 'Admin dinonaktifkan.');
    }
}