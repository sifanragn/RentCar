@extends('layouts.admin.app')

@section('title', 'Verifikasi Pengguna')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-users-verifikasi.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  <div class="page-header">
    <h2>Verifikasi Pengguna</h2>
    <p>Kelola dan pantau status verifikasi pengguna di sistem.</p>
  </div>

  {{-- Alert sukses --}}
  @if(session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @endif

  {{-- Jika kosong --}}
  @if($users->isEmpty())
    <p class="empty-text">Tidak ada user untuk diverifikasi.</p>
  @else
    <div class="table-wrapper">
      <table class="user-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>Status Verifikasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>#{{ $user->user_id }}</td>
            <td>{{ $user->nama_lengkap }}</td>
            <td>{{ $user->email }}</td>
            <td>
              <span class="status {{ strtolower($user->status_verifikasi) }}">
                {{ ucfirst($user->status_verifikasi) }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn-view">
                <i class="bi bi-eye"></i> Detail
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
