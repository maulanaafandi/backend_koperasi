<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisSimpanan;

class JenisSimpananNasabahController extends Controller
{
    public function getJenisSimpanan()
    {
        $data = JenisSimpanan::all();

        return response()->json([
            'data' => $data->map(function ($item) {
                return [
                    'id'             => $item->id,
                    'nama_simpanan'  => $item->nama_simpanan,
                    'saldo_minimal'  => $item->saldo_minimal,
                ];
            })
        ], 200);
    }
}
