<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        if (session('admin_logged_in')) {
            session()->flush();
        } else {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/login');
    }
}
