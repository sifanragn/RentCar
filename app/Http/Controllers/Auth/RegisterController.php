<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_kk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload KTP & KK (jika ada)
        $pathKtp = $request->hasFile('foto_ktp')
            ? $request->file('foto_ktp')->store('ktp', 'public')
            : null;

        $pathKk = $request->hasFile('foto_kk')
            ? $request->file('foto_kk')->store('kk', 'public')
            : null;

        // Tentukan status verifikasi awal
        $statusVerifikasi = ($pathKtp && $pathKk) ? 'menunggu' : 'belum_upload';

        // Simpan user
        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'foto_ktp' => $pathKtp,
            'foto_kk' => $pathKk,
            'status_verifikasi' => $statusVerifikasi,
            'role' => 'user',
        ]);

        
        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan login.');
    }
}