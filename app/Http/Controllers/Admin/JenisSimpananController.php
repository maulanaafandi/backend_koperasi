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
        $data = JenisSimpanan::all();
        return response()->json([
            'data' => $data
        ], 200);
    }

 public function store(Request $request)
{

    if ($request->has('jenis_simpanan')) {
        $request->merge([
            'jenis_simpanan' => ucwords(strtolower($request->jenis_simpanan)),
        ]);
    }

    $validator = Validator::make($request->all(), [
        'jenis_simpanan' => 'required|string|unique:jenis_simpanan,jenis_simpanan',
        'saldo_minimum'  => 'required_unless:jenis_simpanan,Sukarela|nullable|numeric|min:0', 
    ], [
        'jenis_simpanan.unique' => 'Jenis simpanan ini sudah terdaftar.',
        'saldo_minimum.required_unless' => 'Saldo minimum wajib diisi untuk jenis simpanan ini.',
        'saldo_minimum.numeric' => 'Saldo minimum harus berupa angka.',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $admin = auth()->user()->nama_lengkap ?? 'Admin';
    $saldo = $request->saldo_minimum;
    if ($request->jenis_simpanan === 'Sukarela'  && $saldo === null) {
        $saldo = 0;
    }

    $jenis = JenisSimpanan::create([
        'jenis_simpanan' => $request->jenis_simpanan,
        'saldo_minimum'  => $saldo, 
    ]);

    return response()->json([
        'message' => 'Jenis simpanan berhasil ditambahkan',
        'data'    => $jenis
    ], 201);
}

    public function show($id)
    {
        $jenis = JenisSimpanan::find($id);
        if (!$jenis) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['data' => $jenis], 200);
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisSimpanan::find($id);
        if (!$jenis) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis_simpanan' => 'required|string|unique:jenis_simpanan,jenis_simpanan,' . $id,
            'saldo_minimum'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $admin = auth()->user()->nama_lengkap ?? 'Admin';

        $jenis->update([
            'jenis_simpanan' => $request->jenis_simpanan,
            'saldo_minimum'  => $request->saldo_minimum,
            'updated_at'      => Carbon::now(), 
            'updated_by'      => $admin,
        ]);

        return response()->json([
            'message' => 'Jenis simpanan berhasil diupdate',
            'data'    => $jenis
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
