<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rental Mobil')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <main>
        @yield('content')
    </main>

    {{-- Navbar kecuali di login/register --}}
    @if (!request()->is('login') && !request()->is('register'))
        @include('partials.bottom-navbar')
    @endif
</body>

</html>
