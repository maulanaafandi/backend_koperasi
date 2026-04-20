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
    $pengumuman = Pengumuman::select('id','judul','deskripsi','dibuat_oleh')->latest('waktu_dibuat')->get();

    return response()->json($pengumuman);
}

public function show($id)
{
    $pengumuman = Pengumuman::findOrFail($id);

    return response()->json([
        'judul' => $pengumuman->judul,
        'foto' => $pengumuman->foto 
            ? asset('storage/' . $pengumuman->foto) 
            : null,
        'deskripsi' => $pengumuman->deskripsi,
    ]);
}

public function detailpengumuman($id)
{
    $pengumuman = Pengumuman::findOrFail($id);
    return response()->json([
        'judul' => $pengumuman->judul,
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

    $fotoPath = null;
    if ($request->hasFile('foto')) {
        $fotoPath = $request->file('foto')->store('pengumuman', 'public');
    }

    $pengurusLogin = auth()->user()->nomor_pengurus ?? 'PGR000';

    $pengumuman = Pengumuman::create([
        'judul' => $request->judul,
        'foto' => $fotoPath,
        'deskripsi' => $request->deskripsi,

        'dibuat_oleh' => $pengurusLogin,
        'diubah_oleh' => null, 
    ]);

    return response()->json([
        'message' => 'Pengumuman berhasil dibuat',
        // 'data' => $pengumuman
    ]);
}

public function update(Request $request, $id)
{
    $pengumuman = Pengumuman::findOrFail($id);

    $request->validate([
        'judul' => 'required',
        'deskripsi' => 'required',
    ]);

    $pengurusLogin = auth()->user()?->nomor_pengurus ?? 'PGR000';

    $pengumuman->update([
        'judul' => $request->judul,
        'deskripsi' => $request->deskripsi,
        'diubah_oleh' => $pengurusLogin,
    ]);

    return response()->json([
        'message' => 'Berhasil update pengumuman',
    ]);
}

public function updateFoto(Request $request, $id)
{
    $pengumuman = Pengumuman::findOrFail($id);

    $request->validate([
        'foto' => 'required|image'
    ]);

    if ($pengumuman->foto) {
        Storage::disk('public')->delete($pengumuman->foto);
    }

    $fotoPath = $request->file('foto')->store('pengumuman', 'public');
    $pengurusLogin = auth()->user()?->nomor_pengurus ?? 'PGR000';

    $pengumuman->update([
        'foto' => $fotoPath,
        'diubah_oleh' => $pengurusLogin,
    ]);

    return response()->json([
        'message' => 'Foto pengumuman berhasil diupdate',
    ]);
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
