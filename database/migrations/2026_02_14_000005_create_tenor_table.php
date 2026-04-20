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
        Schema::create('tenor', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['Anggota', 'Non-Anggota']);
            $table->integer('lama_angsuran');
            $table->decimal('bunga', 5,2);
            $table->decimal('bunga_keterlambatan', 5,2);
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->string('dibuat_oleh')->nullable();
            $table->timestamp('waktu_diubah')->nullable();
            $table->string('diubah_oleh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenor');
    }
};
