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

        Schema::create('survey_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys');
            $table->foreignId('user_id')->constrained('users');
            $table->float('total_nilai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_results');
        Schema::dropIfExists('responses');
    }
};
