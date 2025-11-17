@extends('layouts.admin.app')

@section('title', 'Monitoring Sosial Media Pengguna')

@section('content')

<style>
.table td { vertical-align: middle !important; }
small.text-muted { font-size: 11px; }
</style>

<div class="container mt-4">

    <h3 class="fw-bold mb-4">📱 Monitoring Sosial Media Pengguna</h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Facebook</th>
                        <th>Instagram</th>
                        <th>TikTok</th>
                        <th>Discord</th>
                        <th>Google</th>
                        <th>LinkedIn</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->user_id }}</td>

                        {{-- NAMA + EMAIL --}}
                        <td>
                            <strong>{{ $u->name }}</strong><br>
                            <small class="text-muted">{{ $u->email }}</small>
                        </td>

                        {{-- FACEBOOK --}}
                        <td>
                            @if($u->facebook_id)
                                <span class="text-success fw-semibold">Tertaut</span><br>
                                <small class="text-muted">{{ $u->facebook_name }}</small>
                            @else
                                <span class="text-danger">Belum</span>
                            @endif
                        </td>

                        {{-- INSTAGRAM --}}
                        <td>
                            @if($u->instagram_username)
                                <span class="text-success fw-semibold">Tertaut</span><br>
                                <small class="text-muted">{{ $u->instagram_username }}</small>
                            @else
                                <span class="text-danger">Belum</span>
                            @endif
                        </td>

                        {{-- TIKTOK --}}
                        <td>
                            @if($u->tiktok_username)
                                <span class="text-success fw-semibold">Tertaut</span><br>
                                <small class="text-muted">{{ $u->tiktok_username }}</small>
                            @else
                                <span class="text-danger">Belum</span>
                            @endif
                        </td>

                        {{-- DISCORD --}}
                        <td>
                            @if($u->discord_id)
                                <span class="text-success fw-semibold">Tertaut</span><br>
                                <small class="text-muted">{{ $u->discord_username }}</small>
                            @else
                                <span class="text-danger">Belum</span>
                            @endif
                        </td>

                        {{-- GOOGLE --}}
                        <td>
                            @if($u->google_id)
                                <span class="text-success fw-semibold">Tertaut</span><br>
                                <small class="text-muted">{{ $u->google_email }}</small>
                            @else
                                <span class="text-danger">Belum</span>
                            @endif
                        </td>

                        {{-- LINKEDIN --}}
                        <td>
                            @if($u->linkedin_id)
                                <span class="text-success fw-semibold">Tertaut</span><br>
                                <small class="text-muted">{{ $u->linkedin_name }}</small>
                            @else
                                <span class="text-danger">Belum</span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td>
                            <a href="{{ route('admin.social.show', $u->user_id) }}" 
                               class="btn btn-dark btn-sm">Detail</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
