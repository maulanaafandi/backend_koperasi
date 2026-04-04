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

    if (!Hash::check($request->password, $pengurus->password)) {
        return response()->json(['message' => 'Password salah'], 401);
    }

    if ($pengurus->status_akun !== 'Aktif') {
        return response()->json([
            'message' => 'Akun Anda sedang dalam status ' . $pengurus->status_akun . '. Silahkan hubungi admin.'
        ], 403);
    }

    $token = $pengurus->createToken('pengurus_token')->plainTextToken;

    return response()->json([
        'token' => $token,
    ], 200);
}

    public function registerAnggota(Request $request) {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required',
            'nomor_anggota' => 'required|unique:anggota',
            'nomor_handphone' => 'required|unique:anggota',
            'email' => 'required|email|unique:anggota',
            'password' => 'required|min:6',
            'pin' => 'required|min:6'
        ], [
            'nomor_handphone.unique' => 'Nomor hp yang terdaftar sudah ada',
            'nomor_anggota.unique' => 'Nomor anggota sudah digunakan',
            'email.unique' => 'Email sudah digunakan'
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $anggota = Anggota::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nomor_anggota' => $request->nomor_anggota,
            'email' => $request->email,
            'nomor_handphone' => $request->nomor_handphone,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'pin' => Hash::make($request->pin),
            'password' => Hash::make($request->password),
            'status_akun' => 'Proses'
        ]);

        return response()->json([
            'message' => 'Registrasi akun berhasil, silahkan hubungi pengurus untuk mengaktivasi akun',
            'data' => $anggota
        ], 201);
    }

    public function loginAnggota(Request $request) {
        $request->validate(['nomor_anggota' => 'required', 'password' => 'required']);
        $anggota = Anggota::where('nomor_anggota', $request->nomor_anggota)->first();

        if (!$anggota) {
            return response()->json(['message' => 'Akun belum terdaftar, silahkan melakukan registrasi akun terlebih dahulu'], 404);
        }

        if (!Hash::check($request->password, $anggota->password)) {
            return response()->json(['message' => 'Password salah'], 401);
        }

        if ($anggota->status_akun !== 'Aktif') {
            return response()->json(['message' => 'Akun Anda belum aktif, silahkan hubungi pengurus.'], 403);
        }

        $token = $anggota->createToken('anggota_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $anggota]);
    }

public function daftarUlangPengurus(Request $request)
{
$pengurus = Pengurus::where('nomor_pengurus', $request->nomor_pengurus)->first();
    
    $idPengurus = $pengurus ? $pengurus->id : null;

    $validator = Validator::make($request->all(), [
        'nomor_pengurus'        => 'required|exists:pengurus,nomor_pengurus',
        'nama_lengkap'          => 'required|string|max:255',
        'jenis_kelamin'         => 'required|in:L,P',
        'nomor_handphone' => 'required|string|max:12|unique:pengurus,nomor_handphone,' . $idPengurus,
        'foto_profil'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'password'              => [
            'required',
            'confirmed',       
            Password::min(6)
                ->letters()
                ->numbers()
                ->mixedCase()
        ],
    ], [
        'nomor_pengurus.exists'  => 'Nomor pengurus tidak terdaftar.',
        'nomor_handphone.unique' => 'Nomor handphone sudah digunakan.',
        'nomor_handphone.max'    => 'Nomor handphone tidak boleh lebih dari 12 digit.',
        'password.confirmed'     => 'Konfirmasi password tidak cocok.',
        'password'               => 'Kata sandi harus 6 karakter, mengandung huruf, angka, dan huruf kapital.'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $pengurus = Pengurus::where('nomor_pengurus', $request->nomor_pengurus)->first();

    if ($pengurus->status_akun === 'Aktif') {
        return response()->json(['message' => 'Akun ini sudah aktif, silakan login.'], 403);
    }

    $pathFoto = $pengurus->foto_profil;
    if ($request->hasFile('foto_profil')) {
        $pathFoto = $request->file('foto_profil')->store('foto_pengurus', 'public');
    }

    $pengurus->update([
        'nama_lengkap'    => $request->nama_lengkap,
        'foto_profil'     => $pathFoto,
        'jenis_kelamin'   => $request->jenis_kelamin,
        'nomor_handphone' => $request->nomor_handphone,
        'password'        => Hash::make($request->password), 
        'status_akun'     => 'Aktif',
    ]);

    return response()->json([
        'message' => 'Daftar ulang berhasil. Akun Anda kini aktif.',
    ], 200);
}
}
