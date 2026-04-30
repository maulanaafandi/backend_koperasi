<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrigin
{

    public function handle($request, Closure $next)
    {
        $allowedOrigin = 'http://192.168.1.7:8000';

        if ($request->headers->get('origin') !== $allowedOrigin) {
            return response()->json([
                'message' => 'Access denied.'
            ], 403);
        }

        return $next($request);
    }
}
