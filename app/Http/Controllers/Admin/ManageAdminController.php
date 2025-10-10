<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageAdminController extends Controller
{
    public function __construct()
{
    // ❗ jangan blokir artisan command (seperti route:list)
    if (app()->runningInConsole()) {
        return;
    }

    if (!session('admin_logged_in')) {
        abort(403, 'Akses ditolak');
    }
}


    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();
        return view('admin.kelola_admin.index', compact('admins'));
    }

    public function create()
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menambah admin baru.');
        }

        return view('admin.kelola_admin.create');
    }

    public function store(Request $request)
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'username' => 'required|unique:admins',
            'email' => 'required|email|unique:admins',
            'password' => 'required|confirmed|min:6',
            'nama_lengkap' => 'required',
            'no_hp' => 'required'
        ]);

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        return redirect()->route('admin.manage.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengedit admin.');
        }

        $admin = Admin::findOrFail($id);
        return view('admin.kelola_admin.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        $admin = Admin::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:admins,email,' . $admin->admin_id . ',admin_id',
            'nama_lengkap' => 'required',
            'no_hp' => 'required'
        ]);

        $admin->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.manage.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function deactivate($id)
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menonaktifkan admin.');
        }

        $admin = Admin::findOrFail($id);
        $admin->update(['status' => 'nonaktif']);

        return back()->with('success', 'Admin dinonaktifkan.');
    }
}
