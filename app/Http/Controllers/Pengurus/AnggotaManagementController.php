<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaManagementController extends Controller
{
    public function index()
    {
        $anggota = Anggota::select('id', 'nomor_anggota', 'nama_lengkap', 'status_akun')->get();
        return response()->json([
            'message' => 'Daftar akun anggota berhasil dimuat',
            'data' => $anggota
        ]);
    }

    public function show($id)
    {
        $anggota = Anggota::select('id', 'nomor_anggota', 'nama_lengkap', 'alamat', 'nomor_handphone', 'status_akun')
            ->find($id);

        if (!$anggota) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail akun anggota',
            'data' => $anggota
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Non-Aktif'
        ]);

        $anggota = Anggota::findOrFail($id);
        $anggota->status_akun = $request->status;
        $anggota->save();

        return response()->json([
            'message' => "Status akun berhasil diubah menjadi {$request->status}",
            'data' => $anggota
        ]);
    }

    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);
        if ($anggota->status_akun !== 'Non-Aktif') {
            return response()->json([
                'message' => 'Gagal menghapus. Akun harus dinonaktifkan terlebih dahulu sebelum dihapus.'
            ], 400);
        }

        $anggota->delete();

        return response()->json([
            'message' => 'Akun anggota berhasil dihapus'
        ]);
    }
}
