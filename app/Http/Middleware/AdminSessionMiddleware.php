<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionMiddleware
{
    public function handle($request, Closure $next): Response
    {
        if (!session()->has('admin_logged_in')) {
            return redirect('/login')->with('error', 'Silakan login sebagai admin.');
        }

        return $next($request);
    }
}
