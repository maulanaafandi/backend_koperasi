<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $adminNumber = 'ADM-001';
        $pengurusNumber = 'PGR-001';
        $nasabahNumber = 'NSB-001';
        $nasabahIdCard = '3201010101010001';
        $transaksiSetorCode = 'TRX-SETOR-0001';
        $transaksiTarikCode = 'TRX-TARIK-0001';
        $rekeningNumber = '8800100001';

        DB::table('jenis_simpanan')->updateOrInsert(
            ['nama_simpanan' => 'Simpanan Pokok'],
            [
                'saldo_minimal' => 100000,
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $adminNumber,
                'diubah_oleh' => $adminNumber,
                'waktu_diubah' => $now,
            ]
        );

        $jenisSimpananId = DB::table('jenis_simpanan')
            ->where('nama_simpanan', 'Simpanan Pokok')
            ->value('id');

        DB::table('tenor')->updateOrInsert(
            ['tipe' => 'Anggota', 'lama_angsuran' => 12],
            [
                'bunga' => 1.50,
                'bunga_keterlambatan' => 0.50,
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $adminNumber,
                'waktu_diubah' => $now,
                'diubah_oleh' => $adminNumber,
            ]
        );

        $tenorId = DB::table('tenor')
            ->where('tipe', 'Anggota')
            ->where('lama_angsuran', 12)
            ->value('id');

        DB::table('pengurus')->updateOrInsert(
            ['nomor_pengurus' => $pengurusNumber],
            [
                'nama_lengkap' => 'Pengurus Demo',
                'foto_profil' => null,
                'jenis_kelamin' => 'L',
                'nomor_handphone' => '081234567801',
                'password' => Hash::make(env('PASSWORD_PENGURUS', 'password')),
                'status_akun' => 'Aktif',
                'waktu_dibuat' => $now,
                'waktu_diaktifkan' => $now,
                'waktu_daftar_ulang' => null,
                'diaktifkan_oleh' => $adminNumber,
                'waktu_dinonaktifkan' => null,
                'dinonaktifkan_oleh' => null,
            ]
        );

        DB::table('nasabah')->updateOrInsert(
            ['nomor_nasabah' => $nasabahNumber],
            [
                'id_jenis_simpanan' => $jenisSimpananId,
                'nama_lengkap' => 'Nasabah Demo',
                'foto_profil' => null,
                'nomor_induk_kependudukan' => $nasabahIdCard,
                'nama_ibu_kandung' => 'Ibu Demo',
                'tanggal_lahir' => '1995-01-01',
                'tempat_lahir' => 'Bandung',
                'status_perkawinan' => 'Belum Kawin',
                'jenis_kelamin' => 'L',
                'alamat_ktp' => 'Jl. Demo No. 1',
                'RT' => '001',
                'RW' => '002',
                'jenis_pekerjaan' => 'Karyawan',
                'gaji_pekerjaan' => 5000000,
                'status' => 'Aktif',
                'nomor_handphone' => '081234567890',
                'email' => 'nasabah.demo@example.com',
                'password' => Hash::make(env('PASSWORD_NASABAH', 'password')),
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $adminNumber,
                'waktu_diaktifkan' => $now,
                'diaktifkan_oleh' => $adminNumber,
                'waktu_dinonaktifkan' => null,
                'dinonaktifkan_oleh' => null,
                'nomor_rekening' => $rekeningNumber,
                'tipe' => 'Anggota',
                'pin' => Hash::make('123456'),
                'saldo' => 1250000,
            ]
        );

        $nasabahId = DB::table('nasabah')
            ->where('nomor_nasabah', $nasabahNumber)
            ->value('id');

        DB::table('pengumuman')->updateOrInsert(
            ['judul' => 'Selamat Datang di Koperasi'],
            [
                'foto' => null,
                'deskripsi' => 'Data ini adalah data awal untuk integrasi frontend.',
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $adminNumber,
                'waktu_diubah' => $now,
                'diubah_oleh' => $adminNumber,
            ]
        );

        DB::table('transaksi')->updateOrInsert(
            ['kode_transaksi' => $transaksiSetorCode],
            [
                'id_nasabah' => $nasabahId,
                'jenis_transaksi' => 'setor_tunai',
                'saldo' => 500000,
                'saldo_sebelum' => 750000,
                'saldo_sesudah' => 1250000,
                'status_transaksi' => 'sukses',
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $pengurusNumber,
                'waktu_transaksi_sukses' => $now,
            ]
        );

        DB::table('transaksi')->updateOrInsert(
            ['kode_transaksi' => $transaksiTarikCode],
            [
                'id_nasabah' => $nasabahId,
                'jenis_transaksi' => 'tarik_tunai',
                'saldo' => 250000,
                'saldo_sebelum' => 1250000,
                'saldo_sesudah' => 1000000,
                'status_transaksi' => 'sukses',
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $pengurusNumber,
                'waktu_transaksi_sukses' => $now,
            ]
        );

        $transaksiSetorId = DB::table('transaksi')
            ->where('kode_transaksi', $transaksiSetorCode)
            ->value('id');

        $transaksiTarikId = DB::table('transaksi')
            ->where('kode_transaksi', $transaksiTarikCode)
            ->value('id');

        DB::table('simpanan')->updateOrInsert(
            ['id_transaksi' => $transaksiSetorId],
            [
                'jumlah_simpanan' => 500000,
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $pengurusNumber,
            ]
        );

        DB::table('penarikan')->updateOrInsert(
            ['id_transaksi' => $transaksiTarikId],
            [
                'jumlah_penarikan' => 250000,
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $pengurusNumber,
            ]
        );

        DB::table('pinjaman')->updateOrInsert(
            ['id_nasabah' => $nasabahId, 'id_tenor' => $tenorId],
            [
                'id_transaksi' => null,
                'jumlah_pinjaman' => 3000000,
                'jaminan' => 'BPKB Motor',
                'foto_jaminan' => 'jaminan-demo.jpg',
                'nilai_jaminan' => '7000000',
                'status' => 'Disetujui',
                'waktu_dibuat' => $now,
                'dibuat_oleh' => $pengurusNumber,
                'waktu_disetujui' => $now,
                'disetujui_oleh' => $adminNumber,
                'waktu_tidak_setujui' => null,
                'tidak_setujui_oleh' => null,
            ]
        );

        $pinjamanId = DB::table('pinjaman')
            ->where('id_nasabah', $nasabahId)
            ->where('id_tenor', $tenorId)
            ->value('id');

        DB::table('cicilan_pinjaman')->updateOrInsert(
            ['id_pinjaman' => $pinjamanId, 'nomor_angsuran' => 1],
            [
                'id_transaksi' => null,
                'tanggal_jatuh_tempo' => now()->addMonth()->toDateString(),
                'total_tagihan' => 270000,
                'tagihan_pokok' => 250000,
                'bunga' => 20000,
                'denda' => 0,
                'waktu_dibayar' => null,
                'status_angsuran' => 'belum_jatuh_tempo',
                'dibayar_oleh' => null,
            ]
        );
    }
}