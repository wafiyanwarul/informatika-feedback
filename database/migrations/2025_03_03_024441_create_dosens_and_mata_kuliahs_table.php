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
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dosen', 100);
            $table->string('email', 100)->unique();
            $table->string('foto_profil')->nullable();
            $table->timestamps();
        });

        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mk', 100);
            $table->foreignId('dosen_id')->constrained('dosens');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliahs');
        Schema::dropIfExists('dosens');
    }
};
