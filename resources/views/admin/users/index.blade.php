@extends('layouts.admin')

@section('title', 'Verifikasi Pengguna')
@section('page_title', 'Verifikasi Pengguna')

@section('content')
@if(session('success'))
  <div class="alert">{{ session('success') }}</div>
@endif

@if($users->isEmpty())
  <p>Tidak ada user untuk diverifikasi.</p>
@else
  <table>
    <thead>
      <tr>
        <th>Nama Lengkap</th>
        <th>Email</th>
        <th>Status Verifikasi</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      <tr>
        <td>{{ $user->nama_lengkap }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ ucfirst($user->status_verifikasi) }}</td>
        <td><a href="{{ route('admin.users.show', $user->user_id) }}" style="color:#0d6efd;">Detail</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
@endif
@endsection
