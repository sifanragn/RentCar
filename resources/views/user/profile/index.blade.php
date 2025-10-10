<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unggah KTP & KK</title>
  <style>
    body { font-family:'Segoe UI',Arial,sans-serif;background:#f8f9fa;padding:30px; }
    .card { background:white;border-radius:10px;padding:25px;max-width:500px;margin:auto;box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { text-align:center;margin-bottom:20px; }
    label { display:block;margin-top:10px;font-weight:600; }
    input[type=file] { width:100%;margin-top:5px; }
    button { margin-top:20px;background:#0d6efd;color:white;padding:10px 14px;border:none;border-radius:6px;cursor:pointer; }
    button:hover { background:#0b5ed7; }
  </style>
</head>
<body>

  <div class="card">
    <h2>Unggah Dokumen Identitas</h2>

    @if(session('error'))
      <div style="background:#f8d7da;color:#842029;padding:10px;border-radius:6px;margin-bottom:10px;">
        {{ session('error') }}
      </div>
    @endif

    @if(session('success'))
      <div style="background:#d1e7dd;color:#0f5132;padding:10px;border-radius:6px;margin-bottom:10px;">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <label for="foto_ktp">Foto KTP</label>
      <input type="file" name="foto_ktp" id="foto_ktp" accept="image/*" required>

      <label for="foto_kk">Foto KK</label>
      <input type="file" name="foto_kk" id="foto_kk" accept="image/*" required>

      <button type="submit">Kirim untuk Verifikasi</button>
    </form>
  </div>

</body>
</html>
