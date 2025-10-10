@extends('layouts.admin')

@section('title', 'Detail Verifikasi User')
@section('page_title', 'Detail Pengguna')

@section('content')
<div style="background:white;padding:20px;border-radius:10px;max-width:700px;">
  <p><strong>Nama Lengkap:</strong> {{ $user->nama_lengkap }}</p>
  <p><strong>Email:</strong> {{ $user->email }}</p>
  <p><strong>No HP:</strong> {{ $user->no_hp ?? '-' }}</p>
  <p><strong>Status Verifikasi:</strong> {{ ucfirst($user->status_verifikasi) }}</p>

  <hr>
  <h3>Dokumen Identitas</h3>

  @if($user->foto_ktp)
    <p><strong>KTP:</strong></p>
    <img src="{{ asset('storage/'.$user->foto_ktp) }}" alt="KTP" style="width:100%;border-radius:10px;margin-bottom:10px;">
  @endif

  @if($user->foto_kk)
    <p><strong>Kartu Keluarga:</strong></p>
    <img src="{{ asset('storage/'.$user->foto_kk) }}" alt="KK" style="width:100%;border-radius:10px;">
  @endif

  <form action="{{ route('admin.users.verify', $user->user_id) }}" method="POST" style="margin-top:20px;">
    @csrf
    <label>Status Verifikasi:</label><br>
    <select name="status_verifikasi" required>
      <option value="">-- Pilih --</option>
      <option value="disetujui">Setujui</option>
      <option value="ditolak">Tolak</option>
    </select>
    <button type="submit" style="margin-top:10px;background:#0d6efd;color:white;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;">Update</button>
  </form>
</div>
@endsection
