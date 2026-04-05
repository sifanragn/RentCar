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

        // ================= CEK BATAS PERUBAHAN USERNAME =================
        // Jika belum pernah diubah, dianggap sudah lewat 7 hari
        $lastChanged = $user->updated_at ?? Carbon::now()->subDays(8);

        // Hitung selisih hari sejak terakhir perubahan
        $daysSince = $lastChanged->diffInDays(Carbon::now(), false);

        // Boleh ubah jika sudah >= 7 hari
        $canChangeUsername = $daysSince >= 7;

        // Sisa hari jika belum boleh ubah
        $daysLeftToChangeUsername = max(0, ceil(7 - $daysSince));

        return view('user.profile.edit', compact(
            'user',
            'canChangeUsername',
            'daysLeftToChangeUsername'
        ));
    }

    /**
     * Memperbarui data profil user
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // ================= VALIDASI INPUT =================
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email',
            'username'     => 'required|string|max:50',
            'current_password' => 'nullable|string',
            'password'     => 'nullable|string|min:6|confirmed',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ================= ATURAN PERUBAHAN USERNAME =================
        if ($request->username !== $user->username) {

            // Ambil waktu terakhir perubahan username
            $lastChange = $user->last_username_change ?? Carbon::now()->subDays(8);
            $daysSince = $lastChange->diffInDays(Carbon::now());

            // Jika belum 7 hari → tolak
            if ($daysSince < 7) {
                return back()->with(
                    'error',
                    'Username hanya bisa diubah setiap 7 hari. Tunggu lagi ' . (7 - $daysSince) . ' hari.'
                );
            }

            // Update username & catat waktu perubahan
            $user->username = $request->username;
            $user->last_username_change = now();
        }

        // ================= UPDATE DATA DASAR =================
        $user->nama_lengkap = $request->nama_lengkap;
        $user->email = $request->email;

        // ================= PERUBAHAN PASSWORD =================
        if ($request->filled('password')) {

            // Wajib isi password lama
            if (!$request->filled('current_password')) {
                return back()->with('error', 'Untuk mengubah password, masukkan password lama terlebih dahulu.');
            }

            // Cek apakah password lama benar
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password lama salah. Tidak bisa mengubah password.');
            }

            // Simpan password baru (dienkripsi)
            $user->password = Hash::make($request->password);
        }

        // ================= UPLOAD FOTO PROFIL =================
        if ($request->hasFile('foto_profil')) {

            // Hapus foto lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Simpan foto baru
            $user->foto_profil = $request->file('foto_profil')
                ->store('uploads/profil', 'public');
        }

        // ================= SIMPAN PERUBAHAN =================
        $user->save();

        return redirect()->route('user.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Menghapus data verifikasi user (KTP & KK)
     */
    public function hapusVerifikasi($tipe = null)
    {
        $user = Auth::user();

        // ================= HAPUS FILE =================
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
        // ================= VALIDASI INPUT =================
        $request->validate([
            'current_password' => 'required|string'
        ]);

        $user = Auth::user();

        // ================= CEK PASSWORD =================
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah.'
            ], 401);
        }

        // ================= RESPONSE =================
        return response()->json([
            'success' => true,
            'message' => 'Password terverifikasi.'
        ]);
    }
}
