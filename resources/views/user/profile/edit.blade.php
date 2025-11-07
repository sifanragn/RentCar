@extends('partials.container')

@section('title', 'Edit Profile')

@section('styles')
<style>
/* ===== PROFILE HEADER ===== */
.profile-header { text-align: center; margin-bottom: 30px; }
.profile-photo-wrapper { position: relative; display: inline-block; margin-bottom: 10px; }
.profile-photo-wrapper img {
  width: 120px; height: 120px; border-radius: 50%; object-fit: cover;
  background: none; box-shadow: 0 3px 6px rgba(0,0,0,0.08);
}
.edit-icon {
  position: absolute; bottom: 3px; right: 3px; background: #333; color: #fff;
  border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center;
  justify-content: center; font-size: 14px; cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: 0.2s ease;
}
.edit-icon:hover { background: #111; transform: scale(1.05); }

/* ===== TEXT ===== */
.profile-header h4 { margin-top: 12px; font-weight: 600; font-size: 18px; }
.profile-header .text-muted { color: #777; font-size: 13.5px; }

/* ===== FORM ===== */
form {
  background: #fff; border-radius: 16px; padding: 28px 22px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}
.form-label { font-weight: 600; font-size: 14.5px; margin-bottom: 5px; }
.form-control { border-radius: 12px; padding: 11px 13px; border: 1px solid #ccc; }
.mb-3 { margin-bottom: 18px !important; }

/* ===== INPUT + VERIFIKASI BUTTON WRAPPER ===== */
.input-group {
  display: flex; align-items: center; border-radius: 12px; overflow: hidden;
}
.input-group input {
  flex: 1; padding: 12px 13px; font-size: 14px;
  border-radius: 12px 0 0 12px; border: 1px solid #ccc; background: #fafafa;
}
.input-group button.btn-verify {
  background: #000 !important; color: #fff !important;
  padding: 0 16px; height: 48px; font-size: 14px; font-weight: 600;
  display: flex; align-items: center; justify-content: center;
  border-radius: 0 12px 12px 0;
}
.input-group button.btn-verify:hover { background: #111 !important; }

/* ===== BUTTONS ===== */
.btn-update {
  width: 100%; height: 48px; background: #000 !important; color: #fff !important;
  border-radius: 12px; font-size: 15px; font-weight: 600; margin: 14px 0 10px;
}
.btn-update:hover { background: #111 !important; }

.btn-danger {
  width: 100%; height: 48px; border-radius: 12px;
  font-weight: 600; font-size: 15px;
}

.btn-outline-danger { border: 1px solid #dc3545; color: #dc3545; }
.btn-outline-danger:hover { background: #dc3545; color: #fff; }

/* Remove highlight on mobile */
button, .btn { -webkit-tap-highlight-color: transparent !important; }
button:focus, .btn:focus { box-shadow: none !important; }

/* ===== PASSWORD BOX ===== */
.password-box {
  position: relative; border: 1px solid #eee; border-radius: 12px;
  padding: 18px 15px 10px; background: #fafafa;
  margin-top: 12px; transition: .3s ease;
}
.cancel-icon {
  position: absolute; top: 10px; right: 12px; color: #dc3545;
  font-size: 17px; cursor: pointer; transition: .2s;
}
.cancel-icon:hover { transform: scale(1.2); color: #b30000; }

/* Back link */
.back-link svg { vertical-align: middle; margin-left: -155px; }

/* Container override */
.app-container { background:#fff!important; padding-top:0!important; border:none!important; }

</style>
@endsection


@section('content')

<a href="{{ route('user.profile.index') }}" class="back-link">
  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="black" stroke-width="2">
    <line x1="19" y1="12" x2="5" y2="12"/>
    <polyline points="12 19 5 12 12 5"/>
  </svg>
</a>

<form id="profileForm" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-3">
@csrf

{{-- Foto Profil --}}
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

@include('partials.verification-alert')

{{-- Fields --}}
<div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="form-control"></div>
<div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control"></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control"></div>

{{-- Password Verify --}}
<div id="verifySection" class="mb-3">
  <label class="form-label">Buat Password Baru</label>
  <div class="input-group" style="overflow:hidden; border-radius:12px;">
    <input id="current_password" type="password" name="current_password" class="form-control" placeholder="Masukkan Password Lama">
    <button class="btn btn-verify" type="button" id="verifyButton">Verifikasi</button>
  </div>
  <div id="verifyMsg" class="mt-2 text-muted" style="font-size:13px;"></div>
</div>

{{-- Password Baru --}}
<div id="passwordFields" class="password-box" style="display:none;">
  <div class="cancel-icon" id="cancelChangePw">✖</div>

  <div class="mb-3"><label class="form-label">Password Baru</label>
    <input id="password" type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
  </div>

  <div class="mb-3"><label class="form-label">Konfirmasi Password Baru</label>
    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
  </div>
</div>

<button type="submit" class="btn btn-update w-100 mt-3">Update</button>

{{-- Hapus verifikasi --}}
@if($user->status_verifikasi === 'disetujui')
<button type="button" class="btn btn-danger mt-3 w-100 small-btn" data-bs-toggle="modal" data-bs-target="#hapusVerifikasiModal">Hapus Verifikasi</button>

{{-- Modal --}}
<div class="modal fade" id="hapusVerifikasiModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm">
      <div class="modal-header"><h5 class="modal-title fw-bold">Konfirmasi Penghapusan</h5></div>
      <div class="modal-body"><div>⚠️</div><p class="fw-medium">Hapus verifikasi akun?</p><small>Data KTP & KK akan terhapus.</small></div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="{{ route('user.profile.hapusVerifikasi', 'all') }}" class="btn btn-danger">Ya, Hapus</a>
      </div>
    </div>
  </div>
</div>
@endif
</form>

@include('partials.bottom-navbar')


<script>
const fotoInput = document.getElementById('fotoInput'),
      previewFoto = document.getElementById('previewFoto');
if (fotoInput){
  fotoInput.addEventListener('change', e => previewFoto.src = URL.createObjectURL(e.target.files[0]));
}

const verifyBtn = document.getElementById('verifyButton'),
      verifyMsg = document.getElementById('verifyMsg'),
      pwSection = document.getElementById('passwordFields'),
      cancelBtn = document.getElementById('cancelChangePw');

if (verifyBtn){
  verifyBtn.addEventListener('click', async () => {
    const oldPw = document.getElementById('current_password').value.trim();
    if (!oldPw) return verifyMsg.textContent="⚠️ Masukkan password lama dulu.";

    verifyMsg.textContent="⏳ Memverifikasi...";
    const res = await fetch("{{ route('user.profile.verifyPassword') }}",{
      method:"POST",
      headers:{ "Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}" },
      body:JSON.stringify({ current_password:oldPw })
    });
    const data = await res.json();

    if(data.success){
      verifyMsg.textContent="✅ Password lama benar."; verifyMsg.style.color="#008000";
      pwSection.style.display="block"; pwSection.style.opacity="0";
      setTimeout(()=>pwSection.style.opacity="1",50);
    } else {
      verifyMsg.textContent="❌ Password salah."; verifyMsg.style.color="#c00";
      pwSection.style.display="none";
    }
  });
}

if (cancelBtn){
  cancelBtn.addEventListener('click',()=>{
    pwSection.style.display="none"; verifyMsg.textContent=""; document.getElementById('current_password').value="";
  });
}
</script>

@endsection
