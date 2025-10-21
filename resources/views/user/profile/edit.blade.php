@extends('partials.container')

@section('title', 'Edit Profile')

@section('styles')
<style>
/* ===== PROFILE HEADER ===== */
.profile-header {
  text-align: center;
  margin-bottom: 30px;
}
.profile-photo-wrapper {
  position: relative;
  display: inline-block;
  margin-bottom: 10px;
}
.profile-photo-wrapper img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: none;
  background: none;
  box-shadow: 0 3px 6px rgba(0,0,0,0.08);
}

/* ===== EDIT ICON (pensil) ===== */
.edit-icon {
  position: absolute;
  bottom: 3px;
  right: 3px;
  background: #0d6efd;
  color: #fff;
  border-radius: 50%;
  width: 25px;
  height: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: 0.2s ease;
}
.edit-icon:hover {
  background: #0b5ed7;
  transform: scale(1.05);
}

/* ===== NAMA & USERNAME ===== */
.profile-header h4 {
  margin-top: 12px;
  font-weight: 600;
  font-size: 18px;
}
.profile-header .text-muted {
  color: #777;
  font-size: 13.5px;
}

/* ===== FORM ===== */
form {
  background: #fff;
  border-radius: 16px;
  padding: 28px 22px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}
.form-label {
  font-weight: 600;
  font-size: 14.5px;
  margin-bottom: 5px;
}
.form-control {
  border-radius: 12px;
  border: 1px solid #ccc;
  padding: 11px 13px;
  font-size: 14px;
}
.form-control:focus {
  border-color: #0d6efd;
  box-shadow: none;
}
.mb-3 {
  margin-bottom: 18px !important;
}

/* ===== BUTTONS ===== */
.btn-primary,
.btn-danger,
.btn-outline-danger {
  font-size: 14px;
  height: 38px;
  border-radius: 10px;
  font-weight: 500;
  transition: 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-primary {
  background: #0d6efd;
  color: #fff;
  border: none;
}
.btn-primary:hover {
  background: #0b5ed7;
}
.btn-outline-danger {
  border: 1px solid #dc3545;
  color: #dc3545;
}
.btn-outline-danger:hover {
  background: #dc3545;
  color: #fff;
}
.btn-danger {
  background: #c91d2e;
  border: none;
  color: #fff;
  margin-bottom: 25px;
}
.btn-danger:hover {
  background: #a31522;
}

/* ===== ALERT ===== */
.alert {
  border-radius: 12px;
  padding: 10px 14px;
  font-size: 14.2px;
  line-height: 1.5;
  margin: 0 auto 18px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

/* ===== PASSWORD FIELD ===== */
.input-group {
  display: flex;
  align-items: center;
}
.input-group input {
  border-radius: 10px 0 0 10px;
  padding: 10px 12px;
  height: 40px;
}
.input-group button {
  border-radius: 0 10px 10px 0;
  height: 40px;
}

/* ===== PASSWORD BARU BOX ===== */
.password-box {
  position: relative;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 18px 15px 10px;
  margin-top: 12px;
  background: #fafafa;
  transition: all 0.3s ease;
}
.cancel-icon {
  position: absolute;
  top: 10px;
  right: 12px;
  color: #dc3545;
  font-size: 17px;
  cursor: pointer;
  transition: 0.2s;
}
.cancel-icon:hover {
  transform: scale(1.2);
  color: #b30000;
}

/* ===== MODAL CUSTOM (versi rapi & elegan) ===== */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: fadeIn 0.25s ease-in-out;
}

.modal-header {
  border-bottom: none;
  text-align: center;
  justify-content: center;
  padding-top: 20px;
}

.modal-header .modal-title {
  font-weight: 700;
  color: #1f2937;
  font-size: 18px;
}

.modal-body {
  padding: 1.8rem 1.6rem 1.4rem;
  text-align: center;
}

.modal-body div {
  font-size: 36px;
  margin-bottom: 10px;
}

.modal-body p {
  font-weight: 500;
  color: #111;
  font-size: 15px;
  margin-bottom: 6px;
}

.modal-body small {
  display: block;
  color: #6b7280;
  font-size: 13.5px;
  line-height: 1.4;
}

.modal-footer {
  border-top: none;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 14px;
  padding-bottom: 22px;
}

/* Tombol sejajar dan proporsional */
.modal-footer .btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 42px;
  min-width: 120px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 14.2px;
  transition: 0.25s ease;
}

