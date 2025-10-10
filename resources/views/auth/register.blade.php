<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Akun</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif; background:#f8f9fa; margin:0; padding:40px; }
    .card { background:white; padding:25px; max-width:500px; margin:auto; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { text-align:center; margin-bottom:20px; }
    label { display:block; margin-top:10px; font-weight:600; color:#333; }
    input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
      width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;
    }
    button {
      width:100%; padding:10px; background:#0d6efd; color:white; border:none; border-radius:6px; margin-top:20px;
      font-size:16px; cursor:pointer;
    }
    button:hover { background:#0b5ed7; }
    .note { background:#fff3cd; color:#856404; padding:10px; border-radius:6px; font-size:13px; margin-top:10px; }
    .alert { background:#d1e7dd; color:#0f5132; padding:10px; border-radius:6px; margin-bottom:15px; }
  </style>
</head>
<body>

  <div class="card">
    <h2>Daftar Akun Baru</h2>

    @if(session('success'))
      <div class="alert">{{ session('success') }}</div>
    @endif

    <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <label>Nama Lengkap</label>
      <input type="text" name="nama_lengkap" required>

      <label>Username</label>
      <input type="text" name="username" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Konfirmasi Password</label>
      <input type="password" name="password_confirmation" required>

      <div class="note">
        ⚠️ Upload <b>KTP & KK</b> di bawah ini bersifat opsional.  
        Jika belum tersedia, Anda bisa mengunggahnya nanti dari menu profil.
      </div>

      <label>Upload Foto KTP (Opsional)</label>
      <input type="file" name="foto_ktp" accept="image/*">

      <label>Upload Foto KK (Opsional)</label>
      <input type="file" name="foto_kk" accept="image/*">

      <button type="submit">Daftar</button>
    </form>

    <p style="text-align:center;margin-top:15px;">
      Sudah punya akun? <a href="{{ route('login') }}" style="color:#0d6efd;text-decoration:none;">Login di sini</a>
    </p>
  </div>

</body>
</html>
