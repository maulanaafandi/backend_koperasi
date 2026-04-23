<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Transaksi;
use App\Models\Nasabah;
use App\Models\Tenor;
use App\Models\CicilanPinjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;

class TransaksiController extends Controller
{
    public function getTransaksi()
    {
        $data = Transaksi::with('nasabah')
            ->latest()
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Data transaksi belum ada',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'List transaksi',
            'data' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                    'nomor_rekening' => optional($item->nasabah)->nomor_rekening,
                    'jenis_transaksi' => $item->jenis_transaksi,
                    'status_transaksi' => $item->status_transaksi,
                ];
            })
        ], 200);
    }

    public function detailTransaksi($id)
    {
        $item = Transaksi::with('nasabah')->find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Data transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail transaksi',
            'data' => [
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_rekening' => optional($item->nasabah)->nomor_rekening,
                'kode_transaksi' => $item->kode_transaksi,
                'jenis_transaksi' => $item->jenis_transaksi,
                'status_transaksi' => $item->status_transaksi,
                'saldo' => $item->saldo,
                'saldo_sebelum' => $item->saldo_sebelum,
                'saldo_sesudah' => $item->saldo_sesudah,
                'waktu_dibuat' => $item->waktu_dibuat,
                'dibuat_oleh' => $item->dibuat_oleh,
            ]
        ], 200);
    }
}
