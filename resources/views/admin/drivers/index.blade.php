@extends('layouts.admin.app')

@section('title', 'Kelola Driver')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/drivers-index.css') }}">
@endsection

@section('content')
<div class="admin-content-wrapper">
  {{-- Header --}}
  <div class="page-header">
    <h2>👨‍✈️ Daftar Driver</h2>
    <a href="{{ route('admin.drivers.create') }}" class="btn-primary">+ Tambah Driver</a>
  </div>

  {{-- Notifikasi sukses --}}
  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- Daftar driver --}}
  @if($drivers->isEmpty())
    <p>Belum ada driver yang terdaftar.</p>
  @else
    <div class="drivers-card">
      <table class="driver-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Nomor HP</th>
            <th>Lokasi</th>
            <th>Tarif / Jam</th>
            <th>Status</th>
            <th>Verifikasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($drivers as $i => $driver)
          <tr>
            <td>{{ $i+1 }}</td>
            <td><img src="{{ $driver->foto_url }}" alt="foto" class="driver-thumb"></td>
            <td>{{ $driver->nama }}</td>
            <td>{{ $driver->no_hp ?? '-' }}</td>
            <td>{{ $driver->lokasi ?? '-' }}</td>
            <td>{{ $driver->harga_formatted }}</td>
            <td>
              <span class="badge {{ $driver->status == 'aktif' ? 'active' : 'inactive' }}">
                {{ ucfirst($driver->status) }}
              </span>
            </td>
            <td>
              <span class="badge {{ $driver->status_verifikasi == 'disetujui' ? 'verified' : 'pending' }}">
                {{ ucfirst($driver->status_verifikasi) }}
              </span>
            </td>
            <td class="driver-actions">
              <button type="button" class="btn-view" onclick="openDriverDetail({{ $driver->toJson() }})">
              👁 Lihat
            </button>
              <a href="{{ route('admin.drivers.edit', $driver->driver_id) }}" class="btn-edit">✏ Edit</a>
              <form action="{{ route('admin.drivers.destroy', $driver->driver_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus driver ini?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">🗑 Hapus</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

<!-- Modal Detail Driver -->
<div id="driverModal" class="driver-modal-overlay" style="display:none;">
  <div class="driver-modal">

    <button class="driver-modal-close" onclick="closeDriverDetail()">✖</button>

    <!-- Foto -->
    <img id="modalFoto" src="" class="driver-detail-foto">

    <h2 id="modalNama" class="driver-name"></h2>

    <div class="driver-info-grid">
      <div><strong>Nomor HP</strong><span id="modalHp"></span></div>
      <div><strong>Email</strong><span id="modalEmail"></span></div>
      <div><strong>No. SIM</strong><span id="modalSim"></span></div>
      <div><strong>Lokasi</strong><span id="modalLokasi"></span></div>
      <div><strong>Pengalaman</strong><span id="modalPengalaman"></span></div>
      <div><strong>Tarif / Jam</strong><span id="modalHarga"></span></div>
      <div style="grid-column: 1 / -1;">
        <strong>Deskripsi</strong><span id="modalDeskripsi"></span>
      </div>
    </div>

    <!-- Dokumen -->
    <div class="driver-docs">
      <h4>📎 Dokumen Identitas</h4>

      <div class="doc-grid">
        <div class="doc-card">
          <p><strong>SIM</strong></p>
          <img id="docSim" src="" class="doc-img" onclick="showFullImage(this.src)">
        </div>

        <div class="doc-card">
          <p><strong>KTP</strong></p>
          <img id="docKtp" src="" class="doc-img" onclick="showFullImage(this.src)">
        </div>

        <div class="doc-card">
          <p><strong>KK</strong></p>
          <img id="docKk" src="" class="doc-img" onclick="showFullImage(this.src)">
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Full Image Preview -->
<div id="fullImgOverlay">
  <img id="fullImg">
</div>
</div>



{{-- ========================================= --}}
{{--                 STYLE                     --}}
{{-- ========================================= --}}
<style>
  :root {
  --bg: #fff;
  --text: #222;
}

/* === WRAPPER UTAMA === */
.admin-content-wrapper {
  padding: 10px 30px 50px 30px;
  max-width: 1300px;
  margin: 0 auto;
}

