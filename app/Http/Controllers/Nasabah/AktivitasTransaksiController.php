<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class AktivitasTransaksiController extends Controller
{
    public function aktivitasTransaksiNasabah(Request $request)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $data = Transaksi::where('id_nasabah', $nasabah->id)
            ->latest()
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada aktivitas transaksi',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Aktivitas transaksi nasabah',
            'data' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kode_transaksi' => $item->kode_transaksi,
                    'saldo' => $item->saldo,
                    'waktu_dibuat' => $item->waktu_dibuat,
                    'status' => $item->status_transaksi,
                ];
            })
        ], 200);
    }

    public function detailAktivitasTransaksi($id)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $item = Transaksi::where('id', $id)
            ->where('id_nasabah', $nasabah->id)
            ->first();

        if (!$item) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail aktivitas transaksi',
            'data' => [
                'kode_transaksi' => $item->kode_transaksi,
                'nama_lengkap' => $nasabah->nama_lengkap,
                'saldo' => $item->saldo,
                'waktu_transaksi_sukses' => $item->waktu_transaksi_sukses,
                'status' => $item->status_transaksi,
            ]
        ], 200);
    }
}
