<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lifestyle_assessments', function (Blueprint $table) {
            $table->json('dietary_history')->nullable()->after('exercise_frequency');
        });
    }

    public function down(): void
    {
        Schema::table('lifestyle_assessments', function (Blueprint $table) {
            $table->dropColumn('dietary_history');
        });
    }
};
