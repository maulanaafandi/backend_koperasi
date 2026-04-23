<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nasabah;

class DashboardNasabahController extends Controller
{
    public function dashboardNasabah(Request $request)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Dashboard nasabah',
            'data' => [
                'nama_nasabah' => $nasabah->nama_lengkap,
                'total_saldo' => $nasabah->saldo
            ]
        ], 200);
    }
}
