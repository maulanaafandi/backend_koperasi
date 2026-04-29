<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LamaAngsuranController extends Controller
{

public function index()
    {
        $data = Tenor::select('id', 'tipe', 'lama_angsuran', 'bunga', 'bunga_keterlambatan')->get();
        return response()->json([
            'data' => $data
        ], 200);
    }

public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe'                => 'required|in:Anggota,Non-Anggota',
            'lama_angsuran'       => 'required|integer|min:1|max:24',
            'bunga'               => 'required|numeric|min:0',
            'bunga_keterlambatan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $isExist = Tenor::where('tipe', $request->tipe)
                        ->where('lama_angsuran', $request->lama_angsuran)
                        ->exists();

        if ($isExist) {
            return response()->json([
                'message' => "Data lama angsuran {$request->lama_angsuran} bulan untuk {$request->tipe} sudah ada."
            ], 422);
        }

        $nomorAdmin = auth()->user()->nomor_admin ?? 'ADM000';
        $tenor = Tenor::create([
            'tipe'                => $request->tipe,
            'lama_angsuran'       => $request->lama_angsuran_label,
            'bunga'               => $request->bunga,
            'bunga_keterlambatan' => $request->bunga_keterlambatan,
            'dibuat_oleh'         => $nomorAdmin,
            'waktu_diubah'        => null,
            'diubah_oleh'         => null,
        ]);

        return response()->json([
            'message' => 'Data lama angsuran berhasil ditambahkan',
        ], 201);
    }

 public function show($id)
    {
        $tenor = Tenor::find($id);

        if (!$tenor) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'id'                  => $tenor->id,
            'tipe'                => $tenor->tipe,
            'lama_angsuran'       => $tenor->lama_angsuran_label,
            'bunga'               => $tenor->bunga,
            'bunga_keterlambatan' => $tenor->bunga_keterlambatan,
            'waktu_dibuat'        => $tenor->waktu_dibuat ? $tenor->waktu_dibuat->format('Y-m-d H:i:s') : null,
            'dibuat_oleh'         => $tenor->dibuat_oleh,
            'waktu_diubah'        => $tenor->waktu_diubah ? $tenor->waktu_diubah->format('Y-m-d H:i:s') : null,
            'diubah_oleh'         => $tenor->diubah_oleh,
        ], 200);
    }

        public function getById($id)
    {
        $tenor = Tenor::select('id', 'tipe', 'lama_angsuran', 'bunga', 'bunga_keterlambatan')
                    ->find($id);

        if (!$tenor) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'id'                  => $tenor->id,
            'tipe'                => $tenor->tipe,
            'lama_angsuran'       => $tenor->lama_angsuran_label,
            'bunga'               => $tenor->bunga,
            'bunga_keterlambatan' => $tenor->bunga_keterlambatan,
        ], 200);
    }

public function update(Request $request, $id)
    {
        $tenor = Tenor::find($id);

        if (!$tenor) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipe'                => 'required|in:Anggota,Non-Anggota',
            'lama_angsuran'       => 'required|integer|min:1|max:60',
            'bunga'               => 'required|numeric|min:0',
            'bunga_keterlambatan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $isExist = Tenor::where('tipe', $request->tipe)
                        ->where('lama_angsuran', $request->lama_angsuran)
                        ->where('id', '!=', $id)
                        ->exists();

        if ($isExist) {
            return response()->json([
                'message' => "Gagal update! Data lama angsuran {$request->lama_angsuran} bulan untuk {$request->tipe} sudah ada."
            ], 422);
        }

        $nomorAdmin = auth()->user()->nomor_admin ?? 'ADM000';

        $tenor->update([
            'tipe'                => $request->tipe,
            'lama_angsuran'       => $request->lama_angsuran,
            'bunga'               => $request->bunga,
            'bunga_keterlambatan' => $request->bunga_keterlambatan,
            'diubah_oleh'         => $nomorAdmin,
        ]);

        return response()->json([
            'message' => "Data lama angsuran berhasil diperbarui.",
        ], 200);
    }

    public function destroy($id)
    {
        $tenor = Tenor::find($id);

        if (!$tenor) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        try {
            $tenor->delete();
            return response()->json(['message' => 'Data lama angsuran berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus. Data ini kemungkinan sedang digunakan oleh data lain.'
            ], 400);
        }
    }
}