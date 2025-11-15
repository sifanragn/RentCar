@extends('partials.container')

@section('title', 'Verifikasi Akun')

@section('styles')
<style>

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(180deg, #f5f7fa, #eef1f7);
    padding: 18px;
}

/* ===== PAGE TITLE ===== */
.page-title {
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 26px;
    color: #0f172a;
}

/* ===== CARD ===== */
.section-card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 22px 20px;
    margin-bottom: 32px;
    border: 1px solid rgba(255,255,255,0.65);
    box-shadow: 0 10px 28px rgba(0,0,0,0.06);
    position: relative; /* penting untuk floating button */
}

/* ===== HEADER ===== */
.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.section-header .icon-box {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #ecf3ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1d4ed8;
    font-size: 18px;
}

.section-header h4 {
    font-weight: 600;
    font-size: 16px;
    color: #111827;
}

/* ===== STATUS BADGE ===== */
.status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 10px;
    border: 1px solid transparent;
    margin-bottom: 10px;
}

.status.pending {
    background: #fff7e6;
    color: #b77900;
    border-color: #ffe8b8;
}

.status.verified {
    background: #e7f9f0;
    color: #15803d;
    border-color: #bcf0d0;
}

.status.none {
    background: #ffeaea;
    color: #d61f1f;
    border-color: #f5c2c2;
}

/* ===== FLOATING CORNER BUTTON ===== */
.btn-floating {
    width: 48px;
    height: 48px;
    background: #111827;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;

    position: absolute;
    bottom: -16px;    /* keluar sedikit dari card */
    right: 16px;

    box-shadow: 0 6px 16px rgba(0,0,0,0.20);
    transition: 0.25s ease;
}

.btn-floating:hover {
    transform: translateY(-2px);
    box-shadow: 0 9px 22px rgba(0,0,0,0.25);
}

</style>
@endsection

@section('content')
<div class="container mt-1 mb-5">

    <h3 class="page-title">Verifikasi Akun Anda</h3>


    {{-- ================== VERIFIKASI DOKUMEN ================== --}}
    <div class="section-card">

        <div class="section-header">
            <div class="icon-box">📄</div>
            <h4>Verifikasi Dokumen</h4>
        </div>

        @if ($user->status_verifikasi === 'menunggu')
            <span class="status pending">
                <i class="fa-solid fa-clock"></i> Menunggu verifikasi admin
            </span>

        @elseif ($user->status_verifikasi === 'disetujui')
            <span class="status verified">
                <i class="fa-solid fa-circle-check"></i> Dokumen sudah diverifikasi
            </span>

        @elseif ($user->status_verifikasi === 'ditolak')
            <span class="status none">
                <i class="fa-solid fa-circle-xmark"></i> Verifikasi ditolak, silakan upload ulang
            </span>

            <a href="{{ route('user.verifikasi.upload') }}" class="btn-floating">
                <i class="fa-solid fa-upload"></i>
            </a>

        @else
            <span class="status none">
                <i class="fa-solid fa-circle-exclamation"></i> Belum memverifikasi dokumen
            </span>

            <a href="{{ route('user.verifikasi.upload') }}" class="btn-floating">
                <i class="fa-solid fa-upload"></i>
            </a>
        @endif

    </div>



    {{-- ================== SOSIAL MEDIA ================== --}}
    <div class="section-card">

        <div class="section-header">
            <div class="icon-box">📱</div>
            <h4>Tautan Sosial Media</h4>
        </div>

        <p>Minimal menautkan 1 akun media sosial untuk keamanan akun.</p>

        @php
            $total = $totalPlatform;
            $linked = $linkedCount;
        @endphp

        @if ($linked === 0)
            <span class="status none">
                <i class="fa-solid fa-link-slash"></i> Belum menautkan akun sosial media
            </span>

            <a href="{{ route('user.social.index') }}" class="btn-floating">
                <i class="fa-solid fa-link"></i>
            </a>

        @elseif ($linked < $total)
            <span class="status pending">
                <i class="fa-solid fa-link"></i> {{ $linked }}/{{ $total }} akun tertaut
            </span>

            <a href="{{ route('user.social.index') }}" class="btn-floating">
                <i class="fa-solid fa-link"></i>
            </a>

        @else
            <span class="status verified">
                <i class="fa-solid fa-circle-check"></i> Semua akun sosial media telah tertaut 🎉
            </span>
        @endif

    </div>

</div>

@include('partials.bottom-navbar')
@endsection
