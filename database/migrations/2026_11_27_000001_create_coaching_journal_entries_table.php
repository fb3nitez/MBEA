<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coaching_journal_entries')) {
            Schema::table('coaching_journal_entries', function (Blueprint $table) {
                $table->index(
                    ['patient_record_id', 'life_coach_id', 'kind'],
                    'journal_patient_coach_kind_idx'
                );
            });

            return;
        }

        Schema::create('coaching_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_coach_id')->constrained('users')->cascadeOnDelete();
            $table->string('kind', 20);
            $table->string('cadence', 30)->nullable();
            $table->date('entry_date');
            $table->json('content');
            $table->timestamps();

            $table->index(['patient_record_id', 'life_coach_id', 'kind'], 'journal_patient_coach_kind_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_journal_entries');
    }
};
