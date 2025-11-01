@extends('partials.container')

@section('title', 'Upload Dokumen')

@section('styles')
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f2f2f2;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
  padding: 15px;
}

.cards-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
  width: 100%;
  max-width: 380px;
}

.card {
  background: #fff;
  border-radius: 15px;
  padding: 20px;
  width: 100%;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

h3 {
  font-weight: 600;
  text-align: center;
  font-size: 18px;
}

.upload-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  border: 2px dashed #ccc;
  border-radius: 10px;
  padding: 15px;
  cursor: pointer;
  transition: 0.3s;
}

.upload-section:hover { border-color: #007bff; }
.upload-section img { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; }
input[type="file"] { display: none; }
.file-name { font-size: 13px; color: #333; text-align: center; }
.upload-guideline { font-size: 12px; color: #555; padding-left: 20px; }
.upload-guideline li { margin-bottom: 5px; }
.back-link { color: #000; text-decoration: none; font-weight: 250; font-size: 14px; margin-bottom: 8px; }
.back-link:hover { text-decoration: underline; }

.submit-button {
  margin-top: 20px;
  padding: 10px 0;
  border-radius: 8px;
  background: #70D972;
  color: #000;
  border: none;
  cursor: pointer;
  font-size: 16px;
  width: 100%;
  max-width: 380px;
  text-align: center;
  margin-bottom: 50px;
}
.submit-button:hover { background: #5AC260; }

@media (max-width: 360px) {
  .card { padding: 15px; }
  .upload-section img { width: 80px; height: 80px; }
  h3 { font-size: 16px; }
  .file-name { font-size: 12px; }
}
</style>
@endsection

@section('content')
<form action="{{ route('user.verifikasi.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

    <div class="cards-container">
        <a href="{{ $redirectTo }}" class="back-link">← Kembali</a>

        <div class="card">
            <h3>Upload KTP</h3>
            <label for="input-ktp" class="upload-section">
                <img src="{{ asset('images/upload.png') }}" alt="Upload KTP" id="preview-ktp">
                <input type="file" accept="image/*" id="input-ktp" name="ktp" required>
                <div class="file-name" id="filename-ktp">Belum ada file dipilih</div>
            </label>
            <ul class="upload-guideline">
                <li>Pastikan tidak buram atau terpotong</li>
                <li>Seluruh bagian terlihat jelas</li>
                <li>Gunakan format JPG, JPEG, atau PNG</li>
                <li>Ukuran maksimal file 2 MB</li>
            </ul>
        </div>

        <div class="card">
            <h3>Upload KK</h3>
            <label for="input-kk" class="upload-section">
                <img src="{{ asset('images/upload.png') }}" alt="Upload KK" id="preview-kk">
                <input type="file" accept="image/*" id="input-kk" name="kk" required>
                <div class="file-name" id="filename-kk">Belum ada file dipilih</div>
            </label>
            <ul class="upload-guideline">
                <li>Pastikan tidak buram atau terpotong</li>
                <li>Seluruh bagian terlihat jelas</li>
                <li>Gunakan format JPG, JPEG, atau PNG</li>
                <li>Ukuran maksimal file 2 MB</li>
            </ul>
        </div>

        <button class="submit-button" type="submit">Kirim</button>
    </div>
</form>

@include('partials.bottom-navbar')

<script>
function previewImage(inputId, previewId, filenameId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const filename = document.getElementById(filenameId);

    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
            filename.textContent = file.name;
        } else {
            preview.src = "{{ asset('images/upload.png') }}";
            filename.textContent = 'Belum ada file dipilih';
        }
    });
}

previewImage('input-ktp', 'preview-ktp', 'filename-ktp');
previewImage('input-kk', 'preview-kk', 'filename-kk');
</script>
@endsection
