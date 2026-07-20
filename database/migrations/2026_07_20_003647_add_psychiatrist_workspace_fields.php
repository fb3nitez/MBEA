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
        Schema::table('patient_records', function (Blueprint $table) {
            $table->foreignId('life_coach_id')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('clinical_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('life_coach_id');
            $table->dropColumn('clinical_notes');
        });
    }
};
