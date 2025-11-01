<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserVerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Ambil URL asal dari parameter redirect_to, fallback ke profil
        $redirectTo = $request->input('redirect_to') ?? route('user.profile.index');

        return view('user.verifikasi.index', compact('user', 'redirectTo'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'kk'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload file dan simpan path-nya
        if ($request->hasFile('ktp')) {
            $ktpPath = $request->file('ktp')->store('dokumen', 'public');
            $user->foto_ktp = $ktpPath;
        }

        if ($request->hasFile('kk')) {
            $kkPath = $request->file('kk')->store('dokumen', 'public');
            $user->foto_kk = $kkPath;
        }

        $user->status_verifikasi = 'menunggu';
        $user->save();

        // Ambil URL redirect asal
        $redirect = $request->input('redirect_to', route('user.profile.index'));

        // Redirect kembali ke halaman asal + pesan sukses
        return redirect($redirect)
            ->with('success', 'Dokumen berhasil diupload, tunggu konfirmasi admin.');
    }
}
