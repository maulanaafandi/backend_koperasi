<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use App\Models\Nasabah;
use App\Models\CicilanPinjaman;
use App\Models\Transaksi;
use App\Models\Pinjaman; 
use App\Models\Tenor;    


class BayarPinjamanController extends Controller
{
    public function getPinjamanNasabah()
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $pinjaman = Pinjaman::with('tenor')
            ->where('id_nasabah', $nasabah->id)
            ->get();

        return response()->json([
            'message' => 'Data pinjaman',
            'data' => $pinjaman->map(function ($item) {
                return [
                    'id'               => $item->id,
                    // 'jumlah_pinjaman' => $item->total_pinjaman,
                    'lama_angsuran'   => $item->tenor->lama_angsuran_label ?? null,
                    'waktu_disetujui' => $item->waktu_disetujui,
                    'status'          => $item->status,
                ];
            })
        ], 200);
    }

    public function getDetailPinjamanNasabah($id)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $pinjaman = Pinjaman::with(['tenor', 'cicilan'])
            ->where('id', $id)
            ->where('id_nasabah', $nasabah->id)
            ->first();

        if (!$pinjaman) {
            return response()->json([
                'message' => 'Pinjaman tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pinjaman',
            'data' => [
                // 'jumlah_pinjaman' => $pinjaman->total_pinjaman,
                'lama_angsuran'   => $pinjaman->tenor->lama_angsuran_label ?? null,
                'waktu_disetujui' => $pinjaman->waktu_disetujui,
                'status'          => $pinjaman->status,
                'jaminan'         => $pinjaman->jaminan,
                'nilai_jaminan'   => $pinjaman->nilai_jaminan,

                'cicilan' => $pinjaman->cicilan->map(function ($c) {
                    return [
                        'nomor_angsuran'      => $c->nomor_angsuran,
                        'tanggal_jatuh_tempo' => $c->tanggal_jatuh_tempo,
                        'total_tagihan'       => $c->tagihan_pokok + $c->bunga + $c->denda,
                        'waktu_dibayar'       => $c->waktu_dibayar,
                        'dibayar_oleh'        => $c->dibayar_oleh,
                        'status_angsuran'     => $c->status_angsuran,
                    ];
                })
            ]
        ], 200);
    }
}
