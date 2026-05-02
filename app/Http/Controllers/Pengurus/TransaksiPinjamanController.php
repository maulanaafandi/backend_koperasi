<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\Transaksi;
use App\Models\Nasabah;
use App\Models\Tenor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransaksiPinjamanController extends Controller
{
public function index()
{
    $data = Pinjaman::with(['nasabah', 'tenor'])
        ->latest()
        ->get();

    return response()->json([
        'message' => 'Daftar Pinjaman Berhasil Dimuat',
        'data' => $data->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_rekening' => optional($item->nasabah)->nomor_rekening,
                'lama_angsuran'  => optional($item->tenor)->lama_angsuran_label,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
            ];
        })
    ]);
}

public function show($id)
{
    $pinjaman = Pinjaman::with(['nasabah', 'tenor'])->find($id);

    if (!$pinjaman) {
        return response()->json([
            'message' => 'Data pinjaman tidak ditemukan'
        ], 404);
    }

    $persenBunga = $pinjaman->tenor->bunga ?? 0;

    $bunga = $pinjaman->jumlah_pinjaman * $persenBunga;

    return response()->json([
        'data' => [
            'nama_lengkap' => optional($pinjaman->nasabah)->nama_lengkap,
            'nomor_rekening' => optional($pinjaman->nasabah)->nomor_rekening,
            'lama_angsuran'  => optional($pinjaman->tenor)->lama_angsuran_label,
            'jumlah_pinjaman' => $pinjaman->jumlah_pinjaman,
            'bunga' => $bunga,
            'jaminan' => $pinjaman->jaminan,
            'jaminan_pinjaman' => $pinjaman->jaminan,
            'foto_jaminan' => $pinjaman->foto_jaminan,
            'nilai_jaminan' => $pinjaman->nilai_jaminan,
            'waktu_dibuat' => $pinjaman->waktu_dibuat,
            'dibuat_oleh' => $pinjaman->dibuat_oleh,
            'waktu_disetujui' => $pinjaman->waktu_disetujui,
            'disetujui_oleh' => $pinjaman->disetujui_oleh,
            'waktu_tidak_disetujui' => $pinjaman->waktu_tidak_setujui,
            'tidak_disetujui_oleh' => $pinjaman->tidak_setujui_oleh,
        ]
    ], 200);
}

public function getStatus($id)
{
    $pinjaman = Pinjaman::find($id);

    if (!$pinjaman) {
        return response()->json([
            'message' => 'Data pinjaman tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'status' => $pinjaman->status
    ], 200);
}

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nomor_rekening' => 'required|exists:nasabah,nomor_rekening',
        'id_tenor' => 'required|exists:tenor,id',
        'jumlah_pinjaman' => 'required|numeric|min:1000000|max:150000000',
        'jaminan' => 'required|string',
        'foto_jaminan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'nilai_jaminan' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $nasabah = Nasabah::where(
        'nomor_rekening',
        $request->nomor_rekening
    )->first();

    $tenor = Tenor::findOrFail($request->id_tenor);

    if ($nasabah->tipe !== $tenor->tipe) {
        return response()->json([
            'message' => "Tenor {$tenor->tipe} tidak sesuai dengan tipe nasabah {$nasabah->tipe}"
        ], 422);
    }

    $pengurusLogin = optional(auth()->user())->nomor_pengurus ?? 'PGR000';

    $pinjaman = Pinjaman::create([
        'id_nasabah' => $nasabah->id,
        'id_tenor' => $tenor->id,
        'id_transaksi' => null,
        'jumlah_pinjaman' => $request->jumlah_pinjaman,
        'jaminan' => $request->jaminan,

        'foto_jaminan' => $request->file('foto_jaminan'),

        'nilai_jaminan' => $request->nilai_jaminan,
        'status' => 'Proses',
        'dibuat_oleh' => $pengurusLogin,
    ]);

    $kodeCicilan = 'CIC-' . strtoupper(Str::random(8));

    CicilanPinjaman::create([
        'id_pinjaman' => $pinjaman->id,
        'kode_cicilan_pinjaman' => $kodeCicilan,
        'nomor_angsuran' => 1,
        'tanggal_jatuh_tempo' => now()->addMonth(),
        'total_tagihan' => $request->jumlah_pinjaman,
        'tagihan_pokok' => $request->jumlah_pinjaman,
        'bunga' => 0,
        'denda' => 0,
        'status_angsuran' => 'belum_jatuh_tempo',
        'dibayar_oleh' => null,
    ]);

    return response()->json([
        'message' => 'Pengajuan pinjaman berhasil dibuat',
        'data' => [
            'kode_cicilan_pinjaman' => $kodeCicilan
        ]
    ], 201);
}

public function approve($id)
{
    $pinjaman = Pinjaman::with('nasabah')->find($id);

    if (!$pinjaman) {
        return response()->json([
            'message' => 'Data pinjaman tidak ditemukan'
        ], 404);
    }

    if ($pinjaman->status !== 'Proses') {
        return response()->json([
            'message' => 'Pinjaman sudah diproses sebelumnya'
        ], 400);
    }

    $pengurusLogin = optional(auth()->user())->nomor_pengurus ?? 'PGR000';

    if ($pinjaman->dibuat_oleh === $pengurusLogin) {
        return response()->json([
            'message' => 'Tidak dapat menyetujui pinjaman yang dibuat sendiri'
        ], 403);
    }

    try {
        return DB::transaction(function () use ($pinjaman, $pengurusLogin) {

            $nasabah = $pinjaman->nasabah;

            $saldoSebelum = $nasabah->saldo;
            $jumlah       = $pinjaman->jumlah_pinjaman;
            $saldoSesudah = $saldoSebelum + $jumlah;

            $transaksi = Transaksi::create([
                'id_nasabah'        => $nasabah->id,
                'kode_transaksi'    => 'TRX-' . strtoupper(Str::random(10)),
                'jenis_transaksi'   => 'transfer_masuk',
                'saldo'             => $jumlah,
                'saldo_sebelum'     => $saldoSebelum,
                'saldo_sesudah'     => $saldoSesudah,
                'status_transaksi'  => 'sukses',
                'dibuat_oleh'       => $pengurusLogin,
                'waktu_transaksi_sukses' => now()
            ]);

            $nasabah->saldo = $saldoSesudah;
            $nasabah->save();

            $pinjaman->update([
                'id_transaksi'     => $transaksi->id,
                'status'           => 'Disetujui',
                'waktu_disetujui'  => now(),
                'disetujui_oleh'   => $pengurusLogin,
            ]);

            return response()->json([
                'message' => 'Pinjaman disetujui dan dana berhasil dicairkan',
                'data' => [
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'jumlah' => $jumlah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah
                ]
            ], 200);
        });

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan sistem'
        ], 500);
    }
}

    public function getAllTenor()
    {
        $tenor = Tenor::all();

        return response()->json([
            'data' => $tenor->map(function ($item) {
                return [
                    'id' => $item->id,
                    'lama_angsuran' => $item->lama_angsuran . ' Bulan',
                    'bunga' => $item->bunga,
                ];
            })
        ], 200);
    }
}