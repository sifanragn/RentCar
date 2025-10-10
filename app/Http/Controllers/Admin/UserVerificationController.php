<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserVerificationController extends Controller
{
    /**
     * 🧾 Menampilkan daftar user yang butuh verifikasi
     */
    public function index()
    {
        $users = User::where('role', 'user')
            ->orderByRaw("FIELD(status_verifikasi, 'menunggu', 'disetujui', 'ditolak', 'belum_upload')")
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * 📄 Lihat detail user dan dokumennya
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * ✅ Verifikasi atau ❌ Tolak user
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
        ]);

        $user = User::findOrFail($id);
        $user->status_verifikasi = $request->status_verifikasi;
        $user->save();

        $pesan = $request->status_verifikasi === 'disetujui'
            ? '✅ Akun berhasil diverifikasi dan bisa melakukan penyewaan.'
            : '❌ Akun ditolak. User tidak dapat melakukan penyewaan.';

        return redirect()->route('admin.users.index')->with('success', $pesan);
    }
}
