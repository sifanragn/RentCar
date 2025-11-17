<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InstagramController extends Controller
{
    // STEP 1: Redirect user ke Instagram OAuth
    public function redirect()
    {
        $appId = env('INSTAGRAM_APP_ID');
        $redirectUri = route('user.instagram.callback');
        $scope = 'user_profile,user_media'; // scope sesuai kebutuhan

        $url = "https://www.facebook.com/v19.0/dialog/oauth?" . http_build_query([
        'client_id' => env('FACEBOOK_APP_ID'),
        'redirect_uri' => route('user.instagram.callback'),
        'scope' => 'instagram_basic instagram_content_publish pages_show_list pages_read_engagement',
        'response_type' => 'code'
        ]);

        return redirect()->away($url);
    }

    // STEP 2: Callback dari Instagram
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('user.social.index')
                ->with('error', 'Akses ke akun Instagram dibatalkan.');
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('user.social.index')
                ->with('error', 'Kode otorisasi tidak ditemukan.');
        }

        $appId = env('INSTAGRAM_APP_ID');
        $appSecret = env('INSTAGRAM_APP_SECRET');
        $redirectUri = route('instagram.callback');

        try {
            // Tukar code dengan access token
            $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            $data = $response->json();
            Log::info('Instagram token response', $data);

            if (!isset($data['access_token'])) {
                return redirect()->route('user.social.index')
                    ->with('error', 'Gagal mendapatkan token akses dari Instagram.');
            }

            $accessToken = $data['access_token'];
            $userId = $data['user_id'];

            // Ambil data profil user
            $profile = Http::get("https://graph.instagram.com/{$userId}", [
                'fields' => 'id,username,account_type',
                'access_token' => $accessToken,
            ])->json();

            // Simpan ke database
            $user = Auth::user();
            $user->update([
                'instagram_id' => $profile['id'] ?? null,
                'instagram_username' => $profile['username'] ?? null,
                'instagram_token' => $accessToken,
            ]);

            return redirect()->route('user.social.index')
                ->with('success', 'Akun Instagram berhasil ditautkan!');
        } catch (\Exception $e) {
            Log::error('Instagram OAuth error: ' . $e->getMessage());
            return redirect()->route('user.social.index')
                ->with('error', 'Terjadi kesalahan saat menghubungkan akun Instagram.');
        }
    }
}
