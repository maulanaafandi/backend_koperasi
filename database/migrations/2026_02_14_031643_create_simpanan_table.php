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
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anggota')
                  ->constrained('anggota')
                  ->restrictOnDelete();
            $table->foreignId('id_jenis_simpanan')
                  ->constrained('jenis_simpanan')
                  ->restrictOnDelete();
            $table->foreignId('id_transaksi')
                  ->constrained('transaksi')
                  ->restrictOnDelete();
            $table->decimal('jumlah_simpanan', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan');
    }
};