.modal-footer .btn-outline-secondary {
  border-color: #d1d5db;
  color: #374151;
  background: #fff;
}

.modal-footer .btn-outline-secondary:hover {
  background: #f3f4f6;
  color: #111827;
}

.modal-footer .btn-danger {
  background: #dc2626;
  color: #fff;
  border: none;
  margin-bottom: 0 !important;
}

.modal-footer .btn-danger:hover {
  background: #b91c1c;
}

/* Responsif */
@media (max-width: 576px) {
  .modal-dialog {
    max-width: 92%;
    margin: auto;
  }
  .modal-footer {
    flex-direction: row;
    gap: 10px;
  }
  .modal-footer .btn {
    flex: 1;
    height: 40px;
    font-size: 13.5px;
  }
}

/* ===== BACK LINK ===== */
.back-link svg {
  vertical-align: middle;
  margin-left: -155px;
}
</style>
@endsection

@section('content')
{{-- Link Kembali --}}
<a href="{{ route('user.profile.index') }}" class="back-link" aria-label="Kembali">
  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
    <line x1="19" y1="12" x2="5" y2="12"/>
    <polyline points="12 19 5 12 12 5"/>
  </svg>
</a>

<form id="profileForm" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-3">
  @csrf

  {{-- FOTO PROFIL --}}
  <div class="profile-header">
    @php
      $foto = $user->foto_profil && file_exists(public_path('storage/'.$user->foto_profil))
        ? asset('storage/'.$user->foto_profil)
        : asset('images/guest.png');
    @endphp

    <div class="profile-photo-wrapper">
      <img id="previewFoto" src="{{ $foto }}" alt="Foto Profil">
      <div class="edit-icon" onclick="document.getElementById('fotoInput').click();">
        <i class="fas fa-pen"></i>
      </div>
      <input type="file" name="foto_profil" id="fotoInput" accept="image/*" style="display:none;">
    </div>

    <h4 class="fw-bold text-capitalize">{{ $user->nama_lengkap }}</h4>
    <div class="text-muted">{{ '@' . $user->username }}</div>
  </div>

  {{-- STATUS VERIFIKASI --}}
  @if($user->status_verifikasi === 'belum_upload')
    <div class="alert alert-warning text-center">
      ⚠️ Anda belum melakukan verifikasi.<br>
      <a href="{{ route('user.verifikasi.index') }}">Verifikasi sekarang</a>
    </div>
  @elseif($user->status_verifikasi === 'menunggu')
    <div class="alert alert-info text-center">⏳ Dokumen Anda sedang menunggu konfirmasi admin.</div>
  @elseif($user->status_verifikasi === 'ditolak')
    <div class="alert alert-danger text-center">
      ❌ Verifikasi gagal. Silakan unggah ulang dokumen Anda.<br>
      <a href="{{ route('user.verifikasi.index') }}">Verifikasi ulang</a>
    </div>
  @elseif($user->status_verifikasi === 'disetujui')
    <div class="alert alert-success text-center">✅ Akun Anda telah terverifikasi!</div>
  @endif

  {{-- FLASH MESSAGE --}}
  @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger text-center">{{ session('error') }}</div>
  @endif

  {{-- FORM FIELD --}}
  <div class="mb-3">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
  </div>

  {{-- PASSWORD --}}
  <div id="verifySection" class="mb-3">
    <label class="form-label">Masukkan Password Lama</label>
    <div class="input-group" style="overflow:hidden; border-radius:12px;">
      <input id="current_password" type="password" name="current_password"
             class="form-control" placeholder="Password lama"
             style="border-radius:12px 0 0 12px;">
      <button class="btn btn-primary" type="button" id="verifyButton"
              style="border-radius:0 12px 12px 0;">Verifikasi</button>
    </div>
    <div id="verifyMsg" class="mt-2 text-muted" style="font-size:13px;"></div>
  </div>

  {{-- PASSWORD BARU --}}
  <div id="passwordFields" class="password-box" style="display:none;">
    <div class="cancel-icon" id="cancelChangePw" title="Batal Mengubah Password">✖</div>

    <div class="mb-3">
      <label class="form-label">Password Baru</label>
      <input id="password" type="password" name="password" class="form-control"
             placeholder="Kosongkan jika tidak ingin mengubah">
    </div>

    <div class="mb-3">
      <label class="form-label">Konfirmasi Password Baru</label>
      <input id="password_confirmation" type="password" name="password_confirmation"
             class="form-control" placeholder="Ulangi password baru">
    </div>
  </div>

  <button type="submit" class="btn btn-primary w-100 mt-3 small-btn">Update</button>

