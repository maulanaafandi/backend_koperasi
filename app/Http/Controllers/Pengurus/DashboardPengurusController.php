<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\CicilanPinjaman;

class DashboardPengurusController extends Controller
{
      public function index()
    {
        return response()->json([
            'total_akun_nasabah' => Nasabah::count(),

            'total_pinjaman' => CicilanPinjaman::count(),

            'total_pinjaman_lunas' => CicilanPinjaman::where('status_angsuran', 'lunas')->count(),

            'total_pinjaman_jatuh_tempo' => CicilanPinjaman::where('status_angsuran', 'jatuh_tempo')->count(),

            'total_pinjaman_macet' => CicilanPinjaman::where('status_angsuran', 'macet')->count(),
        ]);
    }

    public function getNamaPengurus()
    {
        $pengurus = auth()->user();

        if (!$pengurus) {
            return response()->json([
                'message' => 'Pengurus tidak ditemukan'
            ], 404);
        }

        return response()->json([
                'nama_pengurus' => $pengurus->nama_lengkap
        ], 200);
    }
}
