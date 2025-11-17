@extends('layouts.admin.app')

@section('title', 'Detail Sosial Media')

@section('content')

<style>
.profile-pic {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
}
.label {
    font-weight: 600;
}
.card {
    border-radius: 12px;
}
</style>

<div class="container mt-4">

    <h3 class="fw-bold mb-3">👤 Detail Pengguna</h3>

    {{-- USER INFO --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">{{ $user->name }}</h5>
            <p class="text-muted mb-1">User ID: {{ $user->user_id }}</p>
            <p class="text-muted mb-0">Email: {{ $user->email }}</p>
        </div>
    </div>

    {{-- ========================= FACEBOOK ========================= --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">📘 Facebook</h5>

            @if($user->facebook_id)

                @if($user->facebook_avatar)
                    <img src="{{ $user->facebook_avatar }}" class="profile-pic mb-3">
                @endif

                <p><span class="label">Nama:</span> {{ $user->facebook_name }}</p>
                <p><span class="label">Email:</span> {{ $user->facebook_email ?? '-' }}</p>
                <p><span class="label">Facebook ID:</span> {{ $user->facebook_id }}</p>

                <p>
                    <span class="label">Link Profil:</span><br>
                    @if($user->facebook_link)
                        <a href="{{ $user->facebook_link }}" target="_blank" class="text-primary">
                            {{ $user->facebook_link }}
                        </a>
                    @else
                        <span class="text-muted">Tidak ada link profil</span>
                    @endif
                </p>

            @else
                <span class="text-danger">Belum tertaut</span>
            @endif
        </div>
    </div>

    {{-- ========================= INSTAGRAM ========================= --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">📸 Instagram</h5>

            @if($user->instagram_username)
                <p><span class="label">Username:</span> {{ $user->instagram_username }}</p>

                <a href="https://instagram.com/{{ $user->instagram_username }}"
                   target="_blank"
                   class="btn btn-warning btn-sm">Lihat Profil</a>
            @else
                <span class="text-danger">Belum tertaut</span>
            @endif

        </div>
    </div>

    {{-- ========================= TIKTOK ========================= --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">🎵 TikTok</h5>

            @if($user->tiktok_username)
                <p><span class="label">Username:</span> {{ $user->tiktok_username }}</p>

                <a href="https://www.tiktok.com/@{{ $user->tiktok_username }}"
                   target="_blank"
                   class="btn btn-dark btn-sm">Lihat Profil</a>
            @else
                <span class="text-danger">Belum tertaut</span>
            @endif

        </div>
    </div>

    {{-- ========================= DISCORD ========================= --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">💬 Discord</h5>

            @if($user->discord_id)

                @if($user->discord_avatar)
                    <img src="{{ $user->discord_avatar }}" class="profile-pic mb-3">
                @endif

                <p><span class="label">Username:</span> {{ $user->discord_username }}</p>
                <p><span class="label">Email:</span> {{ $user->discord_email ?? '-' }}</p>
                <p><span class="label">Discord ID:</span> {{ $user->discord_id }}</p>

                <p>
                    <span class="label">Link Profil:</span><br>
                    <a href="https://discord.com/users/{{ $user->discord_id }}"
                       target="_blank"
                       class="text-primary">
                        https://discord.com/users/{{ $user->discord_id }}
                    </a>
                </p>

            @else
                <span class="text-danger">Belum tertaut</span>
            @endif

        </div>
    </div>

    {{-- ========================= GOOGLE ========================= --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">🟢 Google</h5>

            @if($user->google_id)

                @if($user->google_avatar)
                    <img src="{{ $user->google_avatar }}" class="profile-pic mb-3">
                @endif

                <p><span class="label">Nama:</span> {{ $user->google_name }}</p>
                <p><span class="label">Email:</span> {{ $user->google_email }}</p>
                <p><span class="label">Google ID:</span> {{ $user->google_id }}</p>

            @else
                <span class="text-danger">Belum tertaut</span>
            @endif

        </div>
    </div>

    {{-- ========================= LINKEDIN ========================= --}}
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">🔗 LinkedIn</h5>

            @if($user->linkedin_id)

                <p><span class="label">Nama:</span> {{ $user->linkedin_name }}</p>
                <p><span class="label">Email:</span> {{ $user->linkedin_email ?? '-' }}</p>
                <p><span class="label">LinkedIn ID:</span> {{ $user->linkedin_id }}</p>

                <p>
                    <span class="label">Link Profil:</span><br>
                    <a href="https://www.linkedin.com/in/{{ $user->linkedin_id }}"
                       target="_blank"
                       class="text-primary">
                        https://www.linkedin.com/in/{{ $user->linkedin_id }}
                    </a>
                </p>

            @else
                <span class="text-danger">Belum tertaut</span>
            @endif
        </div>
    </div>

</div>
@endsection
