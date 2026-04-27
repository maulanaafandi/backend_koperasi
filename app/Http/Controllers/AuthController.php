<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Pengurus;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
 public function login(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string',
            'password' => 'required|string',
        ]);

        $nomor = $request->input('nomor');
        $password = $request->input('password');

        $matches = [];

        $admin = Admin::where('nomor_admin', $nomor)->first();
        if ($admin) {
            $matches[] = ['role' => 'admin', 'user' => $admin];
        }

        $pengurus = Pengurus::where('nomor_pengurus', $nomor)->first();
        if ($pengurus) {
            $matches[] = ['role' => 'pengurus', 'user' => $pengurus];
        }

        $nasabah = Nasabah::where('nomor_nasabah', $nomor)->first();
        if ($nasabah) {
            $matches[] = ['role' => 'nasabah', 'user' => $nasabah];
        }

        if (count($matches) === 0) {
            return response()->json([
                'message' => 'Nomor tidak ditemukan',
            ], 404);
        }

        if (count($matches) > 1) {
            return response()->json([
                'message' => 'Nomor terdeteksi di lebih dari satu akun. Hubungi admin untuk perbaikan data.',
            ], 409);
        }

        $role = $matches[0]['role'];
        $user = $matches[0]['user'];

        if ($user->password === null) {
            return response()->json([
                'message' => 'Silakan daftar ulang terlebih dahulu',
            ], 403);
        }

        if ($role === 'pengurus' && ($user->status_akun ?? null) !== 'Aktif') {
            return response()->json([
                'message' => 'Akun tidak aktif',
            ], 403);
        }

        if ($role === 'nasabah' && ($user->status ?? null) !== 'Aktif') {
            return response()->json([
                'message' => 'Akun tidak aktif',
            ], 403);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Password salah',
            ], 422);
        }

        $tokenName = 'auth_token';
        $abilities = ['role:' . $role];
        $token = $user->createToken($tokenName, $abilities)->plainTextToken;

        return response()->json([
            'role' => $role,
            'token' => $token,
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

public function daftarUlangNasabah(Request $request)
{
    $request->validate([
        'nomor_nasabah' => 'required|exists:nasabah,nomor_nasabah',
        'password' => 'required|min:6|confirmed',
        'pin' => 'required|digits:6|confirmed',
    ]);

    $nasabah = Nasabah::where('nomor_nasabah', $request->nomor_nasabah)->first();

    if (!$nasabah) {
        return response()->json([
            'message' => 'Nasabah tidak ditemukan'
        ], 404);
    }

    if ($nasabah->password !== null) {
        return response()->json([
            'message' => 'Akun sudah aktif, silakan login'
        ], 422);
    }

    $nasabah->password = Hash::make($request->password);
    $nasabah->pin = $request->pin; 

    $nasabah->save();

    return response()->json([
        'message' => 'Daftar ulang berhasil.'
    ], 200);
}

}
