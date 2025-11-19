@extends('layouts.admin.app')

@section('title', 'Detail Sosial Media')

@section('content')

<div class="container mt-4">

    <h3 class="fw-bold mb-4"><i class="bi bi-person-fill"></i> Detail Sosial Media Pengguna</h3>

{{-- ======================== ICON BELUM TERTAUT ======================== --}}
@php
$notConnectedIcon = '
<span class="not-connected-icon">
    <svg viewBox="0 0 24 24" fill="none" width="26" height="26">
        <circle cx="12" cy="12" r="9" stroke="#e63946" stroke-width="2.4"/>
        <path d="M9 9L15 15" stroke="#e63946" stroke-width="2.4" stroke-linecap="round"/>
        <path d="M15 9L9 15" stroke="#e63946" stroke-width="2.4" stroke-linecap="round"/>
    </svg>
</span>';
@endphp

    <div class="detail-wrapper">

        {{-- ========================= USER INFO ========================= --}}
<div class="social-card mb-4">
    <h5 class="section-title">
        <i class="bi bi-person-fill"></i> Informasi Pengguna
    </h5>

    <div class="info-row">
        <div>
            <p><span class="label">User ID:</span> {{ $user->user_id }}</p>
            <p><span class="label">Email:</span> {{ $user->email }}</p>
        </div>
    </div>
</div>

        {{-- ========================= FACEBOOK ========================= --}}
        <div class="social-card">
            <h5 class="section-title brand-facebook"><i class="bi bi-facebook"></i> Facebook</h5>

            @if($user->facebook_id)
                <div class="info-row">
                    @if($user->facebook_avatar)
                        <img src="{{ $user->facebook_avatar }}" class="profile-pic">
                    @endif

                    <div>
                        <p><span class="label">Nama:</span> {{ $user->facebook_name }}</p>
                        <p><span class="label">Email:</span> {{ $user->facebook_email ?? '-' }}</p>
                        <p><span class="label">Facebook ID:</span> {{ $user->facebook_id }}</p>

<p class="mt-2">
    @if($user->facebook_link)
        <a href="{{ $user->facebook_link }}" target="_blank" class="btn-profile">
            Kunjungi Profil
        </a>
    @else
        {!! $notConnectedIcon !!}
    @endif
</p>
                    </div>
                </div>
            @else
                {!! $notConnectedIcon !!}
            @endif
        </div>

        {{-- ========================= TIKTOK ========================= --}}
        <div class="social-card">
            <h5 class="section-title brand-tiktok"><i class="bi bi-music-note-beamed"></i> TikTok</h5>

            @if($user->tiktok_username)
                <p><span class="label">Username:</span> {{ $user->tiktok_username }}</p>
                <a href="https://www.tiktok.com/@{{ $user->tiktok_username }}"
                   class="btn-view"
                   target="_blank">
                   Lihat Profil
                </a>
            @else
                {!! $notConnectedIcon !!}
            @endif
        </div>

        {{-- ========================= DISCORD ========================= --}}
        <div class="social-card">
            <h5 class="section-title brand-discord"><i class="bi bi-discord"></i> Discord</h5>

            @if($user->discord_id)
                <div class="info-row">
                    @if($user->discord_avatar)
                        <img src="{{ $user->discord_avatar }}" class="profile-pic">
                    @endif

                    <div>
                        <p><span class="label">Username:</span> {{ $user->discord_username }}</p>
                        <p><span class="label">Email:</span> {{ $user->discord_email ?? '-' }}</p>
                        <p><span class="label">Discord ID:</span> {{ $user->discord_id }}</p>

<p class="mt-2">
    <a href="https://discord.com/users/{{ $user->discord_id }}"
       target="_blank"
       class="btn-profile">
        Kunjungi Profil
    </a>
</p>

                    </div>
                </div>
            @else
                {!! $notConnectedIcon !!}
            @endif
        </div>

        {{-- ========================= GOOGLE ========================= --}}
        <div class="social-card">
            <h5 class="section-title brand-google"><i class="bi bi-google"></i> Google</h5>

            @if($user->google_id)
                <div class="info-row">
                    @if($user->google_avatar)
                        <img src="{{ $user->google_avatar }}" class="profile-pic">
                    @endif

                    <div>
                        <p><span class="label">Nama:</span> {{ $user->google_name }}</p>
                        <p><span class="label">Email:</span> {{ $user->google_email }}</p>
                        <p><span class="label">Google ID:</span> {{ $user->google_id }}</p>
                    </div>
                </div>
            @else
                {!! $notConnectedIcon !!}
            @endif
        </div>

          <a href="{{ url()->previous() }}" class="btn-back mb-3">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    </div>

</div>

@endsection