{{-- MODAL HAPUS VERIFIKASI --}}
@if($user->status_verifikasi === 'disetujui')
<button type="button" class="btn btn-danger mt-3 w-100 small-btn"
        data-bs-toggle="modal" data-bs-target="#hapusVerifikasiModal">
  Hapus Verifikasi
</button>

<div class="modal fade" id="hapusVerifikasiModal" tabindex="-1" aria-labelledby="hapusVerifikasiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="hapusVerifikasiLabel">Konfirmasi Penghapusan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <div class="modal-body">
        <div>⚠️</div>
        <p class="fw-medium mb-1 mt-2">Apakah Anda yakin ingin menghapus verifikasi akun?</p>
        <small>Data KTP & KK Anda akan terhapus, dan Anda perlu mengunggah ulang untuk verifikasi baru.</small>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="{{ route('user.profile.hapusVerifikasi', 'all') }}" class="btn btn-danger">Ya, Hapus</a>
      </div>
    </div>
  </div>
</div>
@endif
</form>

@include('partials.bottom-navbar')

<script>
  // === Preview Foto Profil ===
  const fotoInput = document.getElementById('fotoInput');
  const previewFoto = document.getElementById('previewFoto');
  if (fotoInput) {
    fotoInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (file) previewFoto.src = URL.createObjectURL(file);
    });
  }

  // === Verifikasi Password Lama (AJAX ke Backend) ===
  const verifyBtn = document.getElementById('verifyButton');
  const verifyMsg = document.getElementById('verifyMsg');
  const pwSection = document.getElementById('passwordFields');
  const cancelBtn = document.getElementById('cancelChangePw');

  if (verifyBtn) {
    verifyBtn.addEventListener('click', async () => {
      const oldPw = document.getElementById('current_password').value.trim();
      if (!oldPw) {
        verifyMsg.textContent = "⚠️ Silakan isi password lama terlebih dahulu.";
        verifyMsg.style.color = "#c00";
        return;
      }

      // Tampilkan status loading
      verifyMsg.textContent = "⏳ Memverifikasi...";
      verifyMsg.style.color = "#555";

      try {
const res = await fetch("{{ route('user.profile.verifyPassword') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
          },
          body: JSON.stringify({ current_password: oldPw }) // ✅ field cocok dgn controller
        });

        const data = await res.json();

        if (data.success) {
          // ✅ Password benar
          verifyMsg.textContent = "✅ Password lama benar.";
          verifyMsg.style.color = "#008000";
          pwSection.style.display = "block";
          pwSection.style.opacity = "0";
          setTimeout(() => pwSection.style.opacity = "1", 50);
        } else {
          // ❌ Password salah
          verifyMsg.textContent = "❌ Password lama salah.";
          verifyMsg.style.color = "#c00";
          pwSection.style.display = "none";
        }

      } catch (err) {
        console.error(err);
        verifyMsg.textContent = "❌ Terjadi kesalahan saat verifikasi.";
        verifyMsg.style.color = "#c00";
      }
    });
  }

  // === Tombol Batal Ubah Password ===
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      pwSection.style.display = "none";
      verifyMsg.textContent = "";
      document.getElementById('current_password').value = "";
    });
  }
</script>
@endsection
