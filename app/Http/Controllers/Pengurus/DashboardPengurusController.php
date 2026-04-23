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

            'total_pinjaman' => Pinjaman::count(),

            'total_pinjaman_lunas' => Pinjaman::where('status_angsuran', 'lunas')->count(),

            'total_pinjaman_jatuh_tempo' => Pinjaman::where('status_angsuran', 'jatuh_tempo')->count(),

            'total_pinjaman_macet' => Pinjaman::where('status_angsuran', 'macet')->count(),
        ]);
    }
}
