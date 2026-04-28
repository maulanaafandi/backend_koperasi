<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nasabah;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileSettingController extends Controller
{
    public function profileNasabah(Request $request)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => [
                'nama_lengkap' => $nasabah->nama_lengkap,
                'foto_profil' => $nasabah->foto_profil_url,
                'nomor_nasabah' => $nasabah->nomor_nasabah,
                'nomor_handphone' => $nasabah->nomor_handphone,
                'email' => $nasabah->email,
                'status_akun' => $nasabah->status,
                'nomor_rekening' => $nasabah->nomor_rekening,
            ]
        ], 200);
    }

    public function updateProfileNasabah(Request $request)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama_lengkap'      => 'nullable|string|max:255',
            'nomor_handphone'   => 'nullable|string|max:15|unique:nasabah,nomor_handphone,' . $nasabah->id,
            'email'             => 'nullable|email|unique:nasabah,email,' . $nasabah->id,
            'foto_profil'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $nasabah->nama_lengkap = $request->input('nama_lengkap', $nasabah->nama_lengkap);
        $nasabah->nomor_handphone = $request->input('nomor_handphone', $nasabah->nomor_handphone);
        $nasabah->email = $request->input('email', $nasabah->email);

        if ($request->hasFile('foto_profil')) {

            if ($nasabah->foto_profil && Storage::disk('public')->exists($nasabah->foto_profil)) {
                Storage::disk('public')->delete($nasabah->foto_profil);
            }

            $path = $request->file('foto_profil')
                            ->store('nasabah/foto_profil', 'public');

            $nasabah->foto_profil = $path;
        }

        $nasabah->save();


        return response()->json([
            'message' => 'Profile berhasil diperbarui',
            'data' => [
                'nama_lengkap'    => $nasabah->nama_lengkap,
                'foto_profil'     => $nasabah->foto_profil_url,
                'nomor_handphone' => $nasabah->nomor_handphone,
                'email'           => $nasabah->email,
            ]
        ], 200);
    }

    public function updatePinNasabah(Request $request)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'pin_lama' => 'required',
            'pin_baru' => 'required|min:6|confirmed',
        ], [
            'pin_baru.confirmed' => 'Konfirmasi PIN baru tidak sesuai'
        ]);

        if (!Hash::check($request->pin_lama, $nasabah->pin)) {
            return response()->json([
                'message' => 'PIN lama salah'
            ], 422);
        }

        if (Hash::check($request->pin_baru, $nasabah->pin)) {
            return response()->json([
                'message' => 'PIN baru tidak boleh sama dengan PIN lama'
            ], 422);
        }

        $nasabah->pin = $request->pin_baru;
        $nasabah->save();

        return response()->json([
            'message' => 'PIN berhasil diperbarui'
        ], 200);
    }

    public function updatePasswordNasabah(Request $request)
    {
        $nasabah = auth()->user();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak sesuai'
        ]);

        if (!Hash::check($request->password_lama, $nasabah->password)) {
            return response()->json([
                'message' => 'Password lama salah'
            ], 422);
        }

        if (Hash::check($request->password_baru, $nasabah->password)) {
            return response()->json([
                'message' => 'Password baru tidak boleh sama dengan password lama'
            ], 422);
        }

        $nasabah->password = Hash::make($request->password_baru);
        $nasabah->save();

        return response()->json([
            'message' => 'Password berhasil diperbarui'
        ], 200);
    }

    public function getNasabahById($id)
    {
        $nasabah = Nasabah::find($id);

        if (!$nasabah) {
            return response()->json([
                'message' => 'Nasabah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail nasabah',
            'data' => [
                'nama_lengkap' => $nasabah->nama_lengkap,
                'foto_profil' => $nasabah->foto_profil_url,
                'nomor_handphone' => $nasabah->nomor_handphone,
                'email' => $nasabah->email,
            ]
        ], 200);
    }
}

