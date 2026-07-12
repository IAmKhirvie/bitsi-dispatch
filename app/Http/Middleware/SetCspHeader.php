<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCspHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (app()->environment('local')) {
            // Still permissive for local dev (if you want)
            $response->headers->set(
                'Content-Security-Policy',
                "script-src 'self' 'unsafe-eval' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 https://unpkg.com;"
            );
        } else {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com; " .
                "style-src 'self' 'unsafe-inline' https://unpkg.com https://fonts.bunny.net; " .
                "img-src 'self' data: https://*.tile.openstreetmap.org http://localhost:8000 http://127.0.0.1:8000; " .
                "font-src 'self' https://fonts.bunny.net; " .
                "connect-src 'self' https://unpkg.com;"
            );
        }

        return $response;
    }
}