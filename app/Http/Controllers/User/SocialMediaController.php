<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocialMediaController extends Controller
{
    public function index()
{
    $user = Auth::user();

    // Hitung berapa yang sudah tertaut
    $linkedCount = collect([
        $user->facebook_id,
        $user->tiktok_id,
        $user->instagram_id,
        $user->discord_id,
        $user->google_id,
        $user->linkedin_id,
    ])->filter()->count();

    // Total platform
    $totalPlatform = 6;

    return view('user.social-media.index', compact('user', 'linkedCount', 'totalPlatform'));
}


    public function connect($platform)
    {
        // === INSTAGRAM ===
        if ($platform === 'instagram') {
            return redirect()->route('user.instagram.redirect');
        }

        // === FACEBOOK ===
        if ($platform === 'facebook') {
            $fbAppId = env('FACEBOOK_APP_ID');
            $redirect = route('user.social.callback', 'facebook');

            $url = "https://www.facebook.com/v19.0/dialog/oauth?" . http_build_query([
            'client_id' => $fbAppId,
            'redirect_uri' => $redirect,
            'response_type' => 'code',
            'scope' => 'public_profile,email,user_link'
        ]);
            return redirect()->away($url);
        }

        // === TIKTOK ===
        if ($platform === 'tiktok') {
            $ttAppId = env('TIKTOK_CLIENT_KEY');
            $redirect = route('user.social.callback', 'tiktok');

            $url = "https://www.tiktok.com/auth/authorize?" . http_build_query([
                'client_key' => $ttAppId,
                'redirect_uri' => $redirect,
                'scope' => 'user.info.basic',
                'response_type' => 'code'
            ]);

            return redirect()->away($url);
        }

        // === DISCORD ===
        if ($platform === 'discord') {

            $params = http_build_query([
                'client_id' => env('DISCORD_CLIENT_ID'),
                'redirect_uri' => env('DISCORD_REDIRECT_URI'),
                'response_type' => 'code',
                'scope' => 'identify email'
            ]);

            return redirect("https://discord.com/oauth2/authorize?$params");
        }

        // === GOOGLE ===
if ($platform === 'google') {

    $googleClientId = env('GOOGLE_CLIENT_ID');
    $redirect = route('user.social.callback', 'google');

    $url = "https://accounts.google.com/o/oauth2/auth?" . http_build_query([
        'client_id' => $googleClientId,
        'redirect_uri' => $redirect,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ]);

    return redirect()->away($url);
}
// === LINKEDIN ===
if ($platform === 'linkedin') {

    $linkedinClientId = env('LINKEDIN_CLIENT_ID');
    $redirect = route('user.social.callback', 'linkedin');

    $url = "https://www.linkedin.com/oauth/v2/authorization?" . http_build_query([
        'response_type' => 'code',
        'client_id' => $linkedinClientId,
        'redirect_uri' => $redirect,
        'scope' => 'r_liteprofile r_emailaddress'
    ]);

    return redirect()->away($url);
}

        abort(404);
    }



    public function callback(Request $request, $platform)
    {
        $user = Auth::user();

        if (!$request->has('code')) {
            return redirect()->route('user.social.index')
                ->with('error', 'Verifikasi gagal.');
        }



        // ============================================
        // =============== FACEBOOK ====================
        // ============================================
        if ($platform === 'facebook') {

            $fbAppId = env('FACEBOOK_APP_ID');
            $fbSecret = env('FACEBOOK_APP_SECRET');
            $redirect = route('user.social.callback', 'facebook');

            // Tukar code → token
            $tokenResponse = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id' => $fbAppId,
                'client_secret' => $fbSecret,
                'redirect_uri' => $redirect,
                'code' => $request->code
            ]);

            if (!$tokenResponse->ok()) {
                return redirect()->route('user.social.index')
                    ->with('error', 'Gagal mendapatkan token.');
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Ambil profil user
            $userData = Http::get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email,link,picture.type(large)',
            'access_token' => $accessToken
        ])->json();

        $link = $userData['link'] ?? null;

            $avatar = $userData['picture']['data']['url'] ?? null;

            // Simpan DB
            $user->facebook_id = $userData['id'] ?? null;
            $user->facebook_name = $userData['name'] ?? null;
            $user->facebook_email = $userData['email'] ?? null;
            $user->facebook_link = $userData['link'] ?? null;
            $user->facebook_avatar = $avatar;
            $user->facebook_token = $accessToken;
            $user->save();

            return redirect()->route('user.social.index')->with('success', 'Facebook berhasil ditautkan.');
        }



        // ============================================
        // =============== TIKTOK ======================
        // ============================================
        if ($platform === 'tiktok') {

            $user->tiktok_username = "tiktok_user";
            $user->tiktok_id = "TT" . rand(10000, 99999);
            $user->save();

            return redirect()->route('user.social.index')
                ->with('success', 'TikTok berhasil ditautkan.');
        }



        // ============================================
        // =============== DISCORD ======================
        // ============================================
        if ($platform === 'discord') {

            // 1. Tukar CODE → ACCESS TOKEN
            $tokenResponse = Http::asForm()->post('https://discord.com/api/oauth2/token', [
                'client_id' => env('DISCORD_CLIENT_ID'),
                'client_secret' => env('DISCORD_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
                'code' => $request->code,
                'redirect_uri' => env('DISCORD_REDIRECT_URI'),
            ]);

            if (!$tokenResponse->ok()) {
                return redirect()->route('user.social.index')
                    ->with('error', 'Gagal mendapatkan token Discord.');
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // 2. Ambil data user Discord
            $userData = Http::withHeaders([
                'Authorization' => "Bearer $accessToken"
            ])->get('https://discord.com/api/users/@me')->json();

            $discordId = $userData['id'] ?? null;
            $username = $userData['username'] ?? null;
            $globalName = $userData['global_name'] ?? null;
            $email = $userData['email'] ?? null;
            $avatar = $userData['avatar'] ?? null;

            // Avatar URL
            $avatarUrl = $avatar
                ? "https://cdn.discordapp.com/avatars/{$discordId}/{$avatar}.png?size=512"
                : null;

            // Profile link
            $profileUrl = "https://discord.com/users/" . $discordId;

            // Simpan DB
            $user->discord_id = $discordId;
            $user->discord_username = $username;
            $user->discord_global_name = $globalName;
            $user->discord_email = $email;
            $user->discord_avatar = $avatarUrl;
            $user->discord_profile_url = $profileUrl;
            $user->discord_token = $accessToken;
            $user->save();

            return redirect()->route('user.social.index')
                ->with('success', 'Discord berhasil ditautkan.');
        }
        // === GOOGLE ===
// ============================================
// =============== GOOGLE ======================
// ============================================
if ($platform === 'google') {

    // 1. Tukar CODE → TOKEN
    $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'grant_type' => 'authorization_code',
        'code' => $request->code,
    ]);

    if (!$tokenResponse->ok()) {
        return redirect()->route('user.social.index')
            ->with('error', 'Gagal mendapatkan token Google.');
    }

    $accessToken = $tokenResponse->json()['access_token'];

    // 2. Ambil data profil Google
    $userInfo = Http::withHeaders([
        'Authorization' => "Bearer $accessToken"
    ])->get("https://www.googleapis.com/oauth2/v2/userinfo")->json();

    // 3. Simpan ke DB
    $user->google_id = $userInfo['id'] ?? null;
    $user->google_name = $userInfo['name'] ?? null;
    $user->google_email = $userInfo['email'] ?? null;
    $user->google_avatar = $userInfo['picture'] ?? null;
    $user->google_token = $accessToken;
    $user->save();

    return redirect()->route('user.social.index')
        ->with('success', 'Google berhasil ditautkan.');
}


