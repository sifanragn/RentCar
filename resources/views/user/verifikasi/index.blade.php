@extends('partials.container')

@section('title', 'Verifikasi Akun')

@section('styles')
<style>
/* =================== GLOBAL =================== */
body {
    font-family: 'Poppins', sans-serif;
    background: #f5f7fa;
    padding: 18px;
}

/* PAGE TITLE */
.page-title {
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 28px;
    color: #0f172a;
}

/* =================== CARD =================== */
.section-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 22px 24px 20px;
    margin-bottom: 30px;
    border: 1px solid #eef1f5;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    margin-left: -12px;
    margin-right: -12px;
}

/* =================== SECTION HEADER =================== */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* TITLE */
.section-header h4 {
    font-weight: 600;
    font-size: 16px;
    color: #111827;
    line-height: 1.25;
    letter-spacing: -0.2px;
    margin: 0;
}

.section-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* ICON KIRI */
.icon-box {
    width: 40px;
    height: 40px;
    border-radius: 25%;
    background: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: rgba(255,255,255,0.92);
    box-shadow: 0 3px 10px rgba(0,0,0,0.10);
}

/* =================== BUTTON ACTION =================== */
.btn-action-verifikasi,
.btn-action-sosmed {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 16px;
    color: #0f172a;
    box-shadow: 0 4px 14px rgba(0,0,0,0.10);
    transition: 0.2s ease;
    cursor: pointer;
}

.btn-action-verifikasi:hover,
.btn-action-sosmed:hover {
    transform: scale(1.05);
}

/* =================== STATUS BADGE =================== */
.status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 10px;
    margin-top: 14px;
}

.status.pending { background: #fff7e6; color: #b77900; }
.status.none { background: #ffeaea; color: #d61f1f; }
.status.verified { background: #e7f9f0; color: #15803d; }

/* SOSMED lebih rapat */
.status-sosmed {
    margin-top: 6px !important;
}

.section-card p {
    font-size: 13px;
    color: #6b7280;
    margin: 8px 0 14px;
}
</style>
@endsection


@section('content')
<div class="container mt-1 mb-5">

    <h3 class="page-title">Verifikasi Akun Anda</h3>

    {{-- ================== VERIFIKASI DOKUMEN ================== --}}
    <div class="section-card">

        <div class="section-header">
            <div class="section-left">
                <div class="icon-box">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <h4>Verifikasi Dokumen</h4>
            </div>

@php
    $status = strtolower(trim($user->status_verifikasi));
@endphp

@if (in_array($status, ['', 'null', 'none', 'belum', 'belum upload', 'belum_upload', 'unverified', '0', 'ditolak']))
    <a href="{{ route('user.verifikasi.upload') }}" class="btn-action-verifikasi">
        <i class="fa-solid fa-upload"></i>
    </a>
@endif
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

        @else
            <span class="status none">
                <i class="fa-solid fa-circle-exclamation"></i> Belum memverifikasi dokumen
            </span>
        @endif

    </div>



    {{-- ================== SOSIAL MEDIA ================== --}}
    <div class="section-card">

        <div class="section-header">
            <div class="section-left">
                <div class="icon-box">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <h4>Tautan Sosial Media</h4>
            </div>

            <a href="{{ route('user.social.index') }}" class="btn-action-sosmed">
                <i class="fa-solid fa-link"></i>
            </a>
        </div>

        <p>Minimal menautkan 1 akun media sosial untuk keamanan akun.</p>

        {{-- REVISI PEMBAGI /4 --}}
        @if ($linkedCount === 0)
            <span class="status status-sosmed none">
                <i class="fa-solid fa-link-slash"></i> Belum menautkan sosial media
            </span>

        @elseif ($linkedCount < 4)
            <span class="status status-sosmed pending">
                <i class="fa-solid fa-link"></i> {{ $linkedCount }}/4 akun tertaut
            </span>

        @else
            <span class="status status-sosmed verified">
                <i class="fa-solid fa-circle-check"></i> Semua akun sosial media telah tertaut 🎉
            </span>
        @endif

    </div>

</div>

@include('partials.bottom-navbar')
@endsection
