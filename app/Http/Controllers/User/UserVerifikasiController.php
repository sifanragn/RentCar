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
        $redirectTo = $request->input('redirect_to') ?? route('user.profile.index');
        return view('user.verifikasi.index', compact('user', 'redirectTo'));
    }

    public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'ktp' => 'required|image|max:2048',
        'kk' => 'required|image|max:2048',
    ]);

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

    // setelah upload langsung ke halaman edit profil
    return redirect()->route('user.profile.edit')
        ->with('success', 'Dokumen berhasil diupload, tunggu konfirmasi admin.');
}
}
