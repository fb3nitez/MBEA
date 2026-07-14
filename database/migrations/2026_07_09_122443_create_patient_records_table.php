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
        Schema::create('patient_records', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->date('birthday');
            $table->string('religion')->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->string('gender')->nullable();
            $table->enum('marital_status', ['single', 'married', 'annulled', 'widowed', 'separated']);
            $table->string('student_year_level')->nullable();
            $table->string('course')->nullable();
            $table->string('occupation')->nullable();
            $table->text('cheif_complaint')->nullable();
            $table->string('primary_diagnosis')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();

            /* Personal Medical History */
            $table->boolean('hypertension')->default(false);
            $table->boolean('stroke_tia')->default(false);
            $table->boolean('diabetes')->default(false);
            $table->boolean('bronchial_asthma')->default(false);
            $table->boolean('tuberculosis')->default(false);
            $table->boolean('thyroid_disorders')->default(false);
            $table->boolean('chronic_pain_fibromyalgia')->default(false);
            $table->boolean('epilepsy_seizure')->default(false);

            $table->boolean('autoimmune_disease')->default(false);
            $table->string('autoimmune_specify')->nullable();

            $table->boolean('cancer')->default(false);
            $table->string('cancer_specify')->nullable();

            $table->boolean('other_medical')->default(false);
            $table->string('other_medical_specify')->nullable();

            $table->longText('current_medications')->nullable();

            /* Family History */

            $table->boolean('family_hypertension')->default(false);
            $table->string('family_hypertension_relation')->nullable();

            $table->boolean('family_stroke')->default(false);
            $table->string('family_stroke_relation')->nullable();

            $table->boolean('family_diabetes')->default(false);
            $table->string('family_diabetes_relation')->nullable();

            $table->boolean('family_cancer')->default(false);
            $table->string('family_cancer_type')->nullable();
            $table->string('family_cancer_relation')->nullable();

            $table->boolean('family_psychiatric_disorder')->default(false);
            $table->string('family_psychiatric_relation')->nullable();

            $table->boolean('family_substance_use')->default(false);
            $table->string('family_substance_relation')->nullable();

            $table->boolean('family_other')->default(false);
            $table->string('family_other_specify')->nullable();
            $table->string('family_other_relation')->nullable();

            $table->timestamps();
        });

        Schema::create('psychiatric_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->constrained()->cascadeOnDelete();

            /* Past psychiatric diagnosis */

            $table->boolean('diagnosed_mental_condition')->default(false);

            $table->string('mental_condition')->nullable();

            $table->boolean('psychiatric_hospitalized')->default(false);

            $table->integer('hospitalization_count')->nullable();

            $table->string('hospitalization_when')->nullable();

            /* Trauma and abuse */
            // Physical abuse
            $table->boolean('physical_abuse')->default(false);
            $table->boolean('physical_child')->default(false);
            $table->boolean('physical_adult')->default(false);
            $table->boolean('physical_ongoing')->default(false);
            $table->boolean('physical_past')->default(false);
            $table->text('physical_notes')->nullable();

            // Emotional abuse
            $table->boolean('emotional_abuse')->default(false);
            $table->boolean('emotional_child')->default(false);
            $table->boolean('emotional_adult')->default(false);
            $table->boolean('emotional_ongoing')->default(false);
            $table->boolean('emotional_past')->default(false);
            $table->text('emotional_notes')->nullable();

            // Sexual abuse
            $table->boolean('sexual_abuse')->default(false);
            $table->boolean('sexual_child')->default(false);
            $table->boolean('sexual_adult')->default(false);
            $table->boolean('sexual_ongoing')->default(false);
            $table->boolean('sexual_past')->default(false);
            $table->text('sexual_notes')->nullable();

            // Neglect
            $table->boolean('neglect')->default(false);
            $table->boolean('neglect_child')->default(false);
            $table->boolean('neglect_adult')->default(false);
            $table->boolean('neglect_ongoing')->default(false);
            $table->boolean('neglect_past')->default(false);
            $table->text('neglect_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_records');
        Schema::dropIfExists('medical_histories');
        Schema::dropIfExists('patient_information');
    }
};
