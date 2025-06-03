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
        Schema::table('responses', function (Blueprint $table) {
            $table->foreignId('mk_id')->nullable()->after('question_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('dosen_id')->nullable()->after('mk_id')->constrained('dosens')->onDelete('cascade');
        });
        // make sure to set mk_id and dosen_id not nullable manually after the migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropForeign(['mk_id']);
            $table->dropForeign(['dosen_id']);
            $table->dropColumn(['mk_id', 'dosen_id']);
        });
    }
};
