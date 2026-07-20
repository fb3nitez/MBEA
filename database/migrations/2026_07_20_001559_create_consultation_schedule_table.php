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
            $table->string('status', 20)->default('Scheduled');
            $table->string('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->text('treatment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_schedule');
    }
};
