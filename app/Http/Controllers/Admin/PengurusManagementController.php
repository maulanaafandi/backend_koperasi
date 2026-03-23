<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengurus;
use Illuminate\Http\Request;

class PengurusManagementController extends Controller
{

    public function index()
    {
        $pengurus = Pengurus::select('id', 'nomor_pengurus', 'nama_lengkap', 'status_akun')->get();
        return response()->json([
            'message' => 'Daftar akun pengurus berhasil dimuat',
            'data' => $pengurus
        ]);
    }

    public function show($id)
    {
        $pengurus = Pengurus::select('id', 'nomor_pengurus', 'nama_lengkap', 'nomor_handphone', 'status_akun')
            ->find($id);

        if (!$pengurus) {
            return response()->json(['message' => 'Data pengurus tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail akun pengurus',
            'data' => $pengurus
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Non-Aktif'
        ]);

        $pengurus = Pengurus::findOrFail($id);
        $pengurus->status_akun = $request->status;
        $pengurus->save();

        return response()->json([
            'message' => "Status pengurus berhasil diubah menjadi {$request->status}",
            'data' => $pengurus
        ]);
    }

    public function destroy($id)
    {
        $pengurus = Pengurus::findOrFail($id);

        if ($pengurus->status_akun !== 'Non-Aktif') {
            return response()->json([
                'message' => 'Gagal menghapus. Akun pengurus harus dinonaktifkan terlebih dahulu.'
            ], 400);
        }

        $pengurus->delete();

        return response()->json([
            'message' => 'Akun pengurus berhasil dihapus'
        ]);
    }
}
