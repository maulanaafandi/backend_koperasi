<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsPengurus
{
   public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Pengurus) {
            return response()->json([
                'message' => 'Akses ditolak. Fitur ini hanya untuk Pengurus.'
            ], 403);
        }
        return $next($request);
    }
}
