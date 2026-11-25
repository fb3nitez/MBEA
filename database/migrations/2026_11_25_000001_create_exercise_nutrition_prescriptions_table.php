<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_nutrition_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prescribed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20);
            $table->string('patient_name');
            $table->unsignedSmallInteger('patient_age')->nullable();
            $table->string('patient_sex', 40)->nullable();
            $table->date('prescription_date');
            $table->json('form_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_nutrition_prescriptions');
    }
};
