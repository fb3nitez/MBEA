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
        Schema::create('consultation_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->string('type', 20)->default('Initial');
            $table->text('notes')->nullable();
        });

        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->text('chief_complaint');
            $table->text('diagnosis');
            $table->text('clinical_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_schedule');
        Schema::dropIfExists('medical_records');
    }
};
