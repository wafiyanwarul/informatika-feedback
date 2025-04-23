<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['created_by']); // Hapus foreign key constraint
            $table->dropColumn('created_by');    // Hapus kolom
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->foreignId('created_by')->constrained('users');
        });
    }
};
