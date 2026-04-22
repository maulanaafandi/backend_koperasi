<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jenis_simpanan')
                   ->nullable()
                  ->constrained('jenis_simpanan')
                  ->restrictOnDelete();
            $table->string('nama_lengkap')->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('nomor_induk_kependudukan', 16)->nullable()->unique();
            $table->string('nama_ibu_kandung')->nullable(); 
            $table->date('tanggal_lahir')->nullable(); 
            $table->string('tempat_lahir')->nullable(); 
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai'])->nullable()->default(null); 
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('alamat_ktp')->nullable(); 
            $table->string('RT', 3)->nullable();
            $table->string('RW', 3)->nullable();
            $table->string('jenis_pekerjaan')->nullable(); 
            $table->decimal('gaji_pekerjaan', 15, 2)->nullable();
            $table->enum('status', ['Aktif', 'Non-Aktif']);
            $table->string('nomor_nasabah')->unique();
            $table->string('nomor_handphone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->string('dibuat_oleh')->nullable();
            $table->timestamp('waktu_diaktifkan')->nullable(); 
            $table->string('diaktifkan_oleh')->nullable();
            $table->timestamp('waktu_dinonaktifkan')->nullable();
            $table->string('dinonaktifkan_oleh')->nullable();
            $table->string('nomor_rekening')->unique();
            $table->enum('tipe', ['Non-Anggota', 'Anggota']);
            $table->string('pin')->nullable();
            $table->decimal('saldo', 15, 2)->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};
