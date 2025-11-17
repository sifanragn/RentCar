<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVerifikasiController extends Controller
{
    /**
     * Menampilkan halaman upload dokumen KTP & KK
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // Redirect default ke halaman status verifikasi
        $redirectTo = $request->input('redirect_to') ?? route('user.verifikasi.index');

        return view('user.upload.index', compact('user', 'redirectTo'));
    }

    /**
     * Menyimpan hasil upload dokumen ke storage & update status user
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'kk'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload file KTP
        if ($request->hasFile('ktp')) {
            $ktpPath = $request->file('ktp')->store('dokumen', 'public');
            $user->foto_ktp = $ktpPath;
        }

        // Upload file KK
        if ($request->hasFile('kk')) {
            $kkPath = $request->file('kk')->store('dokumen', 'public');
            $user->foto_kk = $kkPath;
        }

        // Update status verifikasi
        $user->status_verifikasi = 'menunggu';
        $user->save();

        // Redirect kembali ke halaman status verifikasi
        $redirect = $request->input('redirect_to', route('user.verifikasi.index'));
        return redirect($redirect)->with('success', '📤 Dokumen berhasil diupload. Tunggu konfirmasi admin.');
    }
}
