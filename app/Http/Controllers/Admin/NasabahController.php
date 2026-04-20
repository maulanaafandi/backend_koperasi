<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nasabah;

class NasabahController extends Controller
{
public function indexNasabah()
{
    $nasabah = Nasabah::latest('waktu_dibuat')->get();

    if ($nasabah->isEmpty()) {
        return response()->json([
            'message' => 'Data nasabah belum ada',
            'data' => []
        ], 404);
    }

    return response()->json([
        'message' => 'List nasabah',
        'data' => $nasabah->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_lengkap' => $item->nama_lengkap,
                'nomor_nasabah' => $item->nomor_nasabah,
                'diaktifkan_oleh' => $item->diaktifkan_oleh,
            ];
        })
    ], 200);
}

public function detailNasabah($id)
{
    $nasabah = Nasabah::with('jenisSimpanan')->find($id);

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
            'status_akun' => $nasabah->status,
            'jenis_simpanan' => optional($nasabah->jenisSimpanan)->nama_simpanan,
            'nomor_nasabah' => $nasabah->nomor_nasabah,
            'nomor_handphone' => $nasabah->nomor_handphone,
            'email' => $nasabah->email,
            'waktu_dibuat' => $nasabah->waktu_dibuat,
            'dibuat_oleh' => $nasabah->dibuat_oleh,
            'waktu_diaktifkan' => $nasabah->waktu_diaktifkan,
            'diaktifkan_oleh' => $nasabah->diaktifkan_oleh,
            'waktu_dinonaktifkan' => $nasabah->waktu_dinonaktifkan,
            'dinonaktifkan_oleh' => $nasabah->dinonaktifkan_oleh,
            'nomor_rekening' => $nasabah->nomor_rekening,
            'tipe' => $nasabah->tipe,
        ]
    ]);
}
}
