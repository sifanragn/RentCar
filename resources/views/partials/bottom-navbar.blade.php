{{-- partial bottom-navbar.blade.php --}}
<style>
.bottom-nav {
  width: 100%;
  height: 60px;
  background: #000;
  border-radius: 20px 20px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 -4px 12px rgba(0,0,0,0.25);
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  z-index: 100;
  max-width: 360px;
  padding: 0 12px;
}

/* nav-item */
.bottom-nav .nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: rgba(255,255,255,0.65);
  text-decoration: none;
  font-size: 10px;
  width: 60px;
  gap: 2px;
  transition: color .18s ease, transform .12s ease;
}

.bottom-nav .nav-item:hover {
  color: #fff;
  transform: translateY(-2px);
}

/* icon svg */
.bottom-nav .nav-item svg {
  width: 22px;
  height: 22px;
  fill: currentColor;
}

/* active state */
.bottom-nav .nav-item.active,
.bottom-nav .nav-item.active svg,
.bottom-nav .nav-item.active span {
  color: #fff;
  fill: #fff;
}

/* center button */
.bottom-nav .center-wrap {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: -28px;
  width: 60px;
}

.bottom-nav .center-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #000; /* bisa ganti ke ITWM gradien */
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 24px rgba(0,0,0,0.35), 0 0 18px rgba(90,86,255,0.14);
  border: 6px solid rgba(255,255,255,0.98); /* border putih tetap ada */
  z-index: 70;
  transition: transform .12s ease;
}

.bottom-nav .center-btn.active img {
  filter: brightness(1.2);
}

.bottom-nav .center-btn img {
  width: 32px;
  height: 32px;
}

.bottom-nav .nav-item span,
.bottom-nav .center-label {
  font-size: 10px;
  text-align: center;
  white-space: nowrap;
  color: #f5f6f7;
}

.bottom-nav .muted { color: rgba(255,255,255,0.55); }

/* responsive */
@media (max-width: 480px) {
  .bottom-nav { padding: 10px 14px; }
  .bottom-nav .nav-item { width: 56px; font-size: 11px; }
  .bottom-nav .center-wrap { margin-top: -30px; width: 100px; }
  .bottom-nav .center-btn { width: 68px; height: 68px; }
  .bottom-nav .center-btn img { width: 30px; height: 30px; }
}
</style>

<nav class="bottom-nav" role="navigation" aria-label="Bottom Navigation">
  {{-- Home --}}
  <a class="nav-item {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
    <svg viewBox="0 0 24 24"><path d="M12 3l9 7v11h-6v-6H9v6H3V10z"/></svg>
    <span>Home</span>
  </a>

  {{-- Transaksi --}}
<a 
  class="nav-item {{ request()->is('transaksi') ? 'active' : '' }}" 
  href="{{ auth()->check() ? url('/user/payments') : route('login') }}"
  @guest onclick="return confirm('Kamu harus login dulu untuk melihat transaksi. Login sekarang?')" @endguest
>
  <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
    <path d="M2 4h20c1.1 0 2 .9 2 2v3H0V6c0-1.1.9-2 2-2zm0 5h24v10c0 1.1-.9 2-2 2H2c-1.1 0-2-.9-2-2V9zm4 2v2h4v-2H6z"/>
  </svg>
  <span>Transaksi</span>
</a>

{{-- Tombol Tengah --}}
<div class="center-wrap">
    <a class="center-btn {{ request()->is('cars*') ? 'active' : '' }}" href="{{ route('user.cars.index') }}">
        <img src="/images/navbar.png" alt="Mobil">
    </a>
    <div class="center-label">Daftar Mobil</div>
</div>

  {{-- Hubungi Kami --}}
  <a class="nav-item {{ request()->is('hubungi-kami') ? 'active' : '' }}" href="{{ route('user.kontak.index') }}">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
      <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
    </svg>
    <span>Hubungi Kami</span>
  </a>

  {{-- Profile --}}
  <a class="nav-item {{ request()->is('profile') ? 'active' : '' }}" href="{{ route('user.profile.index') }}">
    <svg viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-8 2-8 4v2h16v-2c0-2-4-4-8-4z"/></svg>
    <span>Profile</span>
  </a>
</nav>
