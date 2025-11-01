@extends('partials.container')

@section('title', 'Profile User')

@section('styles')
<style>
/* === HEADER === */
.profile-header {
  background: #000;
  border-radius: 14px;
  padding: 10px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  color: #fff;
  width: 92%;
  margin: 25px auto 0;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}
.profile-photo {
  width: 58px;
  height: 58px;
  border-radius: 50px;
  object-fit: cover;
}
.profile-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.profile-info h5 {
  margin: 0;
  font-weight: 600;
  font-size: 16px;
}
.profile-info .username {
  color: #ccc;
  font-size: 13px;
  margin-top: 3px;
}

/* === MENU SECTION === */
.menu-section {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  overflow: hidden;
  margin: 20px auto 0;
  width: 92%;
}
.menu-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-bottom: 1px solid #eee;
  transition: background 0.25s ease, transform 0.1s ease;
  cursor: pointer;
}
.menu-item:hover {
  background: #f9f9f9;
  transform: scale(1.01);
}
.menu-item:last-child {
  border-bottom: none;
}
.menu-item-left {
  display: flex;
  align-items: center;
}
.menu-item-left i {
  background: #000;
  color: #fff;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 14px;
  font-size: 15px;
}
.menu-text span {
  font-weight: 600;
  font-size: 15px;
  color: #222;
}
.menu-text small {
  display: block;
  color: #666;
  font-size: 12.5px;
  margin-top: 2px;
}
.menu-item .fa-chevron-right {
  color: #000;
  font-size: 15px;
}

/* === LOGOUT SECTION === */
.logout-section {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  margin: 20px auto 80px;
  width: 92%;
}

/* === MODAL LOGOUT === */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(3px);
}
.modal-overlay.active { display: flex; }

.modal-box {
  background: #fff;
  border-radius: 18px;
  width: 90%;
  max-width: 340px;
  padding: 26px 24px;
  text-align: center;
  box-shadow: 0 8px 25px rgba(0,0,0,0.25);
  transform: scale(0.95);
  opacity: 0;
  transition: all 0.25s ease;
}
.modal-box.active {
  transform: scale(1);
  opacity: 1;
}
.modal-box i {
  font-size: 42px;
  color: #ffc107;
  margin-bottom: 12px;
}
.modal-box h4 {
  color: #000;
  font-weight: 700;
  font-size: 18px;
  margin-bottom: 8px;
}
.modal-box p {
  color: #555;
  font-size: 14px;
  margin-bottom: 22px;
  line-height: 1.4;
}
.modal-buttons {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
.modal-buttons button {
  flex: 1;
  border: none;
  border-radius: 10px;
  padding: 10px 0;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: 0.25s;
}
.btn-cancel {
  background: #f1f1f1;
  color: #333;
}
.btn-cancel:hover { background: #e4e4e4; }
.btn-logout {
  background: #dc3545;
  color: #fff;
}
.btn-logout:hover { background: #b02a37; }

/* === RESPONSIVE === */
@media (max-width: 480px) {
  .profile-header { width: 94%; padding: 8px 14px; gap: 12px; }
  .profile-photo { width: 50px; height: 50px; border-radius: 10px; }
  .profile-info h5 { font-size: 15px; }
  .profile-info .username { font-size: 12.5px; }
  .menu-section, .logout-section { width: 94%; margin-top: 18px; }
  .menu-item { padding: 12px 14px; }
  .menu-item-left i { width: 32px; height: 32px; font-size: 14px; margin-right: 12px; }
  .menu-text span { font-size: 14px; }
  .menu-text small { font-size: 12px; }
  .fa-chevron-right { font-size: 13px; }
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
@php
  $foto = $user->foto_profil && file_exists(public_path('storage/'.$user->foto_profil))
    ? asset('storage/'.$user->foto_profil)
    : asset('images/guest.png');
@endphp

<div class="profile-header">
  <img src="{{ $foto }}" alt="Foto Profil" class="profile-photo">
  <div class="profile-info">
    <h5>{{ $user->nama_lengkap }}</h5>
    <p class="username">{{ '@' . $user->username }}</p>
  </div>
</div>

{{-- ==== MENU UTAMA ==== --}}
<div class="menu-section">
  <div class="menu-item" onclick="window.location='{{ route('user.profile.edit') }}'">
    <div class="menu-item-left">
      <i class="fas fa-pen"></i>
      <div class="menu-text">
        <span>Edit Profile</span>
        <small>Update data Anda dengan mudah</small>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <div class="menu-item" onclick="window.location='{{ route('user.payments.index', ['from' => 'profile']) }}'">
    <div class="menu-item-left">
      <i class="fas fa-receipt"></i>
      <div class="menu-text">
        <span>Transaksi Saya</span>
        <small>Lihat Riwayat Transaksi</small>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <div class="menu-item" onclick="window.location='{{ route('user.rentals.index', ['from' => 'profile']) }}'">
    <div class="menu-item-left">
      <i class="fas fa-car"></i>
      <div class="menu-text">
        <span>Riwayat Penyewaan</span>
        <small>Mobil yang pernah Anda sewa</small>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <div class="menu-item" onclick="window.location='{{ route('user.kontak.index', ['from' => 'profile']) }}'">
    <div class="menu-item-left">
      <i class="fas fa-envelope"></i>
      <div class="menu-text">
        <span>Hubungi Kami</span>
        <small>Butuh bantuan? Kami siap!</small>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>
</div>

{{-- ==== LOG OUT ==== --}}
<div class="logout-section">
  <div class="menu-item" onclick="openLogoutModal()">
    <div class="menu-item-left">
      <i class="fas fa-sign-out-alt"></i>
      <div class="menu-text">
        <span>Log Out</span>
        <small>Sampai jumpa kembali!</small>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>
</div>

@include('partials.bottom-navbar')
@endsection  {{-- ⛔ PENTING: tutup content dulu sebelum modal --}}

{{-- ==== MODAL KONFIRMASI LOGOUT ==== --}}
<div id="logoutOverlay" class="modal-overlay">
  <div id="logoutBox" class="modal-box">
    <i class="fas fa-question-circle"></i>
    <h4>Apakah Anda yakin ingin keluar?</h4>
    <p>Setelah keluar, Anda harus login kembali untuk mengakses akun ini.</p>
    <div class="modal-buttons">
      <button class="btn-cancel" onclick="closeLogoutModal()">Tidak</button>
      <button class="btn-logout" onclick="submitLogout()">Ya</button>
    </div>
  </div>
</div>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
  @csrf
</form>

<script>
function openLogoutModal() {
  const overlay = document.getElementById('logoutOverlay');
  const box = document.getElementById('logoutBox');
  overlay.classList.add('active');
  setTimeout(() => box.classList.add('active'), 20);
  document.body.style.overflow = 'hidden';
}

function closeLogoutModal() {
  const overlay = document.getElementById('logoutOverlay');
  const box = document.getElementById('logoutBox');
  box.classList.remove('active');
  setTimeout(() => overlay.classList.remove('active'), 200);
  document.body.style.overflow = 'auto';
}

function submitLogout() {
  document.getElementById('logoutForm').submit();
}

// Tutup modal jika klik di luar box
window.onclick = e=>{
  const overlay=document.getElementById('logoutOverlay');
  if(e.target===overlay) closeLogoutModal();
};
</script>
