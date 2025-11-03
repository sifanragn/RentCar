<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>

  <title>@yield('title')</title>
  @yield('styles')

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f2f2f2;
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      justify-content: center;
    }

    /* ✅ container utama halaman */
    .app-container {
      width: 100%;
      min-height: 100vh;
      padding: 16px;
      padding-bottom: 70px; /* space untuk bottom nav */
    }

    /* ✅ tampilan saat buka di laptop */
    @media (min-width: 480px) {
      .app-container {
        max-width: 420px;
        background: #fff;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
      }
    }
  </style>
</head>

<body>

<div class="app-container">
    @yield('content')
</div>

<!-- ✅ Bootstrap JS di akhir body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
