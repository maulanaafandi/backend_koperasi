<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
public function index()
{
    $pengumuman = Pengumuman::latest('waktu_dibuat')->get();

    return response()->json(
        $pengumuman->map(function ($item) {
            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'foto' => $item->foto,
                'deskripsi' => $item->deskripsi,
                'dibuat_oleh' => $item->dibuat_oleh,
            ];
        })
    );
}

public function show($id)
{
    $pengumuman = Pengumuman::findOrFail($id);

    return response()->json([
        'judul' => $pengumuman->judul,
        'foto' => $pengumuman->foto,
        'deskripsi' => $pengumuman->deskripsi,
    ]);
}

public function detailpengumuman($id)
{
    $pengumuman = Pengumuman::findOrFail($id);
    return response()->json([
        'id' => $pengumuman->id,
        'judul' => $pengumuman->judul,
        'foto' => $pengumuman->foto,
        'deskripsi' => $pengumuman->deskripsi,
        'waktu_dibuat' => $pengumuman->waktu_dibuat,
        'dibuat_oleh' => $pengumuman->dibuat_oleh,
        'waktu_diubah' => $pengumuman->waktu_diubah,
        'diubah_oleh' => $pengumuman->diubah_oleh,
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'judul' => 'required',
        'deskripsi' => 'required',
        'foto' => 'nullable|image'
    ]);

    $pengurusLogin = auth()->user()->nomor_pengurus ?? 'PGR000';

    $pengumuman = Pengumuman::create([
        'judul' => $request->judul,
        'foto' => $request->file('foto'),
        'deskripsi' => $request->deskripsi,
        'dibuat_oleh' => $pengurusLogin,
        'diubah_oleh' => null,
    ]);

    return response()->json([
        'message' => 'Pengumuman berhasil dibuat'
    ]);
}

public function update(Request $request, $id)
{
    $pengumuman = Pengumuman::findOrFail($id);

    $request->validate([
        'judul'     => 'nullable|string',
        'deskripsi' => 'nullable|string',
        'foto'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $pengurusLogin = auth()->user()?->nomor_pengurus ?? 'PGR000';

    if ($request->hasFile('foto')) {

        if ($pengumuman->foto && Storage::disk('public')->exists($pengumuman->foto)) {
            Storage::disk('public')->delete($pengumuman->foto);
        }

        $fotoPath = $request->file('foto')->store('pengumuman', 'public');
        $pengumuman->foto = $fotoPath;
    }

    if ($request->filled('judul')) {
        $pengumuman->judul = $request->judul;
    }

    if ($request->filled('deskripsi')) {
        $pengumuman->deskripsi = $request->deskripsi;
    }

    $pengumuman->diubah_oleh = $pengurusLogin;

    $pengumuman->save();

    return response()->json([
        'message' => 'Pengumuman berhasil diupdate',
    ], 200);
}

public function destroy($id)
{
    $pengumuman = Pengumuman::find($id);

    if (!$pengumuman) {
        return response()->json([
            'message' => 'Pengumuman tidak ditemukan'
        ], 404);
    }

    if ($pengumuman->foto) {
        Storage::disk('public')->delete($pengumuman->foto);
    }

    $pengumuman->delete();

    return response()->json([
        'message' => 'Pengumuman berhasil dihapus'
    ]);
}
}
