<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1️⃣ Cek dulu di tabel admins
        $admin = Admin::where('email', $request->email)
            ->where('status', 'aktif')
            ->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session([
                'admin_logged_in' => true,
                'admin_id' => $admin->admin_id,
                'admin_role' => $admin->role,
                'admin_name' => $admin->nama_lengkap,
            ]);

            \Log::info('✅ Admin login berhasil', ['email' => $admin->email]);

return redirect()->route('cars.index');
        }

        // 2️⃣ Kalau bukan admin, cek user biasa
        $user = User::where('email', $request->email)->first();

if ($user && Hash::check($request->password, $user->password)) {
    Auth::login($user);
    $request->session()->regenerate();

    \Log::info('✅ User login berhasil', ['email' => $user->email]);

    // ⬇️ ubah ini
return redirect()->route('user.home');
}

        // 3️⃣ Kalau keduanya gagal
        \Log::warning('❌ Login gagal', ['email' => $request->email]);

        return back()->withErrors([
            'login_error' => 'Email atau password salah, atau akun tidak aktif.',
        ]);
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
