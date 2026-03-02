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
        Schema::create('penarikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anggota')
                  ->constrained('anggota')
                  ->onDelete('cascade');
            $table->foreignId('id_transaksi')
                  ->constrained('transaksi')
                  ->onDelete('cascade');
            $table->decimal('jumlah_penarikan', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penarikan');
    }
};
