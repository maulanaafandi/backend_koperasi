<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Pengurus;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function loginAdmin(Request $request) {
        $request->validate(['nomor_admin' => 'required', 'password' => 'required']);
        
        $admin = Admin::where('nomor_admin', $request->nomor_admin)->first();

        if (!$admin) {
            return response()->json(['message' => 'Akun belum terdaftar, silahkan melakukan registrasi akun terlebih dahulu'], 404);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Kredensial (password) salah'], 401);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;
        return response()->json(['token' => $token]);
    }

public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil. Sesi untuk ' . class_basename($user) . ' telah diakhiri.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal logout, token tidak valid atau sudah tidak aktif.'
        ], 401);
    }

public function loginPengurus(Request $request) {
    $request->validate([
        'nomor_pengurus' => 'required',
        'password' => 'required'
    ]);

    $pengurus = Pengurus::where('nomor_pengurus', $request->nomor_pengurus)->first();

    if (!$pengurus) {
        return response()->json([
            'message' => 'Nomor pengurus tidak terdaftar.'
        ], 404); 
    }


    if (is_null($pengurus->password)) {
        return response()->json([
            'message' => 'Anda belum melakukan daftar ulang. Silakan lengkapi data Anda terlebih dahulu.'
        ], 403);
    }


    if (!Hash::check($request->password, $pengurus->password)) {
        return response()->json(['message' => 'Password salah'], 401);
    }

    if ($pengurus->status_akun !== 'Aktif') {
        return response()->json([
            'message' => 'Akun Anda sedang dalam status ' . $pengurus->status_akun . '. Silakan hubungi admin untuk aktivasi.'
        ], 403);
    }

    $token = $pengurus->createToken('pengurus_token')->plainTextToken;

    return response()->json([
        'token'   => $token,
    ], 200);
}
public function daftarUlangPengurus(Request $request)
{
    $pengurus = Pengurus::where('nomor_pengurus', $request->nomor_pengurus)->first();
    $idPengurus = $pengurus ? $pengurus->id : null;

    $validator = Validator::make($request->all(), [
        'nomor_pengurus'  => 'required|exists:pengurus,nomor_pengurus',
        'nama_lengkap'    => 'required|string|max:255',
        'jenis_kelamin'   => 'required|in:L,P',
        'nomor_handphone' => 'required|string|max:12|unique:pengurus,nomor_handphone,' . $idPengurus,
        'foto_profil'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'password'        => ['required', 'confirmed', Password::min(6)->letters()->numbers()->mixedCase()],
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    if ($pengurus->waktu_daftar_ulang !== null) {
        return response()->json(['message' => 'Anda sudah daftar ulang. Tunggu aktivasi admin.'], 403);
    }

    if ($request->hasFile('foto_profil')) {
        $pengurus->foto_profil = $request->file('foto_profil')->store('foto_pengurus', 'public');
    }

    $pengurus->nama_lengkap     = $request->nama_lengkap;
    $pengurus->jenis_kelamin    = $request->jenis_kelamin;
    $pengurus->nomor_handphone  = $request->nomor_handphone;
    $pengurus->password         = Hash::make($request->password);
    
    $pengurus->status_akun        = 'Proses'; 
    $pengurus->waktu_daftar_ulang = now(); 
    
    $pengurus->save();

    return response()->json(['message' => 'Daftar ulang berhasil. Menunggu verifikasi admin.'], 200);
}
}
