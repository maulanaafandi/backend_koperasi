<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\CicilanPinjaman;


class PinjamanController extends Controller
{
     private function filterByStatusAngsuran($status)
    {
        return Pinjaman::with(['nasabah', 'cicilan'])
            ->whereHas('cicilan', function ($q) use ($status) {
                $q->where('status_angsuran', $status);
            })
            ->get();
    }

     public function pengajuan()
    {
        $data = Pinjaman::with('nasabah')
            ->where('status', 'proses')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Data pengajuan belum ada',
                'data' => []
            ]);
        }

    return response()->json([
        'message' => 'List pengajuan',
        'data' => $data->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_lengkap' => $item->nasabah->nama_lengkap,
                'nomor_nasabah' => $item->nasabah->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'status' => 'Proses'
            ];
        })
    ]);
}

public function detailPengajuan($id)
{
    $item = Pinjaman::with('nasabah')->find($id);

    if (!$item) {
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pengajuan',
        'data' => [
            'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
            'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'status' => $item->status, 
            'jaminan' => $item->jaminan,
            'foto_jaminan' => $item->foto_jaminan,
            'nilai_jaminan' => $item->nilai_jaminan,
            'waktu_dibuat' => $item->waktu_dibuat,
            'dibuat_oleh' => $item->dibuat_oleh,
            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
            'waktu_tidak_disetujui' => $item->waktu_tidak_setujui,
            'tidak_disetujui_oleh' => $item->tidak_setujui_oleh,
        ]
    ], 200);
}

public function jatuhTempo()
{
    $data = Pinjaman::with([
        'nasabah',
        'cicilan' => function ($q) {
            $q->where('status_angsuran', 'jatuh_tempo');
        }
    ])
    ->whereHas('cicilan', function ($q) {
        $q->where('status_angsuran', 'jatuh_tempo');
    })
    ->get();

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman jatuh tempo belum ada',
            'data' => []
        ], 404);
    }

    return response()->json([
        'message' => 'List pinjaman jatuh tempo',
        'data' => $data->map(function ($item) {

            $cicilan = $item->cicilan->first();

            return [
                'id' => $item->id,
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'tanggal_jatuh_tempo' => optional($cicilan)->tanggal_jatuh_tempo,
            ];
        })
    ], 200);
}

public function detailJatuhTempo($id)
{
    $item = Pinjaman::with(['nasabah', 'cicilan' => function ($q) {
        $q->where('status_angsuran', 'jatuh_tempo');
    }])->find($id);

    if (!$item || $item->cicilan->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman jatuh tempo tidak ditemukan'
        ], 404);
    }

    $cicilan = $item->cicilan->first();

    return response()->json([
        'message' => 'Detail pinjaman jatuh tempo',
        'data' => [
            'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
            'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'waktu_pinjaman' => $item->waktu_dibuat,
            'nomor_angsuran' => $cicilan->nomor_angsuran,
            'status_angsuran' => $cicilan->status_angsuran,
            'total_tagihan' => $cicilan->total_tagihan,
            'tanggal_jatuh_tempo' => $cicilan->tanggal_jatuh_tempo,
            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
        ]
    ], 200);
}

public function macet()
{
    $data = $this->filterByStatusAngsuran('macet');

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman macet belum ada',
            'data' => []
        ], 404);
    }

    return response()->json([
        'message' => 'List pinjaman macet',
        'data' => $data->map(function ($item) {

            $cicilan = $item->cicilan->first();
            return [
                'id' => $item->id,
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'status_angsuran' => optional($cicilan)->status_angsuran,
            ];
        })
    ], 200);
}

public function detailMacet($id)
{
    $item = Pinjaman::with([
        'nasabah',
        'cicilan' => function ($q) {
            $q->where('status_angsuran', 'macet');
        }
    ])
    ->whereHas('cicilan', function ($q) {
        $q->where('status_angsuran', 'macet');
    })
    ->find($id);

    if (!$item || $item->cicilan->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman macet tidak ditemukan'
        ], 404);
    }

    $cicilan = $item->cicilan->first();
    return response()->json([
        'message' => 'Detail pinjaman macet',
        'data' => [
            'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
            'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,

            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'waktu_pinjaman' => $item->waktu_dibuat,

            'nomor_angsuran' => $cicilan->nomor_angsuran,
            'status_angsuran' => $cicilan->status_angsuran,
            'total_tagihan' => $cicilan->total_tagihan,
            'tanggal_jatuh_tempo' => $cicilan->tanggal_jatuh_tempo,

            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
        ]
    ], 200);
}

public function lunas()
{
    $data = $this->filterByStatusAngsuran('lunas');

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman lunas belum ada',
            'data' => []
        ], 404);
    }

    return response()->json([
        'message' => 'List pinjaman lunas',
        'data' => $data->map(function ($item) {

            $cicilan = $item->cicilan->first();
            return [
                'id' => $item->id,
                'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
                'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'status_angsuran' => optional($cicilan)->status_angsuran,
            ];
        })
    ], 200);
}

public function detailLunas($id)
{
    $item = Pinjaman::with([
        'nasabah',
        'cicilan' => function ($q) {
            $q->where('status_angsuran', 'lunas');
        }
    ])
    ->whereHas('cicilan', function ($q) {
        $q->where('status_angsuran', 'lunas');
    })
    ->find($id);

    if (!$item || $item->cicilan->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman lunas tidak ditemukan'
        ], 404);
    }

    $cicilan = $item->cicilan->first();
    return response()->json([
        'message' => 'Detail pinjaman lunas',
        'data' => [
            'nama_lengkap' => optional($item->nasabah)->nama_lengkap,
            'nomor_nasabah' => optional($item->nasabah)->nomor_nasabah,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'waktu_pinjaman' => $item->waktu_dibuat,
            'nomor_angsuran' => $cicilan->nomor_angsuran,
            'status_angsuran' => $cicilan->status_angsuran,
            'total_tagihan' => $cicilan->total_tagihan,
            'tanggal_jatuh_tempo' => $cicilan->tanggal_jatuh_tempo,
            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
        ]
    ], 200);
}
}
