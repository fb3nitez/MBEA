<?php

namespace App\Services;

use App\Models\BiopsychosocialAssessment;
use App\Models\ClinicalTemplate;
use App\Models\ConsultationSchedule;
use App\Models\LifestyleAssessment;
use App\Models\MedicalHistory;
use App\Models\PatientRecord;
use App\Models\Prescription;
use App\Models\PsychiatricHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

class PatientService
{
    public function getTodayPatients(): Collection
    {
        return PatientRecord::with('lifeCoach')
            ->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])
            ->latest()
            ->get();
    }

    public function getPaginatedTodayPatients(int $perPage = 10): LengthAwarePaginator
    {
        return PatientRecord::with('lifeCoach')
            ->whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])
            ->latest()
            ->paginate($perPage);
    }

    public function getAllPatients(): Collection
    {
        return PatientRecord::with(['lifeCoach', 'lifestyleAssessment'])
            ->oldest('created_at')
            ->get();
    }

    public function getPaginatedPatients(int $perPage = 10): LengthAwarePaginator
    {
        return PatientRecord::with(['lifeCoach', 'lifestyleAssessment'])
            ->oldest('created_at')
            ->paginate($perPage);
    }

    public function getHighRiskPatients(int $limit = 5): Collection
    {
        return PatientRecord::with('lifeCoach')
            ->whereHas('lifestyleAssessment', function ($q) {
                $q->whereIn('phq_thoughts_hurting', [
                    'Several days',
                    'More than half the days',
                    'Nearly every day',
                ]);
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

    /**
     * Lightweight patient list for selectors: initial suggestions when $query is empty,
     * otherwise name / patient_id search.
     *
     * @return SupportCollection<int, array{id:int,patient_id:?string,name:string,age:?int,sex:?string}>
     */
    public function searchPatients(?string $query = null, int $limit = 12): SupportCollection
    {
        $limit = max(1, min($limit, 25));
        $q = trim((string) $query);

        $builder = PatientRecord::query()
            ->select(['id', 'patient_id', 'fullname', 'birthday', 'sex', 'created_at']);

        if ($q !== '') {
            $builder->where(function ($queryBuilder) use ($q) {
                $queryBuilder->where('fullname', 'like', '%'.$q.'%')
                    ->orWhere('patient_id', 'like', '%'.$q.'%');
            })->orderBy('fullname');
        } else {
            $builder->latest('created_at');
        }

        return $builder->limit($limit)->get()->map(fn (PatientRecord $p) => [
            'id' => $p->id,
            'patient_id' => $p->patient_id,
            'name' => $p->fullname,
            'age' => $p->age,
            'sex' => $p->sex ? ucfirst($p->sex) : null,
        ])->values();
    }

    public function getLifestylePatients(): SupportCollection
    {
        return PatientRecord::with('lifestyleAssessment')
            ->whereHas('lifestyleAssessment')
            ->oldest('fullname')
            ->get()
            ->map(fn (PatientRecord $p) => [
                'id' => $p->id,
                'patient_id' => $p->patient_id,
                'name' => $p->fullname,
                'age' => $p->age,
                'sex' => $p->sex ? ucfirst($p->sex) : null,
                'lifestyle_assessment' => $p->lifestyleAssessment,
            ])
            ->values();
    }

    public function getClinicalTemplates(?string $type = null): SupportCollection
    {
        $this->ensureDefaultClinicalTemplates();

        $builder = ClinicalTemplate::query()->orderBy('sort_order')->orderBy('name');
        if ($type) {
            $builder->where('type', $type);
        }

        return $builder->get()->map(fn (ClinicalTemplate $t) => $t->toArray())->values();
    }

    public function createClinicalTemplate(array $data): ClinicalTemplate
    {
        return ClinicalTemplate::create($this->normalizeTemplateData($data));
    }

    public function updateClinicalTemplate(ClinicalTemplate $template, array $data): ClinicalTemplate
    {
        $template->update($this->normalizeTemplateData($data, $template));

        return $template->fresh();
    }

    public function deleteClinicalTemplate(ClinicalTemplate $template): void
    {
        $template->delete();
    }

    public function ensureDefaultClinicalTemplates(): void
    {
        if (ClinicalTemplate::query()->exists()) {
            return;
        }

        $defaults = [
            [
                'type' => 'rx',
                'name' => 'MDD — First Line SSRI',
                'tag' => 'Depression',
                'tag_class' => 'tag-depression',
                'description' => 'Sertraline 50mg',
                'payload' => [
                    'diag' => 'F32.1 Major Depressive Disorder',
                    'meds' => [['name' => 'Sertraline', 'dose' => '50mg', 'freq' => ['Morning'], 'qty' => 30]],
                ],
                'sort_order' => 1,
            ],
            [
                'type' => 'rx',
                'name' => 'GAD — Sertraline + PRN Clonazepam',
                'tag' => 'Anxiety',
                'tag_class' => 'tag-anxiety',
                'description' => 'Sertraline 50mg · Clonazepam 0.5mg',
                'payload' => [
                    'diag' => 'F41.1 Generalized Anxiety Disorder',
                    'meds' => [
                        ['name' => 'Sertraline', 'dose' => '50mg', 'freq' => ['Morning'], 'qty' => 30],
                        ['name' => 'Clonazepam', 'dose' => '0.5mg', 'freq' => ['Bedtime'], 'qty' => 10],
                    ],
                ],
                'sort_order' => 2,
            ],
            [
                'type' => 'rx',
                'name' => 'Schizophrenia — Risperidone Starter',
                'tag' => 'Psychosis',
                'tag_class' => 'tag-psychosis',
                'description' => 'Risperidone 2mg · Biperiden 2mg',
                'payload' => [
                    'diag' => 'F20.9 Schizophrenia',
                    'meds' => [
                        ['name' => 'Risperidone', 'dose' => '2mg', 'freq' => ['Morning', 'Dinner'], 'qty' => 60],
                        ['name' => 'Biperiden', 'dose' => '2mg', 'freq' => ['Morning'], 'qty' => 30],
                    ],
                ],
                'sort_order' => 3,
            ],
            [
                'type' => 'rx',
                'name' => 'Bipolar — Mood Stabilizer',
                'tag' => 'Bipolar',
                'tag_class' => 'tag-bipolar',
                'description' => 'Valproic Acid 500mg · Quetiapine 100mg',
                'payload' => [
                    'diag' => 'F31.0 Bipolar I Disorder',
                    'meds' => [
                        ['name' => 'Valproic Acid', 'dose' => '500mg', 'freq' => ['Morning', 'Dinner'], 'qty' => 60],
                        ['name' => 'Quetiapine', 'dose' => '100mg', 'freq' => ['Bedtime'], 'qty' => 30],
                    ],
                ],
                'sort_order' => 4,
            ],
            [
                'type' => 'dx',
                'name' => 'Psychiatric Baseline Panel',
                'tag' => 'Psychiatric',
                'tag_class' => 'tag-psychiatric',
                'description' => 'CBC, TSH, FBS, Lipid Profile, Urinalysis',
                'payload' => [
                    'tests' => ['CBC with differential', 'TSH', 'Fasting Blood Sugar', 'Lipid Profile', 'Complete urinalysis'],
                ],
                'sort_order' => 1,
            ],
            [
                'type' => 'dx',
                'name' => 'Mood Disorder Workup',
                'tag' => 'Anxiety',
                'tag_class' => 'tag-anxiety',
                'description' => 'CBC, TSH, Free T3/T4, Sodium, Lithium',
                'payload' => [
                    'tests' => ['CBC with differential', 'TSH', 'Free T3', 'Free T4', 'Sodium'],
                ],
                'sort_order' => 2,
            ],
            [
                'type' => 'dx',
                'name' => 'Metabolic Monitoring',
                'tag' => 'Metabolic',
                'tag_class' => 'tag-metabolic',
                'description' => 'FBS, HbA1c, Lipid Profile, Creatinine',
                'payload' => [
                    'tests' => ['Fasting Blood Sugar', 'HbA1c', 'Lipid Profile', 'Creatinine', 'eGFR'],
                ],
                'sort_order' => 3,
            ],
            [
                'type' => 'dx',
                'name' => 'Comprehensive Workup',
                'tag' => 'Comprehensive',
                'tag_class' => 'tag-comprehensive',
                'description' => 'All CBC, Thyroid, Liver, Renal + X-ray',
                'payload' => [
                    'tests' => ['CBC with differential', 'TSH', 'Free T3', 'Free T4', 'AST', 'ALT', 'BUN', 'Creatinine', 'Fasting Blood Sugar', 'HbA1c', 'Lipid Profile', 'Chest X-ray'],
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($defaults as $row) {
            ClinicalTemplate::create($row);
        }
    }

    private function normalizeTemplateData(array $data, ?ClinicalTemplate $existing = null): array
    {
        $type = $data['type'] ?? $existing?->type ?? 'rx';
        $payload = $data['payload'] ?? null;

        if ($payload === null) {
            if ($type === 'dx') {
                $payload = ['tests' => $data['tests'] ?? ($existing?->payload['tests'] ?? [])];
            } else {
                $payload = [
                    'diag' => $data['diag'] ?? ($existing?->payload['diag'] ?? null),
                    'meds' => $data['meds'] ?? ($existing?->payload['meds'] ?? []),
                ];
            }
        }

        $tag = $data['tag'] ?? $existing?->tag;
        $tagClass = $data['tag_class'] ?? $data['tagClass'] ?? $existing?->tag_class;
        if (! $tagClass && $tag) {
            $tagClass = $this->guessTagClass($tag, $type);
        }

        return [
            'type' => $type,
            'name' => $data['name'] ?? $existing?->name,
            'tag' => $tag,
            'tag_class' => $tagClass,
            'description' => $data['description'] ?? $data['desc'] ?? $existing?->description,
            'payload' => $payload,
            'sort_order' => isset($data['sort_order']) ? (int) $data['sort_order'] : ($existing?->sort_order ?? 0),
        ];
    }

    private function guessTagClass(string $tag, string $type): string
    {
        $map = [
            'depression' => 'tag-depression',
            'anxiety' => 'tag-anxiety',
            'psychosis' => 'tag-psychosis',
            'bipolar' => 'tag-bipolar',
            'ptsd' => 'tag-ptsd',
            'psychiatric' => 'tag-psychiatric',
            'metabolic' => 'tag-metabolic',
            'comprehensive' => 'tag-comprehensive',
        ];

        $key = strtolower($tag);

        return $map[$key] ?? ($type === 'dx' ? 'tag-psychiatric' : 'tag-depression');
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
            'life_coach_id' => array_key_exists('life_coach_id', $data)
                ? ($data['life_coach_id'] ?: null)
                : $patient->life_coach_id,
        ]);

        return $patient->fresh(['lifeCoach', 'medicalHistory', 'psychiatricHistory', 'lifestyleAssessment']);
    }

    public function updateMedicalHistory(PatientRecord $patient, array $data): MedicalHistory
    {
        $payload = $this->booleanize($data, [
            'hypertension',
            'stroke_tia',
            'diabetes',
            'bronchial_asthma',
            'tuberculosis',
            'thyroid_disorders',
            'chronic_pain_fibromyalgia',
            'epilepsy_seizure',
            'autoimmune_disease',
            'cancer',
            'other_medical',
            'family_hypertension',
            'family_stroke',
            'family_diabetes',
            'family_cancer',
            'family_psychiatric_disorder',
            'family_substance_use',
            'family_other',
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
            'diagnosed_mental_condition',
            'psychiatric_hospitalized',
            'physical_abuse',
            'physical_child',
            'physical_adult',
            'physical_ongoing',
            'physical_past',
            'emotional_abuse',
            'emotional_child',
            'emotional_adult',
            'emotional_ongoing',
            'emotional_past',
            'sexual_abuse',
            'sexual_child',
            'sexual_adult',
            'sexual_ongoing',
            'sexual_past',
            'neglect',
            'neglect_child',
            'neglect_adult',
            'neglect_ongoing',
            'neglect_past',
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
            'sub_nicotine',
            'sub_alcohol',
            'sub_recreational',
            'sub_marijuana',
            'sub_screentime',
            'sub_gambling',
            'sub_others',
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
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    }

    public function getPaginatedConsultations(int $perPage = 10): LengthAwarePaginator
    {
        return ConsultationSchedule::with('patientRecord')
            ->orderBy('date')
            ->orderBy('time')
            ->paginate($perPage);
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

    public function saveAssessment(PatientRecord $patient, array $data): BiopsychosocialAssessment
    {
        $existing = $this->getAssessment($patient);
        $merged = is_array($existing?->assessment_data) ? $existing->assessment_data : [];

        foreach (['biological', 'psychological', 'social', 'spiritual', 'prayer_points', 'intervention'] as $section) {
            if (array_key_exists($section, $data)) {
                $merged[$section] = $data[$section];
            }
        }

        return BiopsychosocialAssessment::updateOrCreate(
            ['patient_record_id' => $patient->id],
            [
                'assessment_data' => $merged,
                'status' => $data['status'] ?? $existing?->status ?? 'Active',
            ]
        );
    }

    public function getAssessment(PatientRecord $patient): ?BiopsychosocialAssessment
    {
        return BiopsychosocialAssessment::where('patient_record_id', $patient->id)
            ->latest('updated_at')
            ->first();
    }

    public function getPaginatedAssessmentPatients(int $perPage = 12, ?string $search = null): LengthAwarePaginator
    {
        $builder = PatientRecord::query()
            ->with(['biopsychosocialAssessments' => function ($query) {
                $query->latest('updated_at');
            }])
            ->oldest('created_at');

        if ($search) {
            $builder->where(function ($query) use ($search) {
                $query->where('fullname', 'like', '%'.$search.'%')
                    ->orWhere('patient_id', 'like', '%'.$search.'%');
            });
        }

        return $builder->paginate($perPage)->through(function (PatientRecord $patient) {
            $assessment = $patient->biopsychosocialAssessments->first();
            $data = is_array($assessment?->assessment_data) ? $assessment->assessment_data : [];
            $cc = $data['biological']['cc'] ?? null;

            return [
                'id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'name' => $patient->fullname,
                'age' => $patient->age,
                'sex' => $patient->sex ? ucfirst($patient->sex) : null,
                'primary_diagnosis' => $patient->primary_diagnosis,
                'has_assessment' => (bool) $assessment,
                'summary' => $cc ?: ($patient->primary_diagnosis ?: 'No assessment yet'),
                'assessment_updated_at' => optional($assessment?->updated_at)->format('Y-m-d'),
                'completion' => $this->assessmentCompletionPercent($data),
            ];
        });
    }

    public function assessmentToArray(?BiopsychosocialAssessment $assessment): array
    {
        if (! $assessment) {
            return [];
        }

        return is_array($assessment->assessment_data) ? $assessment->assessment_data : [];
    }

    private function assessmentCompletionPercent(array $data): int
    {
        $sections = ['biological', 'psychological', 'social', 'spiritual', 'prayer_points', 'intervention'];
        $filled = 0;

        foreach ($sections as $section) {
            if (empty($data[$section])) {
                continue;
            }

            if (is_array($data[$section])) {
                $hasContent = collect($data[$section])->filter(function ($value) {
                    if (is_array($value)) {
                        return collect($value)->filter(fn ($item) => filled($item))->isNotEmpty();
                    }

                    return filled($value);
                })->isNotEmpty();

                if ($hasContent) {
                    $filled++;
                }
            }
        }

        return (int) round(($filled / max(count($sections), 1)) * 100);
    }

    public function savePrescription(PatientRecord $patient, array $data): Prescription
    {
        return Prescription::updateOrCreate(
            ['patient_record_id' => $patient->id],
            [
                'diagnosis' => $data['diagnosis'] ?? null,
                'medications' => $data['medications'] ?? [],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'Draft',
            ]
        );
    }

    public function getPrescription(PatientRecord $patient): ?Prescription
    {
        return Prescription::where('patient_record_id', $patient->id)->first();
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
            'life_coach_id' => $patient->life_coach_id,
            'coach' => $patient->lifeCoach?->name ?? 'Unassigned',
            'complaint' => $patient->chief_complaint,
            'clinical_notes' => $patient->clinical_notes,
            'medical_history' => $patient->medicalHistory,
            'psychiatric_history' => $patient->psychiatricHistory,
            'lifestyle_assessment' => $patient->lifestyleAssessment,
        ];
    }

    public function consultationToArray(ConsultationSchedule $c): array
    {
        $time = $c->time;
        if (is_string($time) && preg_match('/^\d{2}:\d{2}/', $time)) {
            $displayTime = Carbon::createFromFormat('H:i:s', strlen($time) === 5 ? $time . ':00' : $time)->format('g:i A');
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
