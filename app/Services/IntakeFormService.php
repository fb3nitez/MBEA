<?php

namespace App\Services;

use App\Models\PatientRecord;
use App\Models\MedicalHistory;
use App\Models\PsychiatricHistory;
use App\Models\LifestyleAssessment;
use Illuminate\Support\Facades\DB;

class IntakeFormService
{
    /**
     * Submit the intake form data
     */
    public function submit(array $data): PatientRecord
    {
        return DB::transaction(function () use ($data) {
            // Clean and prepare data
            $cleanedData = $this->cleanData($data);

            // Create records
            $patient = $this->createPatientRecord($cleanedData);
            $this->createMedicalHistory($patient, $cleanedData);
            $this->createPsychiatricHistory($patient, $cleanedData);
            $this->createLifestyleAssessment($patient, $cleanedData);

            return $patient;
        });
    }

    /**
     * Clean and normalize data before processing
     */
    private function cleanData(array $data): array
    {
        // Convert checkbox values from 'on' or string to boolean
        $booleanFields = [
            'pmhHypertension', 'pmhStroke', 'pmhTuberculosis', 'pmhThyroid',
            'pmhDiabetes', 'pmhChronicPain', 'pmhAsthma', 'pmhEpilepsy',
            'pmhAutoimmune', 'pmhCancer', 'pmhOther',
            'fhHypertension', 'fhStroke', 'fhDiabetes', 'fhSubstance',
            'fhCancer', 'fhPsychiatric', 'fhOther',
            'traumaPhysical', 'traumaEmotional', 'traumaSexual', 'traumaNeglect',
            'tpChild', 'tpAdult', 'tpOngoing', 'tpPast',
            'teChild', 'teAdult', 'teOngoing', 'tePast',
            'tsChild', 'tsAdult', 'tsOngoing', 'tsPast',
            'tnChild', 'tnAdult', 'tnOngoing', 'tnPast',
            'subNicotine', 'subAlcohol', 'subRecreational', 'subMarijuana',
            'subScreentime', 'subGambling', 'subOthers',
        ];

        foreach ($booleanFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->toBoolean($data[$field]);
            }
        }

        // Convert concern levels to integers
        $concernFields = [
            'subNicotineConcern', 'subAlcoholConcern', 'subRecreationalConcern',
            'subMarijuanaConcern', 'subScreentimeConcern', 'subGamblingConcern',
            'subOthersConcern'
        ];

