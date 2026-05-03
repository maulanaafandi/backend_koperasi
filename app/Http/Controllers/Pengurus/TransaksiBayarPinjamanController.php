<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\Transaksi;
use App\Models\Nasabah;
use App\Models\Tenor;
use App\Models\CicilanPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use Carbon\Carbon;

class TransaksiBayarPinjamanController extends Controller
{
public function detailBayarCicilan(Request $request)
{
    $request->validate([
        'kode_cicilan_pinjaman' => 'required|exists:cicilan_pinjaman,kode_cicilan_pinjaman'
    ]);

    $cicilan = CicilanPinjaman::with('pinjaman.tenor', 'pinjaman.nasabah')
        ->where(
            'kode_cicilan_pinjaman',
            $request->kode_cicilan_pinjaman
        )
        ->first();

    if (!$cicilan) {
        return response()->json([
            'message' => 'Data cicilan tidak ditemukan'
        ], 404);
    }

    $pinjaman = $cicilan->pinjaman;

    if (!$pinjaman) {
        return response()->json([
            'message' => 'Data pinjaman tidak ditemukan'
        ], 404);
    }

    // if ($pinjaman->status !== 'Disetujui') {
    //     return response()->json([
    //         'message' => 'Pinjaman belum disetujui'
    //     ], 422);
    // }

    $nasabah = $pinjaman->nasabah;

    $bungaPersen = $pinjaman->tenor->bunga ?? 0;

    $bunga = ($cicilan->tagihan_pokok * $bungaPersen) / 100;

    $hariTelat = 0;

    if (now()->gt($cicilan->tanggal_jatuh_tempo)) {
        $hariTelat = Carbon::parse(
            $cicilan->tanggal_jatuh_tempo
        )->diffInDays(now());
    }

    $dendaPerHari = 0.01;

    $denda = (
        ($cicilan->tagihan_pokok + $bunga)
        * $dendaPerHari
        * $hariTelat
    );

    $totalTagihan = (
        $cicilan->tagihan_pokok
        + $bunga
        + $denda
    );

    return response()->json([
        'data' => [
            'nama_lengkap' => $nasabah->nama_lengkap,
            'nomor_rekening' => $nasabah->nomor_rekening,
            'nomor_angsuran' => $cicilan->nomor_angsuran,
            'total_tagihan' => $totalTagihan,
        ]
    ], 200);
}

    public function getBayarPinjaman()
    {
        $data = Pinjaman::with('nasabah')
            ->latest()
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Data pinjaman belum ada',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'List data bayar pinjaman',
            'data' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                    'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
                    'jumlah_pinjaman' => $item->jumlah_pinjaman,
                    'pinjaman_lunas' => $item->sisa_pinjaman <= 0
                ];
            })
        ], 200);
    }

    public function detailBayarPinjaman($id)
    {
        $item = Pinjaman::with(['nasabah', 'tenor'])->find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Data pinjaman tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail bayar pinjaman',
            'data' => [
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'status' => $item->status,
                'tipe' => optional($item->tenor)->tipe,
                'lama_angsuran' => optional($item->tenor)->lama_angsuran_label,
                'bunga' => optional($item->tenor)->bunga,
                'waktu_dibuat' => $item->waktu_dibuat,
                'dibuat_oleh' => $item->dibuat_oleh,
                'waktu_disetujui' => $item->waktu_disetujui,
                'disetujui_oleh' => $item->disetujui_oleh,
            ]
        ], 200);
    }

    public function statuslunasPinjaman($kode_cicilan_pinjaman)
    {
        $cicilan = CicilanPinjaman::with('pinjaman')
            ->where(
                'kode_cicilan_pinjaman',
                $kode_cicilan_pinjaman
            )
            ->first();

        if (!$cicilan) {
            return response()->json([
                'message' => 'Data cicilan tidak ditemukan'
            ], 404);
        }

        $pinjaman = $cicilan->pinjaman;

        if (!$pinjaman) {
            return response()->json([
                'message' => 'Data pinjaman tidak ditemukan'
            ], 404);
        }

        // if ($pinjaman->status !== 'Disetujui') {
        // return response()->json([
        //     'message' => 'Pinjaman belum disetujui'
        // ], 422);
        // }

        $cicilan->status_angsuran = 'lunas';
        $cicilan->waktu_dibayar = now();
        $cicilan->dibayar_oleh = auth()->user()?->nomor_pengurus ?? 'PGR000';
        $cicilan->save();

        return response()->json([
            'message' => 'Status cicilan lunas'
        ], 200);
    }
}
