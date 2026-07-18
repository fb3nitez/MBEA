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
        Schema::create('lifestyle_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('health_score')->nullable();
            $table->unsignedTinyInteger('sleep_hours')->nullable();
            $table->string('tired_frequency')->nullable();

            $table->string('weight_perception')->nullable();
            $table->string('fast_food_frequency')->nullable();
            $table->string('fruits_veg_servings')->nullable();

            $table->string('exercise_frequency')->nullable();

            $table->string('phq_little_interest')->nullable();
            $table->string('phq_feeling_down')->nullable();
            $table->string('phq_trouble_sleeping')->nullable();
            $table->string('phq_feeling_tired')->nullable();
            $table->string('phq_poor_appetite')->nullable();
            $table->string('phq_feeling_bad')->nullable();
            $table->string('phq_trouble_concentrating')->nullable();
            $table->string('phq_moving_slow')->nullable();
            $table->string('phq_thoughts_hurting')->nullable();

            $table->boolean('sub_nicotine')->default(false);
            $table->string('sub_nicotine_amount')->nullable();
            $table->unsignedTinyInteger('sub_nicotine_concern')->default(0);

            $table->boolean('sub_alcohol')->default(false);
            $table->string('sub_alcohol_amount')->nullable();
            $table->unsignedTinyInteger('sub_alcohol_concern')->default(0);

            $table->boolean('sub_recreational')->default(false);
            $table->string('sub_recreational_amount')->nullable();
            $table->unsignedTinyInteger('sub_recreational_concern')->default(0);

            $table->boolean('sub_marijuana')->default(false);
            $table->string('sub_marijuana_amount')->nullable();
            $table->unsignedTinyInteger('sub_marijuana_concern')->default(0);

            $table->boolean('sub_screentime')->default(false);
            $table->string('sub_screentime_amount')->nullable();
            $table->unsignedTinyInteger('sub_screentime_concern')->default(0);

            $table->boolean('sub_gambling')->default(false);
            $table->string('sub_gambling_amount')->nullable();
            $table->unsignedTinyInteger('sub_gambling_concern')->default(0);

            $table->boolean('sub_others')->default(false);
            $table->string('sub_others_specify')->nullable();
            $table->unsignedTinyInteger('sub_others_concern')->default(0);

            $table->longText('lifestyle_motivation')->nullable();
            $table->string('motivation_level')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lifestyle_assessments');
    }
};
