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
        $data = Tenor::all();
        return response()->json([
            'success' => true,
            'data'    => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenor' => 'required|integer|min:1|max:24',
            'tipe'  => 'required|in:Anggota,Non-Anggota',
        ], [
            'tenor.min' => 'Lama angsuran minimal adalah 1 bulan.',
            'tenor.max' => 'Lama angsuran maksimal adalah 24 bulan.',
            'tipe.in'   => 'Pilihan hanya Anggota atau Non-Anggota.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $isExist = Tenor::where('tenor', $request->tenor)
                    ->where('tipe', $request->tipe)
                    ->exists();

        if ($isExist) {
            return response()->json([
                'message' => "Data lama angsuran {$request->tenor} bulan untuk {$request->tipe} sudah ada."
            ], 422);
        }

        $bunga = ($request->tipe === 'Anggota') ? 1.00 : 2.00;

        $admin = auth()->user()->nama_lengkap ?? 'Admin';
        $tenor = Tenor::create([
            'tenor' => $request->tenor,
            'tipe'  => $request->tipe,
            'bunga' => $bunga,
            'created_by' => $admin,
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
            'data'    => $tenor
        ], 200);
    }

public function update(Request $request, $id)
{
    $tenor = Tenor::find($id);

    if (!$tenor) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $validator = Validator::make($request->all(), [
        'tenor' => 'required|integer|min:1|max:24',
        'tipe'  => 'required|in:Anggota,Non-Anggota',
    ], [
        'tenor.min' => 'Lama angsuran minimal adalah 1 bulan.',
        'tenor.max' => 'Lama angsuran maksimal adalah 24 bulan.',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $isExist = Tenor::where('tenor', $request->tenor)
                ->where('tipe', $request->tipe)
                ->where('id', '!=', $id)
                ->exists();

    if ($isExist) {
        return response()->json([
            'message' => "Gagal update! Data tenor {$request->tenor} bulan untuk {$request->tipe} sudah ada."
        ], 422);
    }

    $bungaBaru = ($request->tipe === 'Anggota') ? 1.00 : 2.00;

    $tenorLama = $tenor->tenor;
    $tipeLama  = $tenor->tipe;

    $admin = auth()->user()->nama_lengkap ?? 'Admin';

    $tenor->update([
        'tenor'      => $request->tenor,
        'tipe'       => $request->tipe,
        'bunga'      => $bungaBaru, 
        'updated_at' => \Carbon\Carbon::now(), 
        'updated_by' => $admin,
    ]);

    return response()->json([
        'message' => "Data lama angsuran berhasil diperbarui dari {$tipeLama} - {$tenorLama} bulan menjadi {$request->tipe} - {$request->tenor} bulan.",
    ], 200);
}
}