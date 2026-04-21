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
        Schema::create('pengurus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap')->nullable();
            $table->string('foto_profil')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('nomor_pengurus')->unique();
            $table->string('nomor_handphone')->unique()->nullable();
            $table->string('password')->nullable();
            $table->enum('status_akun', ['Aktif', 'Non-Aktif', 'Proses'])->nullable()->default(null);
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->string('dibuat_oleh')->nullable();
            $table->timestamp('waktu_diaktifkan')->nullable();
            $table->timestamp('waktu_daftar_ulang')->nullable();  
            $table->string('diaktifkan_oleh')->nullable();
            $table->timestamp('waktu_dinonaktifkan')->nullable();
            $table->string('dinonaktifkan_oleh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
