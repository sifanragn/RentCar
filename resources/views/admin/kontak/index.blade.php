@extends('layouts.admin')

@section('title', 'Pesan Kontak')
@section('page_title', 'Pesan dari Pengguna')

@section('content')
@if(session('success'))
  <div style="background:#d1e7dd;color:#0f5132;padding:10px;border-radius:6px;margin-bottom:15px;">
    {{ session('success') }}
  </div>
@endif

<table style="width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;">
  <thead style="background:#0d6efd;color:white;">
    <tr>
      <th>No</th>
      <th>Pengguna</th>
      <th>Subjek</th>
      <th>Pesan</th>
      <th>Status</th>
      <th>Balasan</th>
    </tr>
  </thead>
  <tbody>
    @foreach($messages as $i => $msg)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $msg->user->nama_lengkap ?? '-' }}</td>
        <td>{{ $msg->subject ?? '-' }}</td>
        <td>{{ $msg->message }}</td>
        <td>
          @if($msg->status === 'pending')
            <span style="color:#ffc107;font-weight:600;">Menunggu</span>
          @else
            <span style="color:#28a745;font-weight:600;">Dibalas</span>
          @endif
        </td>
        <td>
          @if($msg->reply)
            <p style="margin:0;">{{ $msg->reply }}</p>
          @else
            <form action="{{ route('admin.kontak.reply', $msg->id) }}" method="POST">
              @csrf
              <textarea name="reply" rows="2" placeholder="Tulis balasan..." style="width:100%;border-radius:6px;border:1px solid #ccc;padding:6px;"></textarea>
              <button type="submit" style="margin-top:6px;background:#0d6efd;color:white;border:none;border-radius:6px;padding:6px 10px;cursor:pointer;">Kirim</button>
            </form>
          @endif
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
@endsection
