@extends('partials.container')

@section('title', 'Verifikasi Sosial Media')

@section('styles')
<style>

body {
    background: #f8f9fc !important;
    font-family: 'Poppins', sans-serif;
}

/* ===== TITLE ===== */
.section-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 22px;
    text-align: center;
    color: #111827;
}

/* ===== CARD ===== */
.connection-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    padding: 16px 18px;
    border-radius: 16px;
    margin-bottom: 14px;
    border: 1px solid #eef0f4;
    box-shadow: 0 4px 14px rgba(0,0,0,0.03);
}

/* LEFT SIDE */
.conn-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

/* ICON PLATFORM */
.platform-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    padding: 6px;
    background: #f2f4f7;
    object-fit: contain;
}

/* TEXT */
.conn-name {
    font-size: 15px;
    font-weight: 600;
    color: #111;
}

.conn-username {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #4b5563;
    margin-top: 2px;
}

.text-danger {
    color: #e63946 !important;
}

/* ===== VERIFIED BADGE (STAR STYLE) ===== */
.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 6px;
    position: relative;
    top: 0px;
}

.status-badge svg {
    width: 20px;
    height: 20px;
    display: block;
}

/* RIGHT ICON */
.right-actions {
    display: flex;
    align-items: center;
}

.action-icon {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: 0.2s;
}

/* Hover umum */
.action-icon:hover {
    background: #f0f2f5;
}

/* Lepaskan */
.unlink {
    color: #e63946;
}
.unlink:hover {
    background: #fdecec;
}

/* Tautkan */
.plus {
    color: #2563eb;
}
.plus:hover {
    background: #e3edff;
}
</style>
@endsection



@section('content')
<div class="container mt-3 mb-5">

    <div class="section-title">Connections</div>

@php
$verifiedBadge = '
<span class="status-badge">
    <svg width="22" height="22" viewBox="0 0 24 24">
        <path fill="#3B82F6" d="
            M12 2.25
            L14.7 4.2 18.1 3.9 17.8 7.3
            20.5 9.6 17.8 11.9 18.1 15.3
            14.7 15 12 17.8 9.3 15
            5.9 15.3 6.2 11.9 3.5 9.6
            6.2 7.3 5.9 3.9 9.3 4.2Z"/>
        <path fill="#ffffff" d="
            M10.3 10.8 
            L8.7 9.2
            7.5 10.4
            10.3 13.2
            16.3 7.2
            15.1 6.0Z"/>
    </svg>
</span>';
@endphp


{{-- ================= FACEBOOK ================= --}}
<div class="connection-item">
    <div class="conn-left">
        <img src="{{ asset('img/icons/facebook.png') }}" class="platform-icon">
        <div>
            <div class="conn-name">Facebook</div>
            @if($user->facebook_name)
                <div class="conn-username">
                    {{ $user->facebook_name }} {!! $verifiedBadge !!}
                </div>
            @else
                <div class="conn-username text-danger">Belum tertaut</div>
            @endif
        </div>
    </div>

    <div class="right-actions">
        @if($user->facebook_name)
            <a href="{{ route('user.social.disconnect','facebook') }}" class="action-icon unlink">
                <i class="fa-solid fa-link-slash"></i>
            </a>
        @else
            <a href="{{ route('user.social.connect','facebook') }}" class="action-icon plus">
                <i class="fa-solid fa-link"></i>
            </a>
        @endif
    </div>
</div>


{{-- ================= TIKTOK ================= --}}
<div class="connection-item">
    <div class="conn-left">
        <img src="{{ asset('img/icons/tiktok.png') }}" class="platform-icon">
        <div>
            <div class="conn-name">TikTok</div>
            @if($user->tiktok_username)
                <div class="conn-username">
                    {{ $user->tiktok_username }} {!! $verifiedBadge !!}
                </div>
            @else
                <div class="conn-username text-danger">Belum tertaut</div>
            @endif
        </div>
    </div>

    <div class="right-actions">
        @if($user->tiktok_username)
            <a href="{{ route('user.social.disconnect','tiktok') }}" class="action-icon unlink">
                <i class="fa-solid fa-link-slash"></i>
            </a>
        @else
            <a href="{{ route('user.social.connect','tiktok') }}" class="action-icon plus">
                <i class="fa-solid fa-link"></i>
            </a>
        @endif
    </div>
</div>


{{-- ================= DISCORD ================= --}}
<div class="connection-item">
    <div class="conn-left">
        <img src="{{ asset('img/icons/discord.png') }}" class="platform-icon">
        <div>
            <div class="conn-name">Discord</div>
            @if($user->discord_username)
                <div class="conn-username">
                    {{ $user->discord_global_name ?? $user->discord_username }} {!! $verifiedBadge !!}
                </div>
            @else
                <div class="conn-username text-danger">Belum tertaut</div>
            @endif
        </div>
    </div>

    <div class="right-actions">
        @if($user->discord_username)
            <a href="{{ route('user.social.disconnect','discord') }}" class="action-icon unlink">
                <i class="fa-solid fa-link-slash"></i>
            </a>
        @else
            <a href="{{ route('user.social.connect','discord') }}" class="action-icon plus">
                <i class="fa-solid fa-link"></i>
            </a>
        @endif
    </div>
</div>


{{-- ================= GOOGLE ================= --}}
<div class="connection-item">
    <div class="conn-left">
        <img src="{{ asset('img/icons/google.png') }}" class="platform-icon">
        <div>
            <div class="conn-name">Google</div>
            @if($user->google_email)
                <div class="conn-username">
                    {{ $user->google_email }} {!! $verifiedBadge !!}
                </div>
            @else
                <div class="conn-username text-danger">Belum tertaut</div>
            @endif
        </div>
    </div>

    <div class="right-actions">
        @if($user->google_email)
            <a href="{{ route('user.social.disconnect','google') }}" class="action-icon unlink">
                <i class="fa-solid fa-link-slash"></i>
            </a>
        @else
            <a href="{{ route('user.social.connect','google') }}" class="action-icon plus">
                <i class="fa-solid fa-link"></i>
            </a>
        @endif
    </div>
</div>
</div>
@include('partials.bottom-navbar')
@endsection
