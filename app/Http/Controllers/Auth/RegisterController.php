<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Otp;
use App\Helpers\PhoneFormatter;
use App\Helpers\Whatsapp;


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
        'no_hp' => 'required|min:10|unique:users,no_hp',
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
        'no_hp' => PhoneFormatter::format($request->no_hp), // ✅ TAMBAH INI
        'password' => Hash::make($request->password),
        'foto_ktp' => $pathKtp,
        'foto_kk' => $pathKk,
        'status_verifikasi' => $statusVerifikasi,
        'role' => 'user',
    ]);
        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan login.');
    }

    public function sendOtp(Request $request)
{
    $request->validate([
        'nama_lengkap' => 'required',
        'email' => 'required|email|unique:users,email',
        'no_hp' => 'required',
        'password' => 'required|min:6'
    ]);

    // ✅ Format nomor (08 -> 628)
    $phone = PhoneFormatter::format($request->no_hp);

    // ✅ Cek apakah nomor WA sudah terdaftar
    if (User::where('no_hp', $phone)->exists()) {
        return redirect()->route('login')
    ->with('error', 'Nomor WhatsApp ini sudah terdaftar. Silakan login.');
    }

    // ✅ Hapus OTP lama untuk nomor ini
    Otp::where('phone', $phone)->delete();

    // ✅ Generate OTP
    $otp = Otp::generate($phone);

    // ✅ Kirim WA
    Whatsapp::send($phone,
        "Kode verifikasi HexaRent kamu: *{$otp->code}*\n\nJangan berikan kode ini kepada siapapun.\nBerlaku 5 menit."
    );

    // ✅ Simpan data pendaftaran & nomor yg sudah diformat
    $data = $request->all();
    $data['no_hp'] = $phone;
    Session::put('register_data', $data);

    return redirect()->route('register.verifyPage')
        ->with('success', 'Kode OTP sudah dikirim via WhatsApp');
}


    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $data = Session::get('register_data');
        if (!$data) {
            return redirect()->route('register')->with('error', 'Data registrasi hilang, ulangi');
        }

        // ✅ Ambil nomor yang sudah diformat
        $phone = PhoneFormatter::format($data['no_hp']);

        $otp = Otp::where('phone', $phone)
            ->where('code', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otp) {
            return back()->with('error', 'Kode OTP salah atau kedaluwarsa');
        }

        // ✅ Hapus OTP setelah valid
        $otp->delete();

        // ✅ Buat user
        $user = User::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'username' => $data['username'] ?? strtok($data['email'], '@'),
            'email' => $data['email'],
            'no_hp' => $phone,
            'password' => Hash::make($data['password']),
            'status_verifikasi' => 'disetujui',
            'role' => 'user',
        ]);

        Session::forget('register_data');
        auth()->login($user);

        return redirect()->route('user.dashboard')->with('success', 'Akun berhasil dibuat!');
    }


        public function resendOtp(Request $request)
    {
        $data = Session::get('register_data');

        if (!$data || !isset($data['no_hp'])) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan. Silakan daftar ulang.'
            ]);
        }

        // ✅ format nomor lagi biar rapih
        $phone = \App\Helpers\PhoneFormatter::format($data['no_hp']);

        // ✅ buat OTP baru dan hapus lama
        $otp = \App\Models\Otp::generate($phone);

        // ✅ kirim WhatsApp
        $wa = \App\Helpers\Whatsapp::send(
            $phone,
            "Kode verifikasi HexaRent kamu: *{$otp->code}*\n\nJangan berikan kode ini kepada siapapun.\nBerlaku 5 menit."
        );

        // ✅ cek kalau API WA gagal
        if (!$wa) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim OTP, coba lagi.'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Kode OTP baru telah dikirim'
        ]);
    }
}
