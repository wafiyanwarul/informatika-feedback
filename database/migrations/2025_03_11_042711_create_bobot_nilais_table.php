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
        Schema::create('bobot_nilais', function (Blueprint $table) {
            $table->id();
            $table->string('deskripsi', 50);
            $table->integer('skor')->check('skor BETWEEN 1 AND 5');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_nilais');
    }
};
