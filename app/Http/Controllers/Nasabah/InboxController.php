<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;

class InboxController extends Controller
{
    public function getInbox()
    {
        $data = Pengumuman::latest('created_at')->get();

        return response()->json([
            'data' => $data->map(function ($item) {
                return [
                    'judul'     => $item->judul,
                    'foto'      => $item->foto,
                    'deskripsi' => $item->deskripsi,
                ];
            })
        ], 200);
    }

    public function getDetailInbox($id)
    {
        $item = Pengumuman::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => [
                'judul'         => $item->judul,
                'foto'          => $item->foto,
                'deskripsi'     => $item->deskripsi,
                'waktu_dibuat'  => $item->created_at,
                'dibuat_oleh'   => $item->dibuat_oleh,
                'waktu_diubah'  => $item->updated_at,
                'diubah_oleh'   => $item->diubah_oleh,
            ]
        ], 200);
    }
}
