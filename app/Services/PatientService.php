<?php

namespace App\Services;

use App\Models\ConsultationSchedule;
use App\Models\LifestyleAssessment;
use App\Models\MedicalHistory;
use App\Models\PatientRecord;
use App\Models\PsychiatricHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PatientService
{
    public function getTodayPatients(): Collection
    {
        return PatientRecord::with('lifeCoach')
            ->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])
            ->latest()
            ->get();
    }

    public function getAllPatients(): Collection
    {
        return PatientRecord::with(['lifeCoach', 'lifestyleAssessment'])
            ->latest()
            ->get();
    }

    public function getHighRiskPatients(int $limit = 5): Collection
    {
        return PatientRecord::with('lifeCoach')
            ->where(function ($query) {
                $query->where('status', 'Critical')
                    ->orWhereHas('lifestyleAssessment', function ($q) {
                        $q->whereIn('phq_thoughts_hurting', [
                            'Several days',
                            'More than half the days',
                            'Nearly every day',
                        ]);
                    });
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getLifeCoaches(): Collection
    {
        if (! \Spatie\Permission\Models\Role::where('name', 'lifecoach')->exists()) {
            return new Collection;
        }

        return User::role('lifecoach')->orderBy('name')->get(['id', 'name', 'email']);
    }

    public function findPatient(int $id): PatientRecord
    {
        return PatientRecord::with([
            'lifeCoach',
            'medicalHistory',
            'psychiatricHistory',
            'lifestyleAssessment',
        ])->findOrFail($id);
    }

    public function createPatient(array $data): PatientRecord
    {
        return PatientRecord::create([
            'fullname' => $data['name'],
            'birthday' => $data['birthday'] ?? Carbon::now()->subYears((int) ($data['age'] ?? 30))->toDateString(),
            'sex' => strtolower($data['sex'] ?? 'female'),
            'gender' => $data['gender'] ?? null,
            'marital_status' => $data['marital_status'] ?? 'single',
            'religion' => $data['religion'] ?? null,
            'student_year_level' => $data['student_year_level'] ?? null,
            'course' => $data['course'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'chief_complaint' => $data['chief_complaint'] ?? '',
            'primary_diagnosis' => $data['primary_diagnosis'] ?? null,
            'clinical_notes' => $data['clinical_notes'] ?? null,
            'status' => $data['status'] ?? 'Active',
            'life_coach_id' => $data['life_coach_id'] ?? null,
        ]);
    }

    public function updatePatientRecord(PatientRecord $patient, array $data): PatientRecord
    {
        $patient->update([
            'fullname' => $data['fullname'] ?? $patient->fullname,
            'birthday' => $data['birthday'] ?? $patient->birthday,
            'religion' => $data['religion'] ?? $patient->religion,
            'sex' => isset($data['sex']) ? strtolower($data['sex']) : $patient->sex,
            'gender' => $data['gender'] ?? $patient->gender,
            'marital_status' => $data['marital_status'] ?? $patient->marital_status,
            'student_year_level' => $data['student_year_level'] ?? $patient->student_year_level,
            'course' => $data['course'] ?? $patient->course,
            'occupation' => $data['occupation'] ?? $patient->occupation,
            'chief_complaint' => $data['chief_complaint'] ?? $patient->chief_complaint,
            'primary_diagnosis' => $data['primary_diagnosis'] ?? $patient->primary_diagnosis,
            'clinical_notes' => $data['clinical_notes'] ?? $patient->clinical_notes,
            'status' => $data['status'] ?? $patient->status,
            'life_coach_id' => array_key_exists('life_coach_id', $data)
                ? ($data['life_coach_id'] ?: null)
                : $patient->life_coach_id,
        ]);

        return $patient->fresh(['lifeCoach', 'medicalHistory', 'psychiatricHistory', 'lifestyleAssessment']);
    }

    public function updateMedicalHistory(PatientRecord $patient, array $data): MedicalHistory
    {
        $payload = $this->booleanize($data, [
            'hypertension', 'stroke_tia', 'diabetes', 'bronchial_asthma', 'tuberculosis',
            'thyroid_disorders', 'chronic_pain_fibromyalgia', 'epilepsy_seizure',
            'autoimmune_disease', 'cancer', 'other_medical',
            'family_hypertension', 'family_stroke', 'family_diabetes', 'family_cancer',
            'family_psychiatric_disorder', 'family_substance_use', 'family_other',
        ]);

        return MedicalHistory::updateOrCreate(
            ['patient_record_id' => $patient->id],
            array_merge($payload, [
                'autoimmune_specify' => $data['autoimmune_specify'] ?? null,
                'cancer_specify' => $data['cancer_specify'] ?? null,
                'other_medical_specify' => $data['other_medical_specify'] ?? null,
                'current_medications' => $data['current_medications'] ?? null,
                'family_hypertension_relation' => $data['family_hypertension_relation'] ?? null,
                'family_stroke_relation' => $data['family_stroke_relation'] ?? null,
                'family_diabetes_relation' => $data['family_diabetes_relation'] ?? null,
                'family_cancer_type' => $data['family_cancer_type'] ?? null,
                'family_cancer_relation' => $data['family_cancer_relation'] ?? null,
                'family_psychiatric_relation' => $data['family_psychiatric_relation'] ?? null,
                'family_substance_relation' => $data['family_substance_relation'] ?? null,
                'family_other_specify' => $data['family_other_specify'] ?? null,
                'family_other_relation' => $data['family_other_relation'] ?? null,
            ])
        );
    }

    public function updatePsychiatricHistory(PatientRecord $patient, array $data): PsychiatricHistory
    {
        $payload = $this->booleanize($data, [
            'diagnosed_mental_condition', 'psychiatric_hospitalized',
            'physical_abuse', 'physical_child', 'physical_adult', 'physical_ongoing', 'physical_past',
            'emotional_abuse', 'emotional_child', 'emotional_adult', 'emotional_ongoing', 'emotional_past',
            'sexual_abuse', 'sexual_child', 'sexual_adult', 'sexual_ongoing', 'sexual_past',
            'neglect', 'neglect_child', 'neglect_adult', 'neglect_ongoing', 'neglect_past',
        ]);

        return PsychiatricHistory::updateOrCreate(
            ['patient_record_id' => $patient->id],
            array_merge($payload, [
                'mental_condition' => $data['mental_condition'] ?? null,
                'hospitalization_count' => isset($data['hospitalization_count']) && $data['hospitalization_count'] !== ''
                    ? (int) $data['hospitalization_count']
                    : null,
                'hospitalization_when' => $data['hospitalization_when'] ?? null,
                'physical_notes' => $data['physical_notes'] ?? null,
                'emotional_notes' => $data['emotional_notes'] ?? null,
                'sexual_notes' => $data['sexual_notes'] ?? null,
                'neglect_notes' => $data['neglect_notes'] ?? null,
            ])
        );
    }

    public function updateLifestyleAssessment(PatientRecord $patient, array $data): LifestyleAssessment
    {
        $payload = $this->booleanize($data, [
            'sub_nicotine', 'sub_alcohol', 'sub_recreational', 'sub_marijuana',
            'sub_screentime', 'sub_gambling', 'sub_others',
        ]);

        return LifestyleAssessment::updateOrCreate(
            ['patient_record_id' => $patient->id],
            array_merge($payload, [
                'health_score' => isset($data['health_score']) && $data['health_score'] !== '' ? (int) $data['health_score'] : null,
                'sleep_hours' => isset($data['sleep_hours']) && $data['sleep_hours'] !== '' ? (int) $data['sleep_hours'] : null,
                'tired_frequency' => $data['tired_frequency'] ?? null,
                'weight_perception' => $data['weight_perception'] ?? null,
                'fast_food_frequency' => $data['fast_food_frequency'] ?? null,
                'fruits_veg_servings' => $data['fruits_veg_servings'] ?? null,
                'exercise_frequency' => $data['exercise_frequency'] ?? null,
                'phq_little_interest' => $data['phq_little_interest'] ?? null,
                'phq_feeling_down' => $data['phq_feeling_down'] ?? null,
                'phq_trouble_sleeping' => $data['phq_trouble_sleeping'] ?? null,
                'phq_feeling_tired' => $data['phq_feeling_tired'] ?? null,
                'phq_poor_appetite' => $data['phq_poor_appetite'] ?? null,
                'phq_feeling_bad' => $data['phq_feeling_bad'] ?? null,
                'phq_trouble_concentrating' => $data['phq_trouble_concentrating'] ?? null,
                'phq_moving_slow' => $data['phq_moving_slow'] ?? null,
                'phq_thoughts_hurting' => $data['phq_thoughts_hurting'] ?? null,
                'sub_nicotine_amount' => $data['sub_nicotine_amount'] ?? null,
                'sub_nicotine_concern' => (int) ($data['sub_nicotine_concern'] ?? 0),
                'sub_alcohol_amount' => $data['sub_alcohol_amount'] ?? null,
                'sub_alcohol_concern' => (int) ($data['sub_alcohol_concern'] ?? 0),
                'sub_recreational_amount' => $data['sub_recreational_amount'] ?? null,
                'sub_recreational_concern' => (int) ($data['sub_recreational_concern'] ?? 0),
                'sub_marijuana_amount' => $data['sub_marijuana_amount'] ?? null,
                'sub_marijuana_concern' => (int) ($data['sub_marijuana_concern'] ?? 0),
                'sub_screentime_amount' => $data['sub_screentime_amount'] ?? null,
                'sub_screentime_concern' => (int) ($data['sub_screentime_concern'] ?? 0),
                'sub_gambling_amount' => $data['sub_gambling_amount'] ?? null,
                'sub_gambling_concern' => (int) ($data['sub_gambling_concern'] ?? 0),
                'sub_others_specify' => $data['sub_others_specify'] ?? null,
                'sub_others_concern' => (int) ($data['sub_others_concern'] ?? 0),
                'lifestyle_motivation' => $data['lifestyle_motivation'] ?? null,
                'motivation_level' => $data['motivation_level'] ?? null,
            ])
        );
    }

    public function getConsultations(): Collection
    {
        return ConsultationSchedule::with('patientRecord')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();
    }

    public function getPendingConsultations(int $limit = 5): Collection
    {
        return ConsultationSchedule::with('patientRecord')
            ->where('status', 'Scheduled')
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('time')
            ->limit($limit)
            ->get();
    }

    public function createConsultation(array $data): ConsultationSchedule
    {
        return ConsultationSchedule::create([
            'patient_record_id' => $data['patient_record_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'type' => $data['type'] ?? 'Initial',
            'status' => $data['status'] ?? 'Scheduled',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function updateConsultation(ConsultationSchedule $consultation, array $data): ConsultationSchedule
    {
        $consultation->update([
            'date' => $data['date'] ?? $consultation->date,
            'time' => $data['time'] ?? $consultation->time,
            'type' => $data['type'] ?? $consultation->type,
            'status' => $data['status'] ?? $consultation->status,
            'notes' => $data['notes'] ?? $consultation->notes,
            'diagnosis' => $data['diagnosis'] ?? $consultation->diagnosis,
            'treatment' => $data['treatment'] ?? $consultation->treatment,
        ]);

        return $consultation->fresh('patientRecord');
    }

    public function deleteConsultation(ConsultationSchedule $consultation): void
    {
        $consultation->delete();
    }

    public function patientToArray(PatientRecord $patient): array
    {
        $patient->loadMissing(['lifeCoach', 'medicalHistory', 'psychiatricHistory', 'lifestyleAssessment']);

        return [
            'id' => $patient->id,
            'patient_id' => $patient->patient_id,
            'name' => $patient->fullname,
            'age' => $patient->age,
            'birthday' => optional($patient->birthday)->format('Y-m-d'),
            'religion' => $patient->religion,
            'sex' => $patient->sex ? ucfirst($patient->sex) : null,
            'gender' => $patient->gender,
            'marital_status' => $patient->marital_status,
            'student_year_level' => $patient->student_year_level,
            'course' => $patient->course,
            'occupation' => $patient->occupation,
            'chief_complaint' => $patient->chief_complaint,
            'primary_diagnosis' => $patient->primary_diagnosis,
            'status' => $patient->status ?? 'Submitted',
            'life_coach_id' => $patient->life_coach_id,
            'coach' => $patient->lifeCoach?->name ?? 'Unassigned',
            'complaint' => $patient->chief_complaint,
            'medical_history' => $patient->medicalHistory,
            'psychiatric_history' => $patient->psychiatricHistory,
            'lifestyle_assessment' => $patient->lifestyleAssessment,
        ];
    }

    public function consultationToArray(ConsultationSchedule $c): array
    {
        $time = $c->time;
        if (is_string($time) && preg_match('/^\d{2}:\d{2}/', $time)) {
            $displayTime = Carbon::createFromFormat('H:i:s', strlen($time) === 5 ? $time.':00' : $time)->format('g:i A');
        } else {
            try {
                $displayTime = Carbon::parse($time)->format('g:i A');
            } catch (\Throwable) {
                $displayTime = (string) $time;
            }
        }

        return [
            'id' => $c->id,
            'patient_record_id' => $c->patient_record_id,
            'patient' => $c->patientRecord?->fullname ?? 'Unknown',
            'patient_id' => $c->patientRecord?->patient_id,
            'date' => optional($c->date)->format('Y-m-d'),
            'time' => $displayTime,
            'time_24' => is_string($c->time) ? substr($c->time, 0, 5) : Carbon::parse($c->time)->format('H:i'),
            'type' => $c->type,
            'status' => $c->status ?? 'Scheduled',
            'notes' => $c->notes,
            'diagnosis' => $c->diagnosis,
            'treatment' => $c->treatment,
        ];
    }

    private function booleanize(array $data, array $fields): array
    {
        $out = [];
        foreach ($fields as $field) {
            $out[$field] = ! empty($data[$field]) && in_array($data[$field], [true, 1, '1', 'on', 'true', 'yes'], true);
        }

        return $out;
    }
}
