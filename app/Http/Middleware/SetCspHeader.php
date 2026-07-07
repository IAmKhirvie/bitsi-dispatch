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
            $response->headers->set(
                'Content-Security-Policy',
                "script-src 'self' 'unsafe-eval' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 https://unpkg.com;"
            );
        }

        return $response;
    }
}