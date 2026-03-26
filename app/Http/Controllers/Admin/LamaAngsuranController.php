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
        $data = Tenor::select('id', 'tenor', 'persen')->get();
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenor'  => 'required|integer|unique:tenor,tenor',
            'persen' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tenor = Tenor::create($request->only('tenor', 'persen'));

        return response()->json([
            'message' => 'Data tenor berhasil ditambahkan',
            'data'    => $tenor
        ], 201);
    }

    public function show($id)
    {
        $tenor = Tenor::find($id);
        if (!$tenor) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        
        return response()->json(['success' => true, 'data' => $tenor], 200);
    }

    public function update(Request $request, $id)
    {
        $tenor = Tenor::find($id);
        if (!$tenor) return response()->json(['message' => 'Nomor tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'tenor'  => 'required|integer|unique:tenor,tenor,' . $id,
            'persen' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tenor->update($request->only('tenor', 'persen'));

        return response()->json([
            'message' => 'Data tenor berhasil diperbarui',
            'data'    => $tenor
        ], 200);
    }
}