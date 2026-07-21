<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_coach_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_type', 50)->default('Follow-up');
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('coaching_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_coach_id')->constrained('users')->cascadeOnDelete();
            $table->string('description');
            $table->string('priority', 20)->default('Medium');
            $table->date('due_date')->nullable();
            $table->boolean('is_done')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('coaching_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_coach_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->date('date');
            $table->time('time')->nullable();
            $table->timestamps();
        });

        Schema::create('coaching_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_coach_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 50)->default('Mental Wellness');
            $table->text('description')->nullable();
            $table->date('target_date')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_goals');
        Schema::dropIfExists('coaching_schedules');
        Schema::dropIfExists('coaching_tasks');
        Schema::dropIfExists('coaching_notes');
    }
};