/* === HEADER === */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding: 18px 24px;
  border-radius: 14px;
  background: #181c26;
  box-shadow: 0 2px 12px rgba(0,0,0,0.3);
  margin-left: -10px;
}
body.light-mode .page-header {
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.page-header h2 {
  font-size: 24px;
  font-weight: 700;
  color: #f5f7fa;
  display: flex;
  align-items: center;
  gap: 10px;
}
body.light-mode .page-header h2 { color: #222; }

/* === TOMBOL TAMBAH DRIVER === */
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0d6efd, #2563eb);
  color: #fff;
  font-weight: 600;
  padding: 10px 20px;
  font-size: 15px;
  border-radius: 8px;
  text-decoration: none;
  box-shadow: 0 3px 8px rgba(0,0,0,0.25);
  border: none;
  transition: all 0.25s ease;
}
.btn-primary:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

/* === CARD WRAPPER === */
.drivers-card {
  background: #fff;
  border-radius: 16px;
  padding: 28px 32px;
  margin-left: -10px;
  margin-right: 20px;
  width: calc(100% - 10px);
  box-shadow: 0 3px 12px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  overflow-x: hidden;
}
body:not(.light-mode) .drivers-card {
  background: #141821;
  box-shadow: 0 3px 12px rgba(0,0,0,0.35);
}

/* === TABLE === */
.driver-table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
  margin-left: -20px;
}
.driver-table th, .driver-table td {
  padding: 14px 16px;
  text-align: left;
  border-bottom: 1px solid #eee;
  transition: all 0.3s ease;
}
.driver-table th {
  background: #f5f7fb;
  color: #333;
  font-weight: 700;
  font-size: 14px;
}
.driver-table td { color: #444; }
.driver-table tr:hover { background: #f8fbff; }

/* === DARK MODE TABLE === */
body:not(.light-mode) .driver-table th {
  background: #1e2430;
  color: #9fc8ff;
}
body:not(.light-mode) .driver-table td {
  color: #e1e5ec;
  border-color: rgba(255,255,255,0.08);
}
body:not(.light-mode) .driver-table tr:hover {
  background: rgba(255,255,255,0.05);
}

/* === FOTO DRIVER === */
.driver-thumb {
  width: 55px;
  height: 55px;
  border-radius: 8px;
  object-fit: cover;
  border: 2px solid #ddd;
  transition: 0.25s ease;
}
body:not(.light-mode) .driver-thumb { border-color: rgba(255,255,255,0.1); }
.driver-thumb:hover {
  transform: scale(1.1);
  border-color: #23c8ff;
}

/* === AKSI TOMBOL === */
.driver-actions {
  display: flex;
  gap: 8px;
  white-space: nowrap;
}
.driver-actions a,
.driver-actions button {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 6px 10px;
  font-size: 14px;
  font-weight: 500;
  border-radius: 6px;
  border: 1px solid transparent;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

/* === WARNA TOMBOL (KONSISTEN SEMUA MODE) === */
.btn-view {
  color: #16a085 !important;
  background: rgba(22,160,133,0.15) !important;
  border: 1px solid rgba(22,160,133,0.25) !important;
}
.btn-view:hover {
  background: rgba(22,160,133,0.25) !important;
  transform: translateY(-2px);
  filter: brightness(1.15);
}
body.light-mode .btn-view {
  color: #16a085 !important;
  background: rgba(22,160,133,0.15) !important;
  border: 1px solid rgba(22,160,133,0.25) !important;
}
body.light-mode .btn-view:hover {
  background: rgba(22,160,133,0.25) !important;
}

.btn-edit {
  color: #007bff;
  background: rgba(0,123,255,0.15);
  border: 1px solid rgba(0,123,255,0.25);
}
.btn-delete {
  color: #e74c3c;
  background: rgba(231,76,60,0.15);
  border: 1px solid rgba(231,76,60,0.25);
}
.btn-edit:hover,
.btn-delete:hover {
  transform: translateY(-2px);
  filter: brightness(1.2);
}

/* === ALERT === */
.alert-success {
  background: rgba(40,167,69,0.15);
  color: #28a745;
  padding: 12px 18px;
  border-radius: 10px;
  margin-bottom: 20px;
  border: 1px solid rgba(40,167,69,0.25);
  font-weight: 500;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }
  .driver-table th, .driver-table td {
    padding: 10px 12px;
    font-size: 14px;
  }
  .driver-thumb { width: 45px; height: 45px; }
}


/* MODAL WRAPPER */
.driver-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(6px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 3000;
}

/* MODAL BOX */
.driver-modal {
  background: var(--bg);
  padding: 28px;
  border-radius: 18px;
  width: 520px;
  max-height: 85vh;
  overflow-y: auto;
  text-align: center;
  box-shadow: 0 6px 28px rgba(0,0,0,.25);
  position: relative;
}
body.light-mode .driver-modal { --bg:#fff; }
body:not(.light-mode) .driver-modal { --bg:#171c26; }

/* Close btn */
.driver-modal-close {
  position: absolute;
  top: 12px;
  right: 12px;
  border: none;
  background: #ff5c5c;
  color: #fff;
  width: 26px;
  height: 26px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
}

/* FOTO DRIVER */
.driver-detail-foto {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  object-fit: cover;
  margin-bottom: 10px;
}

/* INFO */
.driver-modal p {
  margin: 4px 0;
  font-size: 14px;
  font-weight: 500;
  color: var(--text);
}
.driver-modal strong {
  color: var(--primary);
}
body.light-mode .driver-modal { --text:#222; --primary:#007bff }
body:not(.light-mode) .driver-modal { --text:#dce2f0; --primary:#4da3ff }

/* ACTION buttons */
.driver-modal-actions {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin: 14px 0;
}

/* DOC SECTION */
.driver-docs h4 {
  margin: 12px 0 8px;
  font-size: 15px;
  font-weight: 600;
  color: var(--text);
}

/* Thumbnail docs */
.doc-grid {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.doc-card {
  width: 95px;
}

.doc-card p {
  font-size: 12px;
  margin-bottom: 4px;
}

.doc-img {
  width: 95px;
  height: 95px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid rgba(0,0,0,.2);
  cursor: pointer;
  transition: .15s;
}
.doc-img:hover {
  transform: scale(1.05);
}

/* FULL IMAGE */
#fullImgOverlay {
  position: fixed;
  inset: 0;
  display: none;
  justify-content: center;
  align-items: center;
  background: rgba(0,0,0,.9);
  z-index: 5000;
}

#fullImg {
  max-width: 90%;
  max-height: 90%;
  border-radius: 12px;
}

.driver-name {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--text);
}

.driver-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 30px;
  justify-content: center;
  margin: 0 auto 18px;
  max-width: 320px; /* sebelumnya 380px */
  text-align: center; /* pusatkan isi */
}
.driver-info-grid div {
  display: flex;
  flex-direction: column;
  font-size: 13px;
  align-items: center; /* biar label & value sejajar */
}
.driver-info-grid strong {
  font-size: 12px;
  color: #6d6d6d;
  margin-bottom: 2px;
}
.driver-info-grid span {
  font-weight: 600;
  color: var(--text);
}


</style>
<script>
function openDriverDetail(driver) {
  document.getElementById("modalFoto").src = driver.foto_url;
  document.getElementById("modalNama").innerText = driver.nama;
  document.getElementById("modalHp").innerText = driver.no_hp ?? "-";
  document.getElementById("modalEmail").innerText = driver.email ?? "-";
  document.getElementById("modalSim").innerText = driver.sim_number ?? "-";
  document.getElementById("modalLokasi").innerText = driver.lokasi ?? "-";
  document.getElementById("modalPengalaman").innerText = driver.pengalaman ?? "-";
  document.getElementById("modalHarga").innerText = driver.harga_formatted;
  document.getElementById("modalDeskripsi").innerText = driver.deskripsi ?? "-";

  document.getElementById("docSim").src = driver.foto_sim_url;
  document.getElementById("docKtp").src = driver.foto_ktp_url;
  document.getElementById("docKk").src = driver.foto_kk_url;

  document.getElementById("driverModal").style.display = "flex";
}

function closeDriverDetail() {
  document.getElementById("driverModal").style.display = "none";
}

function showFullImage(src) {
  document.getElementById("fullImg").src = src;
  document.getElementById("fullImgOverlay").style.display = "flex";
}

document.getElementById("fullImgOverlay").onclick = () => {
  document.getElementById("fullImgOverlay").style.display = "none";
};

</script>
@endsection
