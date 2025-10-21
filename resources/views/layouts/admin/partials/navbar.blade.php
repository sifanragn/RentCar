<div class="cars-navbar">
  <h1 class="navbar-title">RentCar Admin</h1>

  <div class="navbar-right">
    <div class="toggle-wrapper">
      <label class="switch">
        <input type="checkbox" id="modeToggle">
        <span class="slider"></span>
      </label>
      <span class="mode-text">Light Mode</span>
    </div>

    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn-logout">Logout</button>
    </form>
  </div>
</div>
