<?php

namespace App\Services;

use App\Models\PatientRecord;
use App\Models\MedicalHistory;
use App\Models\PsychiatricHistory;
use App\Models\LifestyleAssessment;
use Illuminate\Support\Facades\DB;

class IntakeFormService
{
    public function submit(array $data)
    {
        return DB::transaction(function () use ($data) {

            $patient = $this->createPatientRecord($data);

            $this->createMedicalHistory($patient, $data);

            $this->createPsychiatricHistory($patient, $data);

            $this->createLifestyleAssessment($patient, $data);

            return $patient;
        });
    }

    private function createPatientRecord(array $data)
    {
        return PatientRecord::create([
            'fullname' => $data['name'],
            'birthday' => $data['birthday'],
            'religion' => $data['religion'],
            'sex' => $data['sex'],
            'gender' => $data['gender'],
            'marital_status' => $data['maritalStatus'],
            'student_year_level' => $data['yearLevel'],
            'course' => $data['course'],
            'occupation' => $data['occupation'],
            'cheif_complaint' => $data['chiefComplaint'],
            'primary_diagnosis' => $data['primaryDiagnosis'],
        ]);
    }

    private function createMedicalHistory(PatientRecord $patientRecord, array $data)
    {
        MedicalHistory::create([
            'patient_record_id' => $patientRecord->id,
            'hypertension' => $data['pmhHypertension'] ?? false,
            'stroke_tia' => $data['pmhStroke'] ?? false,
            'diabetes' => $data['pmhDiabetes'] ?? false,
            'bronchial_asthma' => $data['pmhAsthma'] ?? false,
            'tuberculosis' => $data['pmhTuberculosis'] ?? false,
            'thyroid_disorders' => $data['pmhThyroid'] ?? false,
            'chronic_pain_fibromyalgia' => $data['pmhChronicPain'] ?? false,
            'epilepsy_seizure' => $data['pmhEpilepsy'] ?? false,
            'autoimmune_disease' => $data['pmhAutoimmune'] ?? false,
            'autoimmune_specify' => $data['pmhAutoimmuneSpecify'],
            'cancer' => $data['pmhCancer'] ?? false,
            'cancer_specify' => $data['pmhCancerSpecify'],
            'other_medical' => $data['pmhOther'] ?? false,
            'other_medical_specify' => $data['pmhOtherSpecify'],
            'current_medications' => $data['currentMedications'],
            'family_hypertension' => $data['fhHypertension'] ?? false,
            'family_stroke' => $data['fhStroke'] ?? false,
            'family_diabetes' => $data['fhDiabetes'] ?? false,
            'family_cancer' => $data['fhCancer'] ?? false,
            'family_cancer_type' => $data['fhCancerType'],
            'family_cancer_relation' => $data['fhCancerRelation'],
            'family_psychiatric_disorder' => $data['fhPsychiatric'] ?? false,
            'family_psychiatric_relation' => $data['fhPsychiatricRelation'],
            'family_substance_use' => $data['fhSubstance'] ?? false,
            'family_other' => $data['fhOther'] ?? false,
            'family_other_specify' => $data['fhOtherSpecify'],
            'family_other_relation' => $data['fhOtherRelation'],
        ]);
    }

