<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengurus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PengurusManagementController extends Controller
{
    public function index()
    {
        $pengurus = Pengurus::select('id', 'foto_profil', 'nama_lengkap', 'nomor_pengurus')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'data'    => $pengurus
        ], 200);
    }

    public function show($id)
    {
        $pengurus = Pengurus::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $pengurus
        ], 200);
    }

    public function store(Request $request)
    {
        $tahun = date('Y');
        $lastPengurus = Pengurus::whereYear('created_at', $tahun)->latest()->first();
        
        if (!$lastPengurus) {
            $nomorUrut = '0001';
        } else {
            $lastNumber = substr($lastPengurus->nomor_pengurus, -4);
            $nomorUrut = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }
        
        $nomorPengurusOtomatis = "PGR-" . $tahun . $nomorUrut;

        $pengurus = Pengurus::create([
            'nama_lengkap'    => $request->nama_lengkap,
            'foto_profil'     => $request->foto_profil ?? null,
            'nomor_pengurus'  => $nomorPengurusOtomatis,
            'jenis_kelamin'   => $request->jenis_kelamin,
            'nomor_handphone' => $request->nomor_handphone,
//            'password'        => Hash::make($request->password),
            'status_akun'     => 'Proses', 
        ]);

        return response()->json([
            'message' => "Nomor Pengurus dengan nomor {$nomorPengurusOtomatis} berhasil dibuat",
        ], 201);
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status_akun' => ['required', Rule::in(['Aktif', 'Non-Aktif', 'Proses'])
    ],
    ]);
    $pengurus = Pengurus::find($id);
    if (!$pengurus) {
        return response()->json([
            'message' => 'Nomor pengurus tidak ditemukan'
        ], 404);
    }
    $pengurus->update([
        'status_akun' => $request->status_akun
    ]);

    return response()->json([
        'message' => 'Status akun pengurus berhasil diperbarui',
        'data' => [
            'nama_lengkap' => $pengurus->nama_lengkap,
            'status_akun'  => $pengurus->status_akun
        ]
    ]);
}

public function resetPassword($id)
{

    $pengurus = Pengurus::find($id);

    if (!$pengurus) {
        return response()->json([
            'message' => 'Nomor tidak ditemukan'
        ], 404);
    }

    if ($pengurus->status_akun !== 'Aktif') {
        return response()->json([
            'message' => 'Gagal reset. Password hanya bisa direset untuk akun dengan status Aktif.'
        ], 422); 
    }

    $pengurus->update([
        'status_akun' => 'Proses',
        'password'    => null,
    ]);

    return response()->json([
        'message' => 'Berhasil reset password',
        'data' => [
            'nama_lengkap' => $pengurus->nama_lengkap,
            'status_akun'  => $pengurus->status_akun,
            'password'     => 'NULL'
        ]
    ], 200);
}
}


