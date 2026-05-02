<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Nasabah;
use App\Models\Simpanan;

class TransaksiSimpananController extends Controller
{
public function simpan(Request $request)
{
    $request->validate([
        'nomor_rekening' => 'required|exists:nasabah,nomor_rekening',
        'saldo' => 'required|numeric|min:1'
    ]);

    return DB::transaction(function () use ($request) {

        $nasabah = Nasabah::where('nomor_rekening', $request->nomor_rekening)->first();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Rekening tidak ditemukan'
            ], 404);
        }

        $saldoSebelum = $nasabah->saldo;
        $jumlah = $request->saldo;
        $saldoSesudah = $saldoSebelum + $jumlah;

        $kode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $pengurusLogin = auth()->user()?->nomor_pengurus ?? 'PGR000';

        $transaksi = Transaksi::create([
            'id_nasabah' => $nasabah->id,
            'kode_transaksi' => $kode,
            'jenis_transaksi' => 'setor_tunai',
            'saldo' => $jumlah,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'status_transaksi' => 'sukses',
            'dibuat_oleh' => $pengurusLogin,
            'waktu_transaksi_sukses' => now()
        ]);

        $nasabah->saldo = $saldoSesudah;
        $nasabah->save();

        return response()->json([
            'message' => 'Berhasil simpan saldo',
            'data' => [
                'kode_transaksi' => $transaksi->kode_transaksi,
                'saldo' => $transaksi->saldo,
                'saldo_sebelum' => $transaksi->saldo_sebelum,
                'saldo_sesudah' => $transaksi->saldo_sesudah,
                'status_transaksi' => $transaksi->status_transaksi,
                'dibuat_oleh' => $transaksi->dibuat_oleh,
                'waktu_dibuat' => $transaksi->waktu_transaksi_sukses,
            ]
        ], 201);
    });
}


public function indexSimpan()
{
    $data = Transaksi::with('nasabah')
        ->where('jenis_transaksi', 'setor_tunai')
        ->get();

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data transaksi simpan belum ada',
            'data' => []
        ]);
    }

    return response()->json([
        'message' => 'List transaksi simpan',
        'data' => $data->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_rekening' => optional($item->nasabah)->nomor_rekening,
                'jenis_transaksi' => $item->jenis_transaksi
            ];
        })
    ]);
}

public function detailSimpan($id)
{
    $item = Transaksi::with('nasabah')
        ->where('jenis_transaksi', 'setor_tunai')
        ->find($id);

    if (!$item) {
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail transaksi simpan',
        'data' => [
            'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
            'nomor_rekening' => optional($item->nasabah)->nomor_rekening,
            'kode_transaksi' => $item->kode_transaksi,
            'jenis_transaksi' => $item->jenis_transaksi,
            'saldo' => $item->saldo,
            'saldo_sebelum' => $item->saldo_sebelum,
            'saldo_sesudah' => $item->saldo_sesudah,
            'waktu_dibuat' => $item->waktu_dibuat,
            'dibuat_oleh' => $item->dibuat_oleh,
        ]
    ]);
}
}
