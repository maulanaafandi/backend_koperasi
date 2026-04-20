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
        $user = $request->user();
        if (!$request->user() instanceof Pengurus || $request->user()->status_akun !== 'Aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Fitur ini hanya untuk Pengurus dengan akun aktif.'
            ], 403);
        }

       if (is_null($user->password)) {
            return response()->json([
                'message' => 'Password Anda telah direset oleh Admin. Silakan lakukan daftar ulang untuk membuat password baru.'
            ], 403);
        }

        return $next($request);
    }
}
