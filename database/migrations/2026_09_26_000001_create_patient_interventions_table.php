<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->text('psychiatric_therapy_medication')->nullable();
            $table->text('lifestyle_interventions')->nullable();
            $table->text('substance_use_rehabilitation')->nullable();
            $table->text('spiritual_counseling')->nullable();
            $table->timestamps();

            $table->unique('patient_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_interventions');
    }
};