        foreach ($concernFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $data[$field] = (int) $data[$field];
            } elseif (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = 0;
            }
        }

        // Convert numeric fields
        if (isset($data['healthScore']) && $data['healthScore'] !== '') {
            $data['healthScore'] = (int) $data['healthScore'];
        }

        if (isset($data['sleepHours']) && $data['sleepHours'] !== '') {
            $data['sleepHours'] = (int) $data['sleepHours'];
        }

        if (isset($data['hospTimes']) && $data['hospTimes'] !== '') {
            $data['hospTimes'] = (int) $data['hospTimes'];
        }

        return $data;
    }

    /**
     * Convert various input formats to boolean
     */
    private function toBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower($value);
            return in_array($value, ['on', 'true', '1', 'yes']);
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return false;
    }

    /**
     * Create patient record
     */
    private function createPatientRecord(array $data): PatientRecord
    {
        $gender = $data['gender'] ?? null;
        if ($gender === 'Other' && isset($data['gender_other'])) {
            $gender = $data['gender_other'];
        }

        return PatientRecord::create([
            'fullname' => $data['name'] ?? '',
            'birthday' => $data['birthday'] ?? null,
            'religion' => $data['religion'] ?? null,
            'sex' => $data['sex'] ?? null,
            'gender' => $gender,
            'marital_status' => $data['maritalStatus'],
            'student_year_level' => $data['yearLevel'] ?? null,
            'course' => $data['course'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'chief_complaint' => $data['chiefComplaint'] ?? '',
            'primary_diagnosis' => $data['primaryDiagnosis'] ?? null,
        ]);
    }

    /**
     * Create medical history record
     */
    private function createMedicalHistory(PatientRecord $patientRecord, array $data): void
    {
        MedicalHistory::create([
            'patient_record_id' => $patientRecord->id,

            // Personal Medical History
            'hypertension' => $data['pmhHypertension'] ?? false,
            'stroke_tia' => $data['pmhStroke'] ?? false,
            'diabetes' => $data['pmhDiabetes'] ?? false,
            'bronchial_asthma' => $data['pmhAsthma'] ?? false,
            'tuberculosis' => $data['pmhTuberculosis'] ?? false,
            'thyroid_disorders' => $data['pmhThyroid'] ?? false,
            'chronic_pain_fibromyalgia' => $data['pmhChronicPain'] ?? false,
            'epilepsy_seizure' => $data['pmhEpilepsy'] ?? false,
            'autoimmune_disease' => $data['pmhAutoimmune'] ?? false,
            'autoimmune_specify' => $data['pmhAutoimmuneSpecify'] ?? null,
            'cancer' => $data['pmhCancer'] ?? false,
            'cancer_specify' => $data['pmhCancerSpecify'] ?? null,
            'other_medical' => $data['pmhOther'] ?? false,
            'other_medical_specify' => $data['pmhOtherSpecify'] ?? null,
            'current_medications' => $data['currentMedications'] ?? null,

            // Family Medical History
            'family_hypertension' => $data['fhHypertension'] ?? false,
            'family_stroke' => $data['fhStroke'] ?? false,
            'family_diabetes' => $data['fhDiabetes'] ?? false,
            'family_substance_use' => $data['fhSubstance'] ?? false,
            'family_cancer' => $data['fhCancer'] ?? false,
            'family_cancer_type' => $data['fhCancerType'] ?? null,
            'family_cancer_relation' => $data['fhCancerRelation'] ?? null,
            'family_psychiatric_disorder' => $data['fhPsychiatric'] ?? false,
            'family_psychiatric_relation' => $data['fhPsychiatricRelation'] ?? null,
            'family_other' => $data['fhOther'] ?? false,
            'family_other_specify' => $data['fhOtherSpecify'] ?? null,
            'family_other_relation' => $data['fhOtherRelation'] ?? null,
        ]);
    }

    /**
     * Create psychiatric history record
     */
    private function createPsychiatricHistory(PatientRecord $patientRecord, array $data): void
    {
        PsychiatricHistory::create([
            'patient_record_id' => $patientRecord->id,

            // Past psychiatric diagnosis
            'diagnosed_mental_condition' => ($data['diagnosedMH'] ?? 'no') === 'yes',
            'mental_condition' => $data['diagnosisSpecify'] ?? null,
            'psychiatric_hospitalized' => ($data['hospitalized'] ?? 'no') === 'yes',
            'hospitalization_count' => $data['hospTimes'] ?? null,
            'hospitalization_when' => $data['hospWhen'] ?? null,

            // Physical abuse
            'physical_abuse' => $data['traumaPhysical'] ?? false,
            'physical_child' => $data['tpChild'] ?? false,
            'physical_adult' => $data['tpAdult'] ?? false,
            'physical_ongoing' => $data['tpOngoing'] ?? false,
            'physical_past' => $data['tpPast'] ?? false,
            'physical_notes' => $data['tpDetails'] ?? null,

            // Emotional abuse
            'emotional_abuse' => $data['traumaEmotional'] ?? false,
            'emotional_child' => $data['teChild'] ?? false,
            'emotional_adult' => $data['teAdult'] ?? false,
            'emotional_ongoing' => $data['teOngoing'] ?? false,
            'emotional_past' => $data['tePast'] ?? false,
            'emotional_notes' => $data['teDetails'] ?? null,

            // Sexual abuse
            'sexual_abuse' => $data['traumaSexual'] ?? false,
            'sexual_child' => $data['tsChild'] ?? false,
            'sexual_adult' => $data['tsAdult'] ?? false,
            'sexual_ongoing' => $data['tsOngoing'] ?? false,
            'sexual_past' => $data['tsPast'] ?? false,
            'sexual_notes' => $data['tsDetails'] ?? null,

            // Neglect
            'neglect' => $data['traumaNeglect'] ?? false,
            'neglect_child' => $data['tnChild'] ?? false,
            'neglect_adult' => $data['tnAdult'] ?? false,
            'neglect_ongoing' => $data['tnOngoing'] ?? false,
            'neglect_past' => $data['tnPast'] ?? false,
            'neglect_notes' => $data['tnDetails'] ?? null,
        ]);
    }

    /**
     * Create lifestyle assessment record
     */
    private function createLifestyleAssessment(PatientRecord $patientRecord, array $data): void
    {
        LifestyleAssessment::create([
            'patient_record_id' => $patientRecord->id,

            // General Health
            'health_score' => $data['healthScore'] ?? 5,
            'sleep_hours' => isset($data['sleepHours']) && $data['sleepHours'] !== '' ? (int) $data['sleepHours'] : null,
            'tired_frequency' => $data['tiredFrequency'] ?? null,
            'weight_perception' => $data['weightPerception'] ?? null,
            'fast_food_frequency' => $data['fastFood'] ?? null,
            'fruits_veg_servings' => $data['fruitsVeg'] ?? null,
            'exercise_frequency' => $data['exerciseFreq'] ?? null,

            // PHQ-9
            'phq_little_interest' => $data['phqLittleInterest'] ?? null,
            'phq_feeling_down' => $data['phqFeelingDown'] ?? null,
            'phq_trouble_sleeping' => $data['phqTroubleSleeping'] ?? null,
            'phq_feeling_tired' => $data['phqFeelingTired'] ?? null,
            'phq_poor_appetite' => $data['phqPoorAppetite'] ?? null,
            'phq_feeling_bad' => $data['phqFeelingBad'] ?? null,
            'phq_trouble_concentrating' => $data['phqTroubleConcentrating'] ?? null,
            'phq_moving_slow' => $data['phqMovingSlow'] ?? null,
            'phq_thoughts_hurting' => $data['phqThoughtsHurting'] ?? null,

            // Substances - Nicotine
            'sub_nicotine' => $data['subNicotine'] ?? false,
            'sub_nicotine_amount' => $data['subNicotineAmount'] ?? null,
            'sub_nicotine_concern' => $data['subNicotineConcern'] ?? 0,

            // Substances - Alcohol
            'sub_alcohol' => $data['subAlcohol'] ?? false,
            'sub_alcohol_amount' => $data['subAlcoholAmount'] ?? null,
            'sub_alcohol_concern' => $data['subAlcoholConcern'] ?? 0,

            // Substances - Recreational
            'sub_recreational' => $data['subRecreational'] ?? false,
            'sub_recreational_amount' => $data['subRecreationalAmount'] ?? null,
            'sub_recreational_concern' => $data['subRecreationalConcern'] ?? 0,

            // Substances - Marijuana
            'sub_marijuana' => $data['subMarijuana'] ?? false,
            'sub_marijuana_amount' => $data['subMarijuanaAmount'] ?? null,
            'sub_marijuana_concern' => $data['subMarijuanaConcern'] ?? 0,

            // Substances - Screen Time
            'sub_screentime' => $data['subScreentime'] ?? false,
            'sub_screentime_amount' => $data['subScreentimeAmount'] ?? null,
            'sub_screentime_concern' => $data['subScreentimeConcern'] ?? 0,

            // Substances - Gambling
            'sub_gambling' => $data['subGambling'] ?? false,
            'sub_gambling_amount' => $data['subGamblingAmount'] ?? null,
            'sub_gambling_concern' => $data['subGamblingConcern'] ?? 0,

            // Substances - Others
            'sub_others' => $data['subOthers'] ?? false,
            'sub_others_specify' => $data['subOthersSpecify'] ?? null,
            'sub_others_concern' => $data['subOthersConcern'] ?? 0,

            // Motivation
            'lifestyle_motivation' => $data['lifestyleMotivation'] ?? null,
            'motivation_level' => $data['motivationLevel'] ?? null,
        ]);
    }
}
