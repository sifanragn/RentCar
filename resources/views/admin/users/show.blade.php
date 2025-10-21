@extends('layouts.admin.app')

@section('title', 'Detail Verifikasi Pengguna')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-user-detail.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper wide">
  <div class="page-header">
    <h2>Detail Pengguna</h2>
    <p>Periksa dan kelola status verifikasi pengguna.</p>
  </div>

  <div class="detail-card wide">
    <div class="info-section">
      <h3>👤 Informasi Pengguna</h3>
      <div class="grid-info">
        <div><span>Nama Lengkap:</span> {{ $user->nama_lengkap }}</div>
        <div><span>Email:</span> {{ $user->email }}</div>
        <div><span>No HP:</span> {{ $user->no_hp ?? '-' }}</div>
        <div>
          <span>Status Verifikasi:</span>
          <span class="status {{ strtolower($user->status_verifikasi) }}">
            {{ ucfirst($user->status_verifikasi) }}
          </span>
        </div>
      </div>
    </div>

    <hr>

    <div class="docs-section">
      <h3>🪪 Dokumen Identitas</h3>
      <div class="doc-row">
        @if($user->foto_ktp)
        <div class="doc-item">
          <p><strong>KTP:</strong></p>
          <div class="doc-thumb" onclick="openImageModal('{{ asset('storage/'.$user->foto_ktp) }}')">
            <img src="{{ asset('storage/'.$user->foto_ktp) }}" alt="KTP">
          </div>
        </div>
        @endif

        @if($user->foto_kk)
        <div class="doc-item">
          <p><strong>Kartu Keluarga:</strong></p>
          <div class="doc-thumb" onclick="openImageModal('{{ asset('storage/'.$user->foto_kk) }}')">
            <img src="{{ asset('storage/'.$user->foto_kk) }}" alt="KK">
          </div>
        </div>
        @endif
      </div>
    </div>

    <div class="verify-section">
      <h3>🔍 Ubah Status Verifikasi</h3>
      <form action="{{ route('admin.users.verify', $user->user_id) }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Status Verifikasi</label>
          <select name="status_verifikasi" required>
            <option value="">-- Pilih Status --</option>
            <option value="disetujui">✅ Setujui</option>
            <option value="ditolak">❌ Tolak</option>
          </select>
        </div>
        <button type="submit" class="btn-submit">Update</button>
      </form>
    </div>
    {{-- 🔙 Tombol Kembali --}}
<div class="back-wrapper">
  <a href="{{ route('admin.users.index') }}" class="btn-back">
    ← Kembali ke Daftar
  </a>
  </div>
</div>

{{-- Modal Preview Foto --}}
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
  <span class="close-btn">&times;</span>
  <img id="modalImage" class="modal-content">
</div>

<script>
  function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'flex';
  }
  function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
  }
</script>
@endsection
