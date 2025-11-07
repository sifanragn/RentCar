{{-- partial bottom-navbar.blade.php --}}
<style>
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;

  width: 100% !important; /* ✅ ikut layar, bukan 100vw */
  height: 70px;
  background: #000;
  border-radius: 26px 26px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 -10px 25px rgba(0,0,0,0.25);
  padding: 0 16px;
  padding-bottom: calc(env(safe-area-inset-bottom) + 6px);
  z-index: 100;
  
  transform: none !important;
  max-width: 100% !important; /* ✅ anti kotak */
  inset-inline: 0; /* ✅ pastikan nempel kiri kanan */

  backdrop-filter: blur(18px);
}

/* nav item */
.bottom-nav .nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: rgba(255,255,255,0.65);
  text-decoration: none;
  font-size: 11px;
  width: 60px;
  gap: 2px;
  transition: color .18s ease, transform .12s ease;
}

.bottom-nav .nav-item:hover {
  color: #fff;
  transform: translateY(-2px);
}

/* active */
.bottom-nav .nav-item.active,
.bottom-nav .nav-item.active svg,
.bottom-nav .nav-item.active span {
  color: #fff;
  fill: #fff;
}

/* icon */
.bottom-nav .nav-item svg {
  width: 22px;
  height: 22px;
  fill: currentColor;
}

/* center button */
.bottom-nav .center-wrap {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 60px;
  margin-top: -37px; /* ✅ tambah dikit supaya lingkaran teu kegunting */
  z-index: 200;
}

.bottom-nav .center-btn {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 24px rgba(0,0,0,0.35), 0 0 18px rgba(90,86,255,0.14);
  border: 6px solid rgba(255,255,255,0.98);
  z-index: 201;
  transition: transform .12s ease;
}

.bottom-nav .center-btn img {
  width: 32px;
  height: 32px;
}

/* label */
.bottom-nav .nav-item span,
.bottom-nav .center-label {
  font-size: 10px;
  text-align: center;
  white-space: nowrap;
  color: #f5f6f7;
}

/* ✅ Desktop: FIXED bawah, sama persis mobile, tapi ikut lebar app-container */
@media (min-width: 769px) {
  
  body {
    background: #f2f2f2;
    display: flex;
    justify-content: center;
  }

  .app-container {
    max-width: 420px;
    width: 100%;
    margin: 0 auto;
    padding-bottom: 90px !important; /* biar konten ga ketutup */
    background: #fff;
  }

  .bottom-nav {
    position: fixed !important;
    bottom: 0 !important;
    left: 50% !important;
    transform: translateX(-50%) !important;

    width: 100% !important;
    max-width: 420px !important;  /* ✅ ikut container */

    height: 70px !important;
    border-radius: 26px 26px 0 0 !important;
    background: #000 !important;
    box-shadow: 0 -10px 25px rgba(0,0,0,0.25);
    padding: 0 16px !important;
  }

  .bottom-nav .center-wrap {
    margin-top: -37px !important;
  }

  .bottom-nav .center-btn {
    width: 68px !important;
    height: 68px !important;
    border: 6px solid rgba(255,255,255,0.98) !important;
    background: #000;
  }

  .bottom-nav .center-btn img {
    width: 32px !important;
    height: 32px !important;
  }
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
  class="nav-item {{ request()->is('user/payments*') ? 'active' : '' }}" 
  href="{{ auth()->check() ? route('user.payments.index') : route('user.payments.guest') }}"
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

<a 
  class="nav-item {{ request()->is('user/profile*') ? 'active' : '' }}" 
  href="{{ auth()->check() ? route('user.profile.index') : route('user.profile.guest') }}"
>
  <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
    <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-8 2-8 4v2h16v-2c0-2-4-4-8-4z"/>
  </svg>
  <span>Profile</span>
</a>
</nav>
