<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JenisSimpananController extends Controller
{
    public function index()
{
    $jenisSimpanan = JenisSimpanan::all();
    $data = $jenisSimpanan->map(function ($item) {
        return [
            'nama_simpanan' => $item->nama_simpanan,
            'saldo_minimal' => $item->saldo_minimal,
        ];
    });

    return response()->json([
        'data' => $data
    ], 200);
}

public function store(Request $request)
{
    if ($request->has('nama_simpanan')) {
        $request->merge([
            'nama_simpanan' => ucwords(strtolower($request->nama_simpanan)),
        ]);
    }

    $validator = Validator::make($request->all(), [
        'nama_simpanan' => 'required|string|unique:jenis_simpanan,nama_simpanan',
        'saldo_minimal' => 'required_unless:nama_simpanan,Sukarela|nullable|numeric|min:0', 
    ], [
        'nama_simpanan.unique' => 'Nama simpanan ini sudah terdaftar.',
        'saldo_minimal.required_unless' => 'Saldo minimal wajib diisi.',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $nomorAdmin = auth()->user()->nomor_admin ?? 'ADM000';
    $saldo = $request->saldo_minimal;

    if ($request->nama_simpanan === 'Sukarela' && $saldo === null) {
        $saldo = 0;
    }

    JenisSimpanan::create([
        'nama_simpanan' => $request->nama_simpanan,
        'saldo_minimal' => $saldo,
        'dibuat_oleh'   => $nomorAdmin,
        'diubah_oleh'   => null,
        'waktu_diubah'  => null,
    ]);

    return response()->json([
        'message' => 'Jenis simpanan berhasil ditambahkan',
    ], 201);
}

public function show($id)
{
    $jenis = JenisSimpanan::find($id);

    if (!$jenis) {
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'nama_simpanan' => $jenis->nama_simpanan,
        'saldo_minimal' => $jenis->saldo_minimal,
        'waktu_dibuat'  => $jenis->waktu_dibuat ? $jenis->waktu_dibuat->format('Y-m-d H:i:s') : null,
        'dibuat_oleh'   => $jenis->dibuat_oleh,
        'waktu_diubah'  => $jenis->waktu_diubah ? $jenis->waktu_diubah->format('Y-m-d H:i:s') : null,
        'diubah_oleh'   => $jenis->diubah_oleh,
    ], 200);
}

public function getById($id)
{
    $jenis = JenisSimpanan::find($id);

    if (!$jenis) {
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'nama_simpanan' => $jenis->nama_simpanan,
        'saldo_minimal' => $jenis->saldo_minimal,
    ], 200);
}


public function update(Request $request, $id)
{
    $jenis = JenisSimpanan::find($id);

    if (!$jenis) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    if ($request->has('nama_simpanan')) {
        $request->merge([
            'nama_simpanan' => ucwords(strtolower($request->nama_simpanan)),
        ]);
    }

    $validator = Validator::make($request->all(), [
        'nama_simpanan' => 'required|string|unique:jenis_simpanan,nama_simpanan,' . $id,
        'saldo_minimal' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $nomorAdmin = auth()->user()->nomor_admin ?? 'ADM000';

    $jenis->update([
        'nama_simpanan' => $request->nama_simpanan,
        'saldo_minimal' => $request->saldo_minimal,
        'diubah_oleh'   => $nomorAdmin,
    ]);

    return response()->json([
        'message' => 'Jenis simpanan berhasil diperbarui',
    ], 200);
}

    public function destroy($id)
    {
        $jenis = JenisSimpanan::find($id);
        if (!$jenis) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        try {
            $jenis->delete();
            return response()->json(['message' => 'Jenis simpanan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus. Data ini sudah digunakan pada transaksi simpanan anggota.'
            ], 400);
        }
    }
}
