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
    'no_hp' => 'required|unique:users,no_hp',
    'password' => 'required|min:6'
], [
    'no_hp.unique' => 'Nomor WhatsApp ini sudah terdaftar, silakan login.',
    'email.unique' => 'Email ini sudah terdaftar, silakan login.',
]);

    $otp = Otp::generate($request->no_hp);
    // Hapus OTP sebelumnya biar tidak bentrok
    Otp::where('phone', $request->no_hp)->delete();

    Whatsapp::send($request->no_hp, 
        "Kode verifikasi HexaRent kamu: *{$otp->code}*\n\nJangan berikan kode ini kepada siapapun.\nBerlaku 5 menit."
    );

    Session::put('register_data', $request->all());

    return redirect()->route('register.verifyPage')
        ->with('success', 'Kode OTP sudah dikirim via WhatsApp');
}

public function verifyOtp(Request $request)
{
    $request->validate(['otp' => 'required']);

    $data = Session::get('register_data');
    if (!$data) return redirect()->route('register')->with('error', 'Data registrasi hilang, ulangi');

    $otp = Otp::where('phone', $data['no_hp'])
        ->where('code', $request->otp)
        ->where('expires_at', '>=', now())
        ->first();

    if (!$otp) {
        return back()->with('error', 'Kode OTP salah atau kedaluwarsa');
    }

    // Hapus OTP setelah berhasil
    $otp->delete();

    $user = User::create([
        'nama_lengkap' => $data['nama_lengkap'],
        'username' => $data['username'] ?? strtok($data['email'], '@'),
        'email' => $data['email'],
        'no_hp' => $data['no_hp'],
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
        return response()->json(['status' => false, 'message' => 'Data tidak ditemukan. Silakan daftar ulang.']);
    }

    $otp = Otp::generate($data['no_hp']);

    Whatsapp::send($data['no_hp'],
        "Kode verifikasi HexaRent kamu: *{$otp->code}*\n\nJangan berikan kode ini kepada siapapun.\nBerlaku 5 menit."
    );

    return response()->json(['status' => true, 'message' => 'Kode OTP baru telah dikirim']);
}

}