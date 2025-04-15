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
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('survey_id')->constrained('surveys');
            $table->foreignId('question_id')->constrained('survey_questions');
            $table->integer('nilai')->nullable();
            $table->text('kritik_saran')->nullable();
            $table->timestamps();
        });

        Schema::create('final_scores', function (Blueprint $table) {
            $table->id('id_final_scores');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->float('final_score');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // remove table ('survey_results') (incorrect db schema)
        Schema::dropIfExists('final_scores');
        Schema::dropIfExists('responses');
    }
};
