<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS Bundle (sudah termasuk Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <title>@yield('title')</title>

  {{-- Tempat untuk CSS tambahan dari setiap halaman --}}
  @yield('styles')

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      overflow-y: auto;
    }

    .container {
      width: 100%;
      max-width: 360px;
      background: #fff;
      padding: 2rem 1.5rem;
      box-shadow: 0 4px 14px rgba(0,0,0,0.3);
      overflow: hidden;
      min-height: 100vh; /* ✅ Tambahan penting */
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    @media (max-width: 480px) {
      body {
        padding: 1.5rem 0;
      }

      .container {
        max-width: 340px;
        padding: 1.5rem 1.2rem;
        border-radius: 16px;
      }
    }
  </style>
</head>
<body>
<div class="container mx-auto p-4">
    {{-- konten panjang --}}
    @yield('content')
</div>
</body>
</html>
