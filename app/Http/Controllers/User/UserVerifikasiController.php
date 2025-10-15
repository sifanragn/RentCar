<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVerifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap halaman asal user, default ke home
        $redirectTo = $request->input('redirect_to') ?? url()->previous();
        return view('user.verifikasi.index', compact('redirectTo'));
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

    // Update status verifikasi supaya notif berubah
    $user->status_verifikasi = 'menunggu';  // atau 'menunggu_konfirmasi' sesuai yang kamu pakai
    $user->save();

    return redirect($request->redirect_to)->with('success', 'Dokumen berhasil diupload, tunggu konfirmasi admin.');
}

}
