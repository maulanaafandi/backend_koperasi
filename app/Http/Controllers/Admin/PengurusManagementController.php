<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PengurusManagementController extends Controller
{
public function index()
    {
        $pengurus = Pengurus::select('id', 'foto_profil', 'nama_lengkap', 'nomor_pengurus')
                            ->orderBy('waktu_dibuat', 'desc') 
                            ->get();

        return response()->json([
            'data' => $pengurus
        ], 200);
    }

    public function show($id)
    {
        $pengurus = Pengurus::findOrFail($id);

        return response()->json([
            'data'    => $pengurus
        ], 200);
    }

public function store(Request $request)
    {
        $tahun = date('Y');
        $lastPengurus = Pengurus::whereYear('waktu_dibuat', $tahun)->latest('id')->first();
        
        if (!$lastPengurus) {
            $nomorUrut = '0001';
        } else {
            $lastNumber = substr($lastPengurus->nomor_pengurus, -4);
            $nomorUrut = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }
        
        $nomorPengurusOtomatis = "PGR-" . $tahun . $nomorUrut;

        $pengurus = new Pengurus();
        $pengurus->nomor_pengurus = $nomorPengurusOtomatis;
        $pengurus->status_akun    = 'Proses';
        $pengurus->save();

        return response()->json([
            'message' => "Nomor Pengurus {$nomorPengurusOtomatis} berhasil dibuat.",
        ], 201);
    }

public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_akun' => ['required', Rule::in(['Aktif', 'Non-Aktif', 'Proses'])],
        ]);

        $pengurus = Pengurus::findOrFail($id);
        $adminLogin = auth()->user()->nomor_admin ?? 'Admin';

        if ($request->status_akun === 'Aktif') {
            $pengurus->status_akun      = 'Aktif';
            $pengurus->waktu_diaktifkan = Carbon::now();
            $pengurus->diaktifkan_oleh  = $adminLogin;
            
            $pengurus->waktu_dinonaktifkan = null;
            $pengurus->dinonaktifkan_oleh  = null;

        } elseif ($request->status_akun === 'Non-Aktif') {
            $pengurus->status_akun         = 'Non-Aktif';
            $pengurus->waktu_dinonaktifkan = Carbon::now();
            $pengurus->dinonaktifkan_oleh  = $adminLogin;

        } else {
            $pengurus->status_akun         = 'Proses';
            $pengurus->waktu_diaktifkan    = null;
            $pengurus->diaktifkan_oleh     = null;
            $pengurus->waktu_dinonaktifkan = null;
            $pengurus->dinonaktifkan_oleh  = null;
        }

        $pengurus->save();

        return response()->json([
            'message' => 'Status berhasil diperbarui.',
        ]);
    }
}


