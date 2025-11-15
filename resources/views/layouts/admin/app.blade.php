<!DOCTYPE html>
<html lang="id">
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'RentCar Admin')</title>

  {{-- CSS utama --}}
  <link rel="stylesheet" href="{{ asset('css/admin/admin-layout.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/navbar.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/footer.css?v=' . time()) }}">

  {{-- CARS CSS --}}
  <link rel="stylesheet" href="{{ asset('css/admin/cars.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/cars-create.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/cars-edit.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/cars-merek.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/cars-models.css?v=' . time()) }}">

  {{-- RENTALS --}}
  <link rel="stylesheet" href="{{ asset('css/admin/rentals-index.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/rentals-show.css?v=' . time()) }}">

  {{-- USERS --}}
  <link rel="stylesheet" href="{{ asset('css/admin/users-index.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/users-show.css?v=' . time()) }}">

  {{-- LAPORAN --}}
  <link rel="stylesheet" href="{{ asset('css/admin/laporan-index.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/laporan-cetak.css?v=' . time()) }}">

  {{-- INVOICE --}}
  <link rel="stylesheet" href="{{ asset('css/admin/invoice-index.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/invoice-show.css?v=' . time()) }}">
  <link rel="stylesheet" href="{{ asset('css/admin/invoice-create.css?v=' . time()) }}">

  {{--Social Media--}}
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- ⬇⬇⬇ WAJIB ADA DI SINI --}}
  @yield('styles')

  @stack('styles')
</head>


<body>
  <div class="admin-wrapper">
    @include('layouts.admin.partials.sidebar')

    <div class="admin-main">
      @include('layouts.admin.partials.navbar')

      <main class="admin-content">
        @yield('content')
      </main>

      @include('layouts.admin.partials.footer')
    </div>
  </div>

    <script src="{{ asset('js/admin-theme.js?v=' . time()) }}"></script>

  {{-- tambahkan ini sebelum body tutup --}}
  @stack('scripts')
</body>
</html>
