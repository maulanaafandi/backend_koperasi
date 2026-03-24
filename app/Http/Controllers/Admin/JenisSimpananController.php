<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JenisSimpananController extends Controller
{
    public function index()
    {
        $data = JenisSimpanan::all();
        return response()->json([
            'message' => 'Daftar jenis simpanan berhasil dimuat',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_simpanan' => 'required|string|unique:jenis_simpanan,jenis_simpanan'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jenis = JenisSimpanan::create([
            'jenis_simpanan' => $request->jenis_simpanan
        ]);

        return response()->json([
            'message' => 'Jenis simpanan berhasil ditambahkan',
            'data' => $jenis
        ], 201);
    }

    public function show($id)
    {
        $jenis = JenisSimpanan::find($id);
        if (!$jenis) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        return response()->json(['data' => $jenis]);
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisSimpanan::find($id);
        if (!$jenis) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'jenis_simpanan' => 'required|string|unique:jenis_simpanan,jenis_simpanan,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jenis->update(['jenis_simpanan' => $request->jenis_simpanan]);

        return response()->json([
            'message' => 'Jenis simpanan berhasil diperbarui',
            'data' => $jenis
        ]);
    }

    public function destroy($id)
    {
        $jenis = JenisSimpanan::find($id);
        if (!$jenis) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        // Cek apakah jenis simpanan ini sudah digunakan di tabel simpanan
        // Jika sudah digunakan, maka tidak boleh dihapus karena ada restrictOnDelete di migration
        try {
            $jenis->delete();
            return response()->json(['message' => 'Jenis simpanan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus. Data ini sudah digunakan pada transaksi simpanan anggota.'
            ], 400);
        }
    }
}