// ============================================
// =============== LINKEDIN ====================
// ============================================
if ($platform === 'linkedin') {

    // 1. Tukar CODE → TOKEN
    $tokenResponse = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
        'grant_type' => 'authorization_code',
        'code' => $request->code,
        'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    ]);

    if (!$tokenResponse->ok()) {
        return redirect()->route('user.social.index')
            ->with('error', 'Gagal mendapatkan token LinkedIn.');
    }

    $accessToken = $tokenResponse->json()['access_token'];

    // 2. Ambil data user LinkedIn
    $profile = Http::withHeaders([
        "Authorization" => "Bearer $accessToken"
    ])->get('https://api.linkedin.com/v2/me')->json();

    $emailData = Http::withHeaders([
        "Authorization" => "Bearer $accessToken"
    ])->get('https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))')->json();

    $email = $emailData['elements'][0]['handle~']['emailAddress'] ?? null;

    // 3. Simpan DB
    $user->linkedin_id = $profile['id'] ?? null;
    $user->linkedin_name = $profile['localizedFirstName'] . ' ' . $profile['localizedLastName'];
    $user->linkedin_email = $email;
    $user->linkedin_token = $accessToken;
    $user->save();

    return redirect()->route('user.social.index')
        ->with('success', 'LinkedIn berhasil ditautkan.');
}



        abort(404);
    }





    // ============================================
    // =============== DISCONNECT ==================
    // ============================================
    public function disconnect($platform)
    {
        $user = Auth::user();

        if ($platform === 'facebook') {
            $user->facebook_id = null;
            $user->facebook_name = null;
            $user->facebook_email = null;
            $user->facebook_link = null;
            $user->facebook_avatar = null;
            $user->facebook_token = null;
        }

        if ($platform === 'instagram') {
            $user->instagram_id = null;
            $user->instagram_username = null;
            $user->instagram_token = null;
        }

        if ($platform === 'tiktok') {
            $user->tiktok_id = null;
            $user->tiktok_username = null;
            $user->tiktok_token = null;
        }

        if ($platform === 'discord') {
            $user->discord_id = null;
            $user->discord_username = null;
            $user->discord_global_name = null;
            $user->discord_email = null;
            $user->discord_avatar = null;
            $user->discord_profile_url = null;
            $user->discord_token = null;
        }

        if ($platform === 'google') {
    $user->google_id = null;
    $user->google_name = null;
    $user->google_email = null;
    $user->google_avatar = null;
    $user->google_token = null;
}

if ($platform === 'linkedin') {
    $user->linkedin_id = null;
    $user->linkedin_name = null;
    $user->linkedin_email = null;
    $user->linkedin_token = null;
}

        $user->save();

        return redirect()->route('user.social.index')
            ->with('success', ucfirst($platform) . ' berhasil dilepaskan.');
    }
}
