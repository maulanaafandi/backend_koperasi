<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Nasabah;

class CheckIsNasabah
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() instanceof Nasabah || $request->user()->status !== 'Aktif') {
            return response()->json([
                'message' => 'Access denied. Fitur ini hanya untuk Nasabah dengan akun aktif.'
            ], 403);
        }

        return $next($request);
    }
}