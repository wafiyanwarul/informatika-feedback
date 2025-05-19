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
        // Update table dosens (no change)

        // Update table mata_kuliahs
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
            $table->integer('sks')->after('nama_mk');
        });

        // Create pivot table mata_kuliah_dosens
        Schema::create('mata_kuliah_dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->foreignId('dosen_id')->constrained('dosens');
            $table->dropColumn('sks');
        });

        Schema::dropIfExists('mata_kuliah_dosens');
    }
};
