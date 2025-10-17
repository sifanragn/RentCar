<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil - RentCar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f5f6fa; padding: 25px; }
    .container { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
    .profile-header { text-align: center; margin-bottom: 12px; }
    .profile-header img { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; }
    .btn-dark { background: #000; color: #fff; border: none; border-radius: 10px; padding: 10px 0; }
    .btn-danger { border-radius: 10px; padding: 10px 0; }
    .form-label { font-weight: 600; font-size: 14px; }
    .note-muted { font-size: 13px; color: #666; margin-top: 6px; }
    .pw-field { position: relative; }
    .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; }
  </style>
</head>
<body>

  <div class="container">
    <a href="{{ route('user.profile.index') }}" class="text-decoration-none">&larr; Kembali</a>

    <div class="profile-header mt-3">
      <img src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : asset('img/default-user.png') }}" alt="Foto Profil">
      <h4 class="mt-2 fw-bold text-capitalize">{{ $user->nama_lengkap }}</h4>
      <div class="text-muted">{{ '@' . $user->username }}</div>
    </div>

    {{-- STATUS VERIFIKASI --}}
    @if($user->status_verifikasi === 'belum_upload')
      <div class="alert alert-warning text-center">
        ⚠️ Anda belum melakukan verifikasi.  
        <a href="{{ route('user.verifikasi.index') }}" class="fw-semibold text-decoration-underline">Verifikasi sekarang</a>
      </div>
    @elseif($user->status_verifikasi === 'menunggu')
      <div class="alert alert-info text-center">⏳ Dokumen Anda sedang menunggu konfirmasi admin.</div>
    @elseif($user->status_verifikasi === 'ditolak')
      <div class="alert alert-danger text-center">❌ Verifikasi gagal. Silakan unggah ulang dokumen Anda. <a href="{{ route('user.verifikasi.index') }}" class="fw-semibold">Verifikasi ulang</a></div>
    @elseif($user->status_verifikasi === 'disetujui')
      <div class="alert alert-success text-center">✅ Akun Anda telah terverifikasi!</div>
    @endif

    {{-- flash messages --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form id="profileForm" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-3">
      @csrf

      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="form-control">
      </div>

      <div class="mb-3">
  <label class="form-label">Username</label>

  @if(isset($canChangeUsername) && $canChangeUsername === false)
    <input 
      type="text" 
      name="username" 
      value="{{ old('username', $user->username) }}" 
      class="form-control" 
      readonly
      style="background: #e9ecef; color: #6c757d; cursor: not-allowed;"
    >
    <div class="note-muted">Tunggu {{ $daysLeftToChangeUsername }} hari lagi untuk mengubah username.</div>
  @else
    <input 
      type="text" 
      name="username" 
      value="{{ old('username', $user->username) }}" 
      class="form-control"
      style="color:#212529;"
    >
  @endif
</div>


      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
      </div>

     {{-- PASSWORD SECTION --}}
<div id="verifySection" class="mb-3">
  <label class="form-label">Masukkan Password Lama</label>
  <div class="input-group">
    <input id="current_password" type="password" name="current_password" class="form-control" autocomplete="current-password" placeholder="Password lama">
    <button class="btn btn-outline-secondary" type="button" id="togglePwOld">👁️</button>
    <button class="btn btn-primary" type="button" id="verifyButton">Verifikasi</button>
  </div>
  <div id="verifyMsg" class="mt-2 text-muted" style="font-size: 13px;"></div>
</div>

<div id="passwordFields" style="display:none;">
  <div class="mb-3">
    <label class="form-label">Password Baru</label>
    <div class="input-group">
      <input id="password" type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
      <button class="btn btn-outline-secondary" type="button" id="togglePwNew">👁️</button>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Konfirmasi Password Baru</label>
    <div class="input-group">
      <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
      <button class="btn btn-outline-secondary" type="button" id="togglePwConfirm">👁️</button>
    </div>
  </div>

  {{-- tombol batal --}}
  <button type="button" id="cancelChangePw" class="btn btn-outline-danger w-100">Batal Mengubah Password</button>
</div>



      <button type="submit" class="btn btn-dark w-100">Update</button>

      {{-- Tombol hanya jika sudah benar-benar terverifikasi --}}
      @if($user->status_verifikasi === 'disetujui')
        <a href="{{ route('user.profile.hapusVerifikasi', 'all') }}"
           onclick="return confirm('⚠️ Yakin ingin menghapus? Anda akan menghapus data Anda yang telah diverifikasi dan harus verifikasi ulang.');"
           class="btn btn-danger mt-3 w-100">Hapus Verifikasi</a>
      @endif

    </form>
  </div>

  <script>
    // Toggle password visibility. For password fields (new/confirm), require that current_password is filled.
    document.querySelectorAll('.pw-toggle').forEach(btn => {
      btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-target');
        const target = document.getElementById(targetId);

        // if target is new password or confirmation, require current_password not empty
        if (targetId === 'password' || targetId === 'password_confirmation') {
          const current = document.getElementById('current_password');
          if (!current.value) {
            alert('Masukkan password lama terlebih dahulu untuk melihat/mengubah password baru.');
            current.focus();
            return;
          }
        }

        if (target.type === 'password') {
          target.type = 'text';
          this.textContent = '🙈';
        } else {
          target.type = 'password';
          this.textContent = '👁️';
        }
      });
    });

    // Optional: prevent submitting empty new password without current_password (handled server-side, but UX help)
    document.getElementById('profileForm').addEventListener('submit', function(e){
      const newPw = document.getElementById('password').value;
      const current = document.getElementById('current_password').value;
      if (newPw && !current) {
        e.preventDefault();
        alert('Untuk mengganti password, masukkan password lama terlebih dahulu.');
        document.getElementById('current_password').focus();
      }
    });
  </script>

    <script>
  const verifyBtn = document.getElementById('verifyButton');
  const currentPwInput = document.getElementById('current_password');
  const verifyMsg = document.getElementById('verifyMsg');
  const passwordFields = document.getElementById('passwordFields');
  const verifySection = document.getElementById('verifySection');
  const cancelBtn = document.getElementById('cancelChangePw');

  // Toggle visibility function
  const togglePw = (btnId, inputId) => {
    document.getElementById(btnId).addEventListener('click', () => {
      const input = document.getElementById(inputId);
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  };
  togglePw('togglePwOld', 'current_password');
  togglePw('togglePwNew', 'password');
  togglePw('togglePwConfirm', 'password_confirmation');

  // AJAX verification for old password
  verifyBtn.addEventListener('click', () => {
    const password = currentPwInput.value.trim();
    verifyMsg.textContent = '';
    if (!password) {
      verifyMsg.textContent = '⚠️ Masukkan password lama terlebih dahulu.';
      verifyMsg.className = 'text-danger mt-2';
      return;
    }

    verifyBtn.disabled = true;
    verifyBtn.textContent = 'Memverifikasi...';

    fetch("{{ route('user.profile.verifyPassword') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}"
      },
      body: JSON.stringify({ current_password: password })
    })
    .then(res => res.json().then(data => ({status: res.status, body: data})))
    .then(({status, body}) => {
      if (status === 200 && body.success) {
        verifyMsg.textContent = '✅ ' + body.message;
        verifyMsg.className = 'text-success mt-2';
        passwordFields.style.display = 'block';
        verifySection.style.display = 'none';
        // Kosongkan input lama setelah verifikasi
        currentPwInput.value = '';
      } else {
        verifyMsg.textContent = '❌ ' + (body.message || 'Verifikasi gagal.');
        verifyMsg.className = 'text-danger mt-2';
      }
    })
    .catch(() => {
      verifyMsg.textContent = '❌ Terjadi kesalahan.';
      verifyMsg.className = 'text-danger mt-2';
    })
    .finally(() => {
      verifyBtn.disabled = false;
      verifyBtn.textContent = 'Verifikasi';
    });
  });

  // Batal ubah password → kembali ke awal
  cancelBtn.addEventListener('click', () => {
    passwordFields.style.display = 'none';
    verifySection.style.display = 'block';
    document.getElementById('password').value = '';
    document.getElementById('password_confirmation').value = '';
    verifyMsg.textContent = '';
  });

  // Bersihkan password lama setiap kali submit form
  document.getElementById('profileForm').addEventListener('submit', function() {
    currentPwInput.value = '';
  });
</script>
</body>
</html>
