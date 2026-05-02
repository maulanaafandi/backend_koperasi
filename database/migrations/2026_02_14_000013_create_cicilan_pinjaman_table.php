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
        Schema::create('cicilan_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pinjaman')
                  ->constrained('pinjaman')
                  ->restrictOnDelete();
            $table->foreignId('id_transaksi')
                  ->nullable()
                  ->constrained('transaksi')
                  ->restrictOnDelete();    
            $table->integer('nomor_angsuran');
            $table->string('kode_cicilan_pinjaman')->unique();
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('total_tagihan', 15, 2);
            $table->decimal('tagihan_pokok', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->decimal('denda', 15, 2);
            $table->timestamp('waktu_dibayar')->nullable();
            $table->enum('status_angsuran', [ 'belum_jatuh_tempo', 'jatuh_tempo', 'lunas','macet' ])->default('belum_jatuh_tempo');
            $table->string('dibayar_oleh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cicilan_pinjaman');
    }
};
