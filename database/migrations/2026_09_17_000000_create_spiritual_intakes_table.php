<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spiritual_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique('patient_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spiritual_intakes');
    }
};
