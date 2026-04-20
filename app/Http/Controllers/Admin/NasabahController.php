<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nasabah;

class NasabahController extends Controller
{
public function indexNasabah()
{
    $nasabah = Nasabah::select(
        'id',
        'nama_lengkap',
        'nomor_nasabah',
        'diaktifkan_oleh'
    )->latest('waktu_dibuat')->get();

    if ($nasabah->isEmpty()) {
        return response()->json([
            'message' => 'Data nasabah belum ada',
            'data' => []
        ], 404);
    }

    return response()->json([
        'data' => $nasabah
    ]);
}

public function detailNasabah($id)
{
    $nasabah = Nasabah::find($id);

    if (!$nasabah) {
        return response()->json([
            'message' => 'Nasabah tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail nasabah',
        'data' => [
            'nama_lengkap' => $nasabah->nama_lengkap,
            'foto_profil' => $nasabah->foto_profil 
                ? asset('storage/' . $nasabah->foto_profil) 
                : null,
        'nomor_induk_kependudukan' => $nasabah->nomor_induk_kependudukan,
        'nama_ibu_kandung' => $nasabah->nama_ibu_kandung,
        'tanggal_lahir' => $nasabah->tanggal_lahir,
        'tempat_lahir' => $nasabah->tempat_lahir,
        'status_perkawinan' => $nasabah->status_perkawinan,
        'jenis_kelamin' => $nasabah->jenis_kelamin,
        'alamat_ktp' => $nasabah->alamat_ktp,
        'rt' => $nasabah->RT,
        'rw' => $nasabah->RW,
        'jenis_pekerjaan' => $nasabah->jenis_pekerjaan,
        'gaji_pekerjaan' => $nasabah->gaji_pekerjaan,
        'nomor_handphone' => $nasabah->nomor_handphone,
        'email' => $nasabah->email,
        ]
    ]);
}
}
