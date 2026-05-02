<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; 
use Carbon\Carbon;

class NasabahManagementController extends Controller
{
public function index()
{
    $nasabah = Nasabah::orderBy('waktu_dibuat', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_lengkap' => $item->nama_lengkap,
                'nomor_rekening' => $item->nomor_rekening,
                'diaktifkan_oleh' => $item->diaktifkan_oleh,
            ];
        });

    return response()->json([
        'data' => $nasabah
    ], 200);
}

public function show($id)
{
    $nasabah = Nasabah::find($id);

    if (!$nasabah) {
        return response()->json([
            'message' => 'Data nasabah tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'data' => [
            'nama_lengkap' => $nasabah->nama_lengkap,
            'foto_profil' => $nasabah->foto_profil,
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
            'status' => $nasabah->status,
            'jenis_pinjaman' => optional($nasabah->jenisSimpanan)->nama_simpanan,
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
    ], 200);
}

public function getStatus($id)
{
    $nasabah = Nasabah::find($id);

    if (!$nasabah) {
        return response()->json([
            'message' => 'Data nasabah tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'status' => $nasabah->status
    ], 200);
}


 public function store(Request $request)
{
    $request->validate([
        'id_jenis_simpanan' => 'required|exists:jenis_simpanan,id',
        'nomor_induk_kependudukan' => 'required|digits:16|unique:nasabah,nomor_induk_kependudukan',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'nullable|email|unique:nasabah,email',
        'tanggal_lahir' => 'required|date',
        'tempat_lahir' => 'required|string',
        'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
        'status_perkawinan' => ['required', Rule::in(['Belum Kawin', 'Kawin', 'Cerai'])],
        'nomor_handphone' => 'nullable|string|max:15|unique:nasabah,nomor_handphone',
        'alamat_ktp' => 'required|string',
        'RT' => 'required|string|max:3',
        'RW' => 'required|string|max:3',
        'jenis_pekerjaan' => 'required|string|max:100',
        'gaji_pekerjaan' => 'required|numeric|min:0',
        'nama_ibu_kandung' => 'required|string',
        'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'tipe' => ['required', Rule::in(['Non-Anggota', 'Anggota'])],
    ]);

    $tahun = date('Y');
    $last = Nasabah::whereYear('waktu_dibuat', $tahun)->latest('id')->first();
    $urut = $last ? str_pad((int)substr($last->nomor_nasabah, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
    $nomorNasabah = "NSB-$tahun$urut";

    $nomorRekening = 'REK-' . date('Ymd') . strtoupper(Str::random(6));

    $pengurusLogin = optional(auth()->user())->nomor_pengurus ?? 'PGR000';

    $pathFoto = $request->file('foto_profil')
        ->store('nasabah/foto_profil', 'public');


    $nasabah = new Nasabah();
    $nasabah->nomor_nasabah = $nomorNasabah;
    $nasabah->nomor_rekening = $nomorRekening;
    $nasabah->id_jenis_simpanan = $request->id_jenis_simpanan;
    $nasabah->nomor_induk_kependudukan = $request->nomor_induk_kependudukan;
    $nasabah->nama_lengkap = $request->nama_lengkap;
    $nasabah->email = $request->email;
    $nasabah->password = null;
    $nasabah->tanggal_lahir = $request->tanggal_lahir;
    $nasabah->tempat_lahir = $request->tempat_lahir;
    $nasabah->jenis_kelamin = $request->jenis_kelamin;
    $nasabah->status_perkawinan = $request->status_perkawinan;
    $nasabah->nomor_handphone = $request->nomor_handphone;
    $nasabah->alamat_ktp = $request->alamat_ktp;
    $nasabah->RT = $request->RT;
    $nasabah->RW = $request->RW;
    $nasabah->jenis_pekerjaan = $request->jenis_pekerjaan;
    $nasabah->gaji_pekerjaan = $request->gaji_pekerjaan;
    $nasabah->nama_ibu_kandung = $request->nama_ibu_kandung;
    $nasabah->foto_profil = $pathFoto;
    $nasabah->tipe = $request->tipe;

    $nasabah->status = 'Aktif';
    $nasabah->saldo = 0;
    $nasabah->pin = null;
    $nasabah->dibuat_oleh = $pengurusLogin;
    $nasabah->waktu_diaktifkan = now();
    $nasabah->diaktifkan_oleh = $pengurusLogin;

    $nasabah->save();

    return response()->json([
        'message' => 'Akun nasabah berhasil dibuat',
    ], 201);
}

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => ['required', Rule::in(['Aktif', 'Non-Aktif'])],
    ]);

    $nasabah = Nasabah::find($id);

    if (!$nasabah) {
        return response()->json([
            'message' => 'Data nasabah tidak ditemukan'
        ], 404);
    }

    $pengurusLogin = optional(auth()->user())->nomor_pengurus ?? 'PGR000';

    if ($request->status === 'Aktif') {
        if (!$nasabah->nomor_induk_kependudukan) {
            return response()->json([
                'message' => 'Gagal. Nasabah belum melengkapi profil.'
            ], 422);
        }

        $nasabah->status = 'Aktif';
        $nasabah->waktu_diaktifkan = now();
        $nasabah->diaktifkan_oleh = $pengurusLogin;

        $nasabah->waktu_dinonaktifkan = null;
        $nasabah->dinonaktifkan_oleh = null;

    } else {

        $nasabah->status = 'Non-Aktif';
        $nasabah->waktu_dinonaktifkan = now();
        $nasabah->dinonaktifkan_oleh = $pengurusLogin;
    }

    $nasabah->save();

    return response()->json([
        'message' => 'Status nasabah berhasil diperbarui',
        'data' => [
            'status' => $nasabah->status
        ]
    ], 200);
}

public function resetPassword($id)
{
    $nasabah = Nasabah::find($id);

    if (!$nasabah) {
        return response()->json([
            'message' => 'Data nasabah tidak ditemukan.'
        ], 404);
    }

    if ($nasabah->status !== 'Aktif') {
        return response()->json([
            'message' => 'Gagal reset. Hanya akun Aktif yang dapat direset.'
        ], 422);
    }

    $nasabah->password = null;

    $nasabah->save();

    return response()->json([
        'message' => "Password nasabah {$nasabah->nama_lengkap} berhasil direset."
    ], 200);
}

    public function getJenisSimpanan()
    {
        $jenisSimpanan = JenisSimpanan::all();

        return response()->json([
            'data' => $jenisSimpanan->map(function ($item) {
                return [
                    'id_jenis_simpanan' => $item->id,
                    'nama_simpanan' => $item->nama_simpanan,
                    'saldo_minimal' => $item->saldo_minimal,
                ];
            })
        ], 200);
    }
}