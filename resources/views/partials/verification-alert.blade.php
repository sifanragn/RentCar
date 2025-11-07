<style>
/* ===== Alert Styling ===== */
.alert {
  width: 100%;
  max-width: 600px;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 12px;
  line-height: 1.45;
  font-weight: 500;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  font-family: 'Poppins', sans-serif;
  margin: 10px auto 20px;
  display: block;
}

/* link di dalam alert */
.alert a {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 600;
}
.alert a:hover {
  text-decoration: underline;
}

/* warna background & teks */
.alert-warning {
  background: #fff3cd;
  color: #856404;
}
.alert-info {
  background: #d1ecf1;
  color: #0c5460;
}
.alert-danger {
  background: #f8d7da;
  color: #842029;
  border: 1px solid #f5c2c7;
}

/* ✅ Container biar tidak nempel pinggir */
.alert-container {
  padding: 0 12px;
}

/* extra untk tampilan HP */
@media (max-width: 480px) {
  .alert {
    font-size: 11px;
    padding: 10px 12px;
  }
}
</style>

{{-- ✅ Alert Status Verifikasi --}}
@if(auth()->check())
<div class="alert-container">
  @php 
    $status = auth()->user()->status_verifikasi;
  @endphp

  @if($status === 'belum_upload')
    <div class="alert alert-warning">
      ⚠️ Kamu belum mengunggah dokumen identitas (KTP & KK).<br>
      <a href="{{ route('user.verifikasi.index') }}">Klik di sini untuk verifikasi sekarang</a>
    </div>

  @elseif($status === 'menunggu')
    <div class="alert alert-info">
      Dokumen kamu sedang diperiksa admin. Harap tunggu ya!
    </div>

  @elseif($status === 'ditolak')
    <div class="alert alert-danger">
      Verifikasi gagal. Silakan unggah ulang dokumen kamu.<br>
      <a href="{{ route('user.verifikasi.index') }}">Verifikasi ulang</a>
    </div>
  @endif
</div>
@endif
