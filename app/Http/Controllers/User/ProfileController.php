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
    /**
     * Menampilkan halaman profil user
     */
    public function index()
    {
        // ================= AMBIL DATA USER =================
        $user = Auth::user();

        // ================= TAMPILKAN VIEW =================
        return view('user.profile.index', compact('user'));
    }

    /**
     * Menampilkan halaman edit profil
     * Sekaligus menghitung aturan perubahan username (7 hari)
     */
    public function edit()
    {
        $user = Auth::user();

        // Hitung apakah user boleh mengganti username (>= 7 hari)
        $lastChanged = $user->updated_at ?? Carbon::now()->subDays(8);

        // Hitung selisih hari sejak terakhir perubahan
        $daysSince = $lastChanged->diffInDays(Carbon::now(), false);

        // Boleh ubah jika sudah >= 7 hari
        $canChangeUsername = $daysSince >= 7;

        // Sisa hari jika belum boleh ubah
        $daysLeftToChangeUsername = max(0, ceil(7 - $daysSince));

        return view(
            'user.profile.edit',
            compact('user', 'canChangeUsername', 'daysLeftToChangeUsername')
        );
    }

    /**
     * Memperbarui data profil user
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
        'nama_lengkap' => 'required|string|max:100',
        'email'        => 'required|email',
        'username'     => 'required|string|max:50',
        'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

        // === Username change rules ===
        if ($request->username !== $user->username) {
            $lastChange = $user->last_username_change ?? Carbon::now()->subDays(8);
            $daysSince = $lastChange->diffInDays(Carbon::now());

            if ($daysSince < 7) {
                return back()->with('error', 'Username hanya bisa diubah setiap 7 hari. Tunggu lagi ' . (7 - $daysSince) . ' hari.');
            }

            $user->username = $request->username;
            $user->last_username_change = now();
        }

        // === Update basic fields ===
        $user->nama_lengkap = $request->nama_lengkap;
        $user->email = $request->email;

        // === Password change ===
if ($request->filled('password')) {

    $request->validate([
        'password' => 'required|string|min:6|confirmed',
        'current_password' => 'required|string',
    ]);

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error', 'Password lama salah. Tidak bisa mengubah password.');
    }

    $user->password = Hash::make($request->password);
}

        // === FOTO PROFIL (FIXED) ===
        if ($request->file('foto_profil')) {
            $file = $request->file('foto_profil');

            $path = $file->store('uploads/profil', 'public');

            $user->foto_profil = $path;
        }

        $user->save();

        return redirect()->route('user.profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function hapusVerifikasi($tipe = null)
    {
        $user = Auth::user();

        if ($user->foto_ktp) Storage::disk('public')->delete($user->foto_ktp);
        if ($user->foto_kk) Storage::disk('public')->delete($user->foto_kk);

        // ================= RESET DATA =================
        $user->foto_ktp = null;
        $user->foto_kk = null;
        $user->status_verifikasi = 'belum_upload';
        $user->save();

        return back()->with(
            'warning',
            'Anda telah menghapus data verifikasi. Silakan verifikasi ulang.'
        );
    }

    /**
     * Verifikasi password lama (digunakan untuk AJAX / validasi frontend)
     */
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
