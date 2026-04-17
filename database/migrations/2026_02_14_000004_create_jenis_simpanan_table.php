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
        Schema::create('jenis_simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_simpanan');
            $table->decimal('saldo_minimal', 15, 2);
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->string('dibuat_oleh')->nullable();
            $table->string('diubah_oleh')->nullable();
            $table->timestamp('waktu_diubah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_simpanan');
    }
};
