<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengurus;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\CicilanPinjaman;

class DashboardAdminController extends Controller
{
     public function admin()
    {
        return response()->json([
            'total_pengurus_aktif' => Pengurus::where('status_akun', 'Aktif')->count(),

            'total_pengurus_proses' => Pengurus::where('status_akun', 'Proses')->count(),

            'total_nasabah' => Nasabah::count(),

           'total_pinjaman' => Pinjaman::count(),

           'total_pinjaman_macet' => CicilanPinjaman::where('status_angsuran', 'macet')->count(),
        ]);
    }

    public function getNamaAdmin()
{
    $admin = auth()->user();

    if (!$admin) {
        return response()->json([
            'message' => 'Admin tidak ditemukan'
        ], 404);
    }

    return response()->json([
            'nomor_admin' => $admin->nomor_admin
    ], 200);
}
}
