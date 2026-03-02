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
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('foto_profil')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat');
            $table->string('pin');
            $table->string('nomor_anggota')->unique();
            $table->string('nomor_handphone')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status_akun', ['Aktif', 'Non-Aktif', 'Proses'])
                  ->default('Proses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
