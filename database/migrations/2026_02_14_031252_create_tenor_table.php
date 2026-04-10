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
            $table->integer('tenor');
            $table->enum('tipe', ['Anggota', 'Non-Anggota'])->nullable();
            $table->decimal('bunga', 5,2);
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updated_by')->nullable()->useCurrentOnUpdate();
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
