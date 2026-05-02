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
public function bayarCicilan(Request $request)
{
    $request->validate([
        'kode_cicilan_pinjaman' => 'required|exists:cicilan_pinjaman,kode_cicilan_pinjaman'
    ]);

    return DB::transaction(function () use ($request) {

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

        if ($cicilan->status_angsuran === 'lunas') {
            return response()->json([
                'message' => 'Cicilan sudah lunas'
            ], 422);
        }

        $pinjaman = $cicilan->pinjaman;
        $nasabah = $pinjaman->nasabah;

        $bungaPersen = $pinjaman->tenor->bunga ?? 0;

        $bunga = ($cicilan->tagihan_pokok * $bungaPersen) / 100;

        $hariTelat = 0;

        if (now()->gt($cicilan->tanggal_jatuh_tempo)) {

            $hariTelat = Carbon::parse(
                $cicilan->tanggal_jatuh_tempo
            )->diffInDays(now());

            if ($cicilan->status_angsuran !== 'macet') {
                $cicilan->status_angsuran = 'macet';
            }
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

        if ($nasabah->saldo < $totalTagihan) {
            return response()->json([
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        $saldoSebelum = $nasabah->saldo;

        $saldoSesudah = $saldoSebelum - $totalTagihan;

        $cicilan->bunga = $bunga;
        $cicilan->denda = $denda;
        $cicilan->status_angsuran = 'lunas';
        $cicilan->waktu_dibayar = now();

        $pinjaman->sisa_pinjaman -= $totalTagihan;

        if ($pinjaman->sisa_pinjaman < 0) {
            $pinjaman->sisa_pinjaman = 0;
        }

        $pinjaman->save();

        $kode = 'TRX-' . strtoupper(Str::random(10));

        $pengurusLogin = auth()->user()?->nomor_pengurus ?? 'PGR000';

        $transaksi = Transaksi::create([
            'id_nasabah' => $nasabah->id,
            'kode_transaksi' => $kode,
            'jenis_transaksi' => 'angsuran',
            'saldo' => $totalTagihan,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'status_transaksi' => 'sukses',
            'dibuat_oleh' => $pengurusLogin,
            'waktu_transaksi_sukses' => now()
        ]);

        $cicilan->id_transaksi = $transaksi->id;
        $cicilan->dibayar_oleh = $pengurusLogin;
        $cicilan->save();

        $nasabah->saldo = $saldoSesudah;
        $nasabah->save();

        return response()->json([
            'message' => 'Pembayaran cicilan berhasil',
        ], 200);
    });
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

    public function statuslunasPinjaman(Request $request)
    {
        $request->validate([
            'kode_cicilan_pinjaman' => 'required|exists:cicilan_pinjaman,kode_cicilan_pinjaman'
        ]);

        $cicilan = CicilanPinjaman::with('pinjaman')
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

        $cicilan->status_angsuran = 'lunas';
        $cicilan->waktu_dibayar = now();
        $cicilan->dibayar_oleh = auth()->user()?->nomor_pengurus ?? 'PGR000';
        $cicilan->save();

        $pinjaman->status = 'Lunas';
        $pinjaman->sisa_pinjaman = 0;
        $pinjaman->save();

        return response()->json([
            'message' => 'Status lunas'
        ], 200);
    }
}
