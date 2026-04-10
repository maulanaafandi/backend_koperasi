<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAnggota
{

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Anggota) {
            return response()->json([
                'message' => 'Acces denied. Fitur ini hanya untuk Anggota.'
            ], 403);
        }
        return $next($request);
    }
}
