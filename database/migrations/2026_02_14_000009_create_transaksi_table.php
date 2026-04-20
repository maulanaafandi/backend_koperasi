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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nasabah')
                  ->constrained('nasabah')
                  ->restrictOnDelete();
            $table->string('kode_transaksi')->unique();
            $table->enum('jenis_transaksi', ['setor_tunai', 'tarik_tunai', 'transfer_masuk', 'transfer_keluar']);
            $table->decimal('saldo', 15, 2)->default(0); 
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->enum('status_transaksi', ['sukses',  'gagal',  'pending', 'dibatalkan'])->default('pending'); 
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->string('dibuat_oleh')->nullable();
            $table->timestamp('waktu_transaksi_sukses')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
