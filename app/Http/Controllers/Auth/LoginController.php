<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Helpers\PhoneFormatter;


class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
{
    $request->validate([
        'login_id' => 'required',
        'password' => 'required',
    ]);

    $login_id = $request->login_id;

    // Tentukan email atau HP
    $field = filter_var($login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_hp';

    // ✅ Jika nomor HP → normalisasi (08 → 628)
    if ($field === 'no_hp') {
        $login_id = PhoneFormatter::format($login_id);
    }

    // ---------------------- ADMIN LOGIN ---------------------- //
    $admin = Admin::where($field, $login_id)
        ->where('status', 'aktif')
        ->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
        session([
            'admin_logged_in' => true,
            'admin_id' => $admin->admin_id,
            'admin_role' => $admin->role,
            'admin_name' => $admin->nama_lengkap,
        ]);

        \Log::info('✅ Admin login berhasil', [$field => $login_id]);
        return redirect()->route('admin.dashboard.index');
    }

    // ---------------------- USER LOGIN ---------------------- //
    $user = User::where($field, $login_id)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);
        $request->session()->regenerate();

        \Log::info('✅ User login berhasil', [$field => $login_id]);
        return redirect()->route('user.home');
    }

    // ---------------------- LOGIN FAILED ---------------------- //
    \Log::warning('❌ Login gagal', [$field => $login_id]);

    return back()->withErrors([
        'login_error' => 'Email / No HP atau password salah, atau akun tidak aktif.',
    ])->withInput();
}


   public function __construct()
{
    $this->middleware(\App\Http\Middleware\PreventBackHistory::class)
         ->only(['index', 'logout']);
}


    public function logout(Request $request)
    {
        Auth::logout();

        session()->forget(['admin_logged_in', 'admin_id', 'admin_role', 'admin_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
