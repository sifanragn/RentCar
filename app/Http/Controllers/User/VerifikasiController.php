<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan status verifikasi dokumen & sosial media user
     */
    public function index()
{
    $user = Auth::user();

    // Hitung sosial media tertaut
    $linkedCount = collect([
        $user->facebook_id,
        $user->tiktok_id,
        $user->instagram_id,
        $user->discord_id,
        $user->google_id,
        $user->linkedin_id,
    ])->filter()->count();

    $totalPlatform = 6;

    return view('user.verifikasi.index', compact('user', 'linkedCount', 'totalPlatform'));
}

}
