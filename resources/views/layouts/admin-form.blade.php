<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Form')</title>

  {{-- FONT & BASE STYLE --}}
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  {{-- STYLE TAMBAHAN KHUSUS FORM --}}
  <link rel="stylesheet" href="{{ asset('css/cars-form.css') }}">

  {{-- Tambahan custom style dari masing-masing halaman --}}
  @yield('styles')
</head>
<body>
  <div class="admin-form-container">
    <header class="form-header">
      <h1>@yield('page_title', 'Form Admin')</h1>
      <a href="{{ route('cars.index') }}" class="btn-back">← Kembali</a>
    </header>

    <main class="form-content">
      @yield('content')
    </main>
  </div>
</body>
</html>
