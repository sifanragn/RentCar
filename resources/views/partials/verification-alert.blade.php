<style>
/* ===== Alert Styling ===== */
.alert {
  width: 110%;
  max-width: 600px;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 10px;
  line-height: 1.4;
  font-weight: 500;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  font-family: 'Poppins', sans-serif;
  margin-bottom: 25px;
  height: 50px;
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

/* ❌ Warna untuk gagal verifikasi */
.alert-danger {
  background: #f8d7da; /* merah lembut */
  color: #842029; /* teks merah gelap */
  border: 1px solid #f5c2c7; /* outline senada */
}
</style>

{{-- ✅ Alert dinamis sesuai status verifikasi --}}
@if(auth()->check())
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
      ⏳ Dokumen kamu sedang dalam proses verifikasi. Mohon tunggu konfirmasi admin.
    </div>

  {{-- ❌ Verifikasi gagal --}}
  @elseif($status === 'ditolak')
    <div class="alert alert-danger">
      ❌ Verifikasi gagal. Silakan unggah ulang dokumen kamu.<br>
      <a href="{{ route('user.verifikasi.index') }}">Verifikasi ulang</a>.
    </div>
  @endif
@endif
