@extends('layouts.admin.app')

@section('title', 'Monitoring Sosial Media Pengguna')

@section('content')

<div class="container mt-4">

    <h3 class="fw-bold mb-4">📱 Monitoring Sosial Media Pengguna</h3>

    <div class="social-table-wrapper">

        <table class="social-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Facebook</th>
                    <th>TikTok</th>
                    <th>Discord</th>
                    <th>Google</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach($users as $u)
                <tr>
                    <td>{{ $u->user_id }}</td>

                    {{-- NAMA + EMAIL --}}
                    <td>
                        <strong>{{ $u->name }}</strong>
<span class="user-email">{{ $u->email }}</span>
                    </td>

                    {{-- FACEBOOK --}}
                    <td>
                        @if($u->facebook_id)
                            <span class="badge-connected">Tertaut</span>
                        @else
                            <span class="badge-not">Belum</span>
                        @endif
                    </td>

                    {{-- TIKTOK --}}
                    <td>
                        @if($u->tiktok_username)
                            <span class="badge-connected">Tertaut</span>
                        @else
                            <span class="badge-not">Belum</span>
                        @endif
                    </td>

                    {{-- DISCORD --}}
                    <td>
                        @if($u->discord_id)
                            <span class="badge-connected">Tertaut</span>
                        @else
                            <span class="badge-not">Belum</span>
                        @endif
                    </td>

                    {{-- GOOGLE --}}
                    <td>
                        @if($u->google_id)
                            <span class="badge-connected">Tertaut</span>
                        @else
                            <span class="badge-not">Belum</span>
                        @endif
                    </td>

                    {{-- ACTION --}}
                    <td>
                        <a href="{{ route('admin.social.show', $u->user_id) }}" class="btn-detail">
                            👁 Detail
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>

    </div>

</div>

@endsection