    private function createPsychiatricHistory(PatientRecord $patientRecord, array $data)
    {
        PsychiatricHistory::create([
            'patient_record_id' => $patientRecord->id,
            'diagnosed_mental_condition' => $data['diagnosedMH'] === 'yes',
            'mental_condition' => $data['diagnosisSpecify'],
            'psychiatric_hospitalized' => $data['hospitalized'] === 'yes',
            'hospitalization_count' => $data['hospTimes'] ? (int) $data['hospTimes'] : null,
            'hospitalization_when' => $data['hospWhen'],
            'physical_abuse' => $data['traumaPhysical'] ?? false,
            'physical_child' => $data['tpChild'] ?? false,
            'physical_adult' => $data['tpAdult'] ?? false,
            'physical_ongoing' => $data['tpOngoing'] ?? false,
            'physical_past' => $data['tpPast'] ?? false,
            'physical_notes' => $data['tpDetails'],
            'emotional_abuse' => $data['traumaEmotional'] ?? false,
            'emotional_child' => $data['teChild'] ?? false,
            'emotional_adult' => $data['teAdult'] ?? false,
            'emotional_ongoing' => $data['teOngoing'] ?? false,
            'emotional_past' => $data['tePast'] ?? false,
            'emotional_notes' => $data['teDetails'],
            'sexual_abuse' => $data['traumaSexual'] ?? false,
            'sexual_child' => $data['tsChild'] ?? false,
            'sexual_adult' => $data['tsAdult'] ?? false,
            'sexual_ongoing' => $data['tsOngoing'] ?? false,
            'sexual_past' => $data['tsPast'] ?? false,
            'sexual_notes' => $data['tsDetails'],
            'neglect' => $data['traumaNeglect'] ?? false,
            'neglect_child' => $data['tnChild'] ?? false,
            'neglect_adult' => $data['tnAdult'] ?? false,
            'neglect_ongoing' => $data['tnOngoing'] ?? false,
            'neglect_past' => $data['tnPast'] ?? false,
            'neglect_notes' => $data['tnDetails'],
        ]);
    }

    private function createLifestyleAssessment(PatientRecord $patientRecord, array $data)
    {
        LifestyleAssessment::create([
            'patient_record_id' => $patientRecord->id,
            'health_score' => $data['healthScore'],
            'sleep_hours' => $data['sleepHours'] ? (int) $data['sleepHours'] : null,
            'tired_frequency' => $data['tiredFrequency'],
            'weight_perception' => $data['weightPerception'],
            'fast_food_frequency' => $data['fastFood'],
            'fruits_veg_servings' => $data['fruitsVeg'],
            'exercise_frequency' => $data['exerciseFreq'],
            'phq_little_interest' => $data['phqLittleInterest'],
            'phq_feeling_down' => $data['phqFeelingDown'],
            'phq_trouble_sleeping' => $data['phqTroubleSleeping'],
            'phq_feeling_tired' => $data['phqFeelingTired'],
            'phq_poor_appetite' => $data['phqPoorAppetite'],
            'phq_feeling_bad' => $data['phqFeelingBad'],
            'phq_trouble_concentrating' => $data['phqTroubleConcentrating'],
            'phq_moving_slow' => $data['phqMovingSlow'],
            'phq_thoughts_hurting' => $data['phqThoughtsHurting'],
            'sub_nicotine' => $data['subNicotine'] ?? false,
            'sub_nicotine_amount' => $data['subNicotineAmount'],
            'sub_nicotine_concern' => $data['subNicotineConcern'] ?? 0,
            'sub_alcohol' => $data['subAlcohol'] ?? false,
            'sub_alcohol_amount' => $data['subAlcoholAmount'],
            'sub_alcohol_concern' => $data['subAlcoholConcern'] ?? 0,
            'sub_recreational' => $data['subRecreational'] ?? false,
            'sub_recreational_amount' => $data['subRecreationalAmount'],
            'sub_recreational_concern' => $data['subRecreationalConcern'] ?? 0,
            'sub_marijuana' => $data['subMarijuana'] ?? false,
            'sub_marijuana_amount' => $data['subMarijuanaAmount'],
            'sub_marijuana_concern' => $data['subMarijuanaConcern'] ?? 0,
            'sub_screentime' => $data['subScreentime'] ?? false,
            'sub_screentime_amount' => $data['subScreentimeAmount'],
            'sub_screentime_concern' => $data['subScreentimeConcern'] ?? 0,
            'sub_gambling' => $data['subGambling'] ?? false,
            'sub_gambling_amount' => $data['subGamblingAmount'],
            'sub_gambling_concern' => $data['subGamblingConcern'] ?? 0,
            'sub_others' => $data['subOthers'] ?? false,
            'sub_others_specify' => $data['subOthersSpecify'],
            'sub_others_concern' => $data['subOthersConcern'] ?? 0,
            'lifestyle_motivation' => $data['lifestyleMotivation'],
            'motivation_level' => $data['motivationLevel'],
        ]);
    }
}
