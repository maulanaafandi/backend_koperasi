<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
         if (! $request->user() instanceof Admin) {
            return response()->json([
                'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.'
            ], 403);
        }
        return $next($request);
    }
}
