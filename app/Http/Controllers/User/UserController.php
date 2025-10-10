<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * 🧍 Menampilkan halaman profil user untuk upload KTP & KK
     */
    public function profile()
    {
        $user = Auth::user();
        // arahkan ke folder /user/profile/index.blade.php
        return view('user.profile.index', compact('user'));
    }

    /**
     * 📤 Menyimpan / update dokumen KTP & KK user
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_kk'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto_ktp')) {
            $pathKtp = $request->file('foto_ktp')->store('uploads/ktp', 'public');
            $user->foto_ktp = $pathKtp;
        }

        if ($request->hasFile('foto_kk')) {
            $pathKk = $request->file('foto_kk')->store('uploads/kk', 'public');
            $user->foto_kk = $pathKk;
        }

        // ubah status jadi menunggu verifikasi
        $user->status_verifikasi = 'menunggu';
        $user->save();

        return redirect()->route('user.profile')->with('success', '📩 Dokumen berhasil diunggah! Menunggu verifikasi admin.');
    }
}
