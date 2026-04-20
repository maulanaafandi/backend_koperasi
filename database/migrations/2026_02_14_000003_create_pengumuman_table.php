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
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('foto')->nullable();
            $table->string('deskripsi');
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
        Schema::dropIfExists('pengumuman');
    }
};
