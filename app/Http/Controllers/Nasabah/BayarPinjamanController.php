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
    public function bayarCicilanNasabah($id)
    {
        return DB::transaction(function () use ($id) {

            $nasabah = auth()->user();

            if (!$nasabah) {
                return response()->json([
                    'message' => 'Nasabah tidak ditemukan'
                ], 404);
            }

            $cicilan = CicilanPinjaman::with('pinjaman.tenor')
                ->find($id);

            if (!$cicilan) {
                return response()->json([
                    'message' => 'Cicilan tidak ditemukan'
                ], 404);
            }

            if ($cicilan->pinjaman->id_nasabah !== $nasabah->id) {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }

            if ($cicilan->status_angsuran === 'lunas') {
                return response()->json([
                    'message' => 'Cicilan sudah lunas'
                ], 422);
            }

            $pinjaman = $cicilan->pinjaman;

            $bungaPersen = $pinjaman->tenor->bunga ?? 0;
            $bunga = ($cicilan->tagihan_pokok * $bungaPersen) / 100;

            $hariTelat = 0;

            if (now()->gt($cicilan->tanggal_jatuh_tempo)) {
                $hariTelat = Carbon::parse($cicilan->tanggal_jatuh_tempo)
                    ->diffInDays(now());

                if ($cicilan->status_angsuran !== 'macet') {
                    $cicilan->status_angsuran = 'macet';
                    $cicilan->waktu_macet = now();
                }
            }

            $dendaPerHari = 0.01;
            $denda = ($cicilan->tagihan_pokok + $bunga) * $dendaPerHari * $hariTelat;

            $totalTagihan = $cicilan->tagihan_pokok + $bunga + $denda;

            if ($nasabah->saldo < $totalTagihan) {
                return response()->json([
                    'message' => 'Saldo tidak mencukupi'
                ], 400);
            }

            $saldoSebelum = $nasabah->saldo;
            $saldoSesudah = $saldoSebelum - $totalTagihan;

            $cicilan->bunga = $bunga;
            $cicilan->denda = $denda;
            $cicilan->total_dibayar = $totalTagihan;
            $cicilan->status_angsuran = 'lunas';
            $cicilan->waktu_dibayar = now();

            $pinjaman->sisa_pinjaman -= $totalTagihan;

            if ($pinjaman->sisa_pinjaman < 0) {
                $pinjaman->sisa_pinjaman = 0;
            }

            $pinjaman->save();

            $kode = 'TRX-' . strtoupper(Str::random(10));

            $transaksi = Transaksi::create([
                'id_nasabah' => $nasabah->id,
                'kode_transaksi' => $kode,
                'jenis_transaksi' => 'angsuran',
                'saldo' => $totalTagihan,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'status_transaksi' => 'sukses',
                'dibuat_oleh' => $nasabah->nomor_nasabah,
                'waktu_transaksi_sukses' => now()
            ]);

            $cicilan->id_transaksi = $transaksi->id;
            $cicilan->dibayar_oleh = $nasabah->nomor_nasabah;
            $cicilan->save();

            $nasabah->saldo = $saldoSesudah;
            $nasabah->save();

            return response()->json([
                'message' => 'Pembayaran cicilan berhasil',
            ], 200);
        });
    }

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
                    // 'jumlah_pinjaman' => $item->total_pinjaman,
                    'lama_angsuran'   => $item->tenor->lama_angsuran ?? null,
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
                'lama_angsuran'   => $pinjaman->tenor->lama_angsuran ?? null,
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
