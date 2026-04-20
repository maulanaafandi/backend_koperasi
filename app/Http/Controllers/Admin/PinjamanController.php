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
            'nama_lengkap' => $item->nasabah->nama_lengkap,
            'nomor_nasabah' => $item->nasabah->nomor_nasabah,
            'waktu_dibuat' => $item->waktu_dibuat,
            'dibuat_oleh' => $item->dibuat_oleh,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'status' => 'Proses'
        ]
    ]);
}

public function jatuhTempo()
{
    $data = $this->filterByStatusAngsuran('jatuh_tempo');

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman jatuh tempo belum ada',
            'data' => []
        ]);
    }

    return response()->json([
        'message' => 'List pinjaman jatuh tempo',
        'data' => $data->map(function ($item) {
            return [
                'nama_lengkap' => $item->nasabah->nama_lengkap,
                'nomor_nasabah' => $item->nasabah->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'waktu_dibuat' => $item->waktu_dibuat,
                'status_angsuran' => 'jatuh_tempo'
            ];
        })
    ]);
}

public function detailJatuhTempo($id)
{
    $item = Pinjaman::with(['nasabah', 'cicilan'])
        ->whereHas('cicilan', function ($q) {
            $q->where('status_angsuran', 'jatuh_tempo');
        })
        ->find($id);

    if (!$item) {
        return response()->json([
            'message' => 'Data pinjaman jatuh tempo tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pinjaman jatuh tempo',
        'data' => [
            'nama_lengkap' => $item->nasabah->nama_lengkap,
            'nomor_nasabah' => $item->nasabah->nomor_nasabah,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'status_angsuran' => 'jatuh_tempo',
            'waktu_dibuat' => $item->waktu_dibuat,
            'dibuat_oleh' => $item->dibuat_oleh,
            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
        ]
    ]);
}

public function macet()
{
    $data = $this->filterByStatusAngsuran('macet');

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman macet belum ada',
            'data' => []
        ]);
    }

    return response()->json([
        'message' => 'List pinjaman macet',
        'data' => $data->map(function ($item) {
            return [
                'nama_lengkap' => $item->nasabah->nama_lengkap,
                'nomor_nasabah' => $item->nasabah->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'waktu_dibuat' => $item->waktu_dibuat,
                'status_angsuran' => 'macet'
            ];
        })
    ]);
}

public function detailMacet($id)
{
    $item = Pinjaman::with(['nasabah', 'cicilan'])
        ->whereHas('cicilan', function ($q) {
            $q->where('status_angsuran', 'macet');
        })
        ->find($id);

    if (!$item) {
        return response()->json([
            'message' => 'Data pinjaman macet tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pinjaman macet',
        'data' => [
            'nama_lengkap' => $item->nasabah->nama_lengkap,
            'nomor_nasabah' => $item->nasabah->nomor_nasabah,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'waktu_pinjaman' => $item->created_at,
            'status_angsuran' => 'macet',
            'waktu_dibuat' => $item->waktu_dibuat,
            'dibuat_oleh' => $item->dibuat_oleh,
            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
        ]
    ]);
}

public function lunas()
{
    $data = $this->filterByStatusAngsuran('lunas');

    if ($data->isEmpty()) {
        return response()->json([
            'message' => 'Data pinjaman lunas belum ada',
            'data' => []
        ]);
    }

    return response()->json([
        'message' => 'List pinjaman lunas',
        'data' => $data->map(function ($item) {
            return [
                'nama_lengkap' => $item->nasabah->nama_lengkap,
                'nomor_nasabah' => $item->nasabah->nomor_nasabah,
                'jumlah_pinjaman' => $item->jumlah_pinjaman,
                'waktu_dibuat' => $item->waktu_dibuat,
                'status_angsuran' => 'lunas'
            ];
        })
    ]);
}

public function detailLunas($id)
{
    $item = Pinjaman::with(['nasabah', 'cicilan'])
        ->whereHas('cicilan', function ($q) {
            $q->where('status_angsuran', 'lunas');
        })
        ->find($id);

    if (!$item) {
        return response()->json([
            'message' => 'Data pinjaman lunas tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pinjaman lunas',
        'data' => [
            'nama_lengkap' => $item->nasabah->nama_lengkap,
            'nomor_nasabah' => $item->nasabah->nomor_nasabah,
            'jumlah_pinjaman' => $item->jumlah_pinjaman,
            'status_angsuran' => 'lunas',
            'waktu_dibuat' => $item->waktu_dibuat,
            'dibuat_oleh' => $item->dibuat_oleh,
            'waktu_disetujui' => $item->waktu_disetujui,
            'disetujui_oleh' => $item->disetujui_oleh,
        ]
    ]);
}
}
