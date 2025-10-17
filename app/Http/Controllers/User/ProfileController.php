<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();

        // Hitung apakah user boleh mengganti username (>= 7 hari sejak terakhir update)
        $lastChanged = $user->updated_at ?? Carbon::now()->subDays(8);
        $daysSince = $lastChanged->diffInDays(Carbon::now(), false);
        $canChangeUsername = $daysSince >= 7;
        $daysLeftToChangeUsername = max(0, ceil(7 - $daysSince));

        return view('user.profile.edit', compact('user', 'canChangeUsername', 'daysLeftToChangeUsername'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // basic validation for profile fields (password handled separately)
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email',
            'username'     => 'required|string|max:50',
            'current_password' => 'nullable|string',
            'password'     => 'nullable|string|min:6|confirmed', // password_confirmation required if password present
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // --- Username change rules (7 hari) ---
        if ($request->username !== $user->username) {
            $lastChanged = $user->updated_at ?? Carbon::now()->subDays(8);
            $daysSince = $lastChanged->diffInDays(Carbon::now());
            if ($daysSince < 7) {
    return back()->with('error', 'Username hanya bisa diubah setiap 7 hari. Tunggu lagi ' . (7 - $daysSince) . ' hari.');
}

            $user->username = $request->username;
            // Note: if you have a dedicated username_changed_at column, set it here.
        }

        // --- Update other basic fields ---
        $user->nama_lengkap = $request->nama_lengkap;
        $user->email = $request->email;

        // --- Password change: require current_password verification ---
        if ($request->filled('password')) {
            // current_password must be provided
            if (!$request->filled('current_password')) {
                return back()->with('error', 'Untuk mengubah password, masukkan password lama terlebih dahulu.');
            }

            // verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password lama salah. Tidak bisa mengubah password.');
            }

            // all good -> set new password
            $user->password = Hash::make($request->password);
        }

        // --- Foto profil (boleh ganti kapanpun) ---
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = $request->file('foto_profil')->store('uploads/profil', 'public');
        }

        $user->save();

        return redirect()->route('user.profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function hapusVerifikasi($tipe = null)
    {
        $user = Auth::user();

        // jika memang menghapus semua verifikasi
        if ($user->foto_ktp) Storage::disk('public')->delete($user->foto_ktp);
        if ($user->foto_kk)  Storage::disk('public')->delete($user->foto_kk);

        $user->foto_ktp = null;
        $user->foto_kk  = null;
        $user->status_verifikasi = 'belum_upload';
        $user->save();

        return back()->with('warning', 'Anda telah menghapus data verifikasi. Silakan verifikasi ulang.');
    }

    public function verifyPassword(Request $request)
{
    $request->validate(['current_password' => 'required|string']);
    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Password lama salah.'], 401);
    }

    return response()->json(['success' => true, 'message' => 'Password terverifikasi.']);
}

}
