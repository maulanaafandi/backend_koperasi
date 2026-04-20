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
            $table->foreignId('id_nasabah')
                  ->constrained('nasabah')
                  ->restrictOnDelete();
            $table->foreignId('id_transaksi')->nullable()
                  ->constrained('transaksi')
                  ->restrictOnDelete();
            $table->foreignId('id_tenor')
                  ->constrained('tenor')
                  ->restrictOnDelete();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->string('jaminan');
            $table->string('foto_jaminan');
            $table->string('nilai_jaminan');
            $table->enum('status', ['Disetujui','Tidak Disetujui','Proses']);
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->string('dibuat_oleh')->nullable();
            $table->timestamp('waktu_disetujui')->nullable();
            $table->string('disetujui_oleh')->nullable();
            $table->timestamp('waktu_tidak_setujui')->nullable();
            $table->string('tidak_setujui_oleh')->nullable();
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
