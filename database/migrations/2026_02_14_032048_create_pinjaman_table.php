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
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anggota')
                  ->constrained('anggota')
                  ->restrictOnDelete();
            $table->foreignId('id_transaksi')
                  ->constrained('transaksi')
                  ->restrictOnDelete();
            $table->foreignId('id_tenor')
                  ->constrained('tenor')
                  ->restrictOnDelete();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
             $table->enum('status', [
                'Lunas',
                'Jatuh Tempo',
                'Macet',
                'Proses'
            ])->default('Proses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
