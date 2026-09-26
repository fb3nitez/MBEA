<?php

namespace App\Services;

use App\Models\CoachingGoal;
use App\Models\CoachingNote;
use App\Models\CoachingSchedule;
use App\Models\CoachingTask;
use App\Models\PatientRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;

class LifeCoachService
{
    public function currentCoach(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function profileActivity(?int $coachId = null, int $offset = 0, int $limit = 10): array
    {
        $coachId ??= $this->currentCoach()->id;
        $limit = max(1, min($limit, 25));

        $items = collect()
            ->merge(CoachingNote::where('life_coach_id', $coachId)->latest()->get()->map(fn(CoachingNote $note) => [
                'type' => 'note',
                'icon' => 'edit-3',
                'label' => 'Coaching note written',
                'detail' => $note->session_type,
                'date' => optional($note->created_at)->toIso8601String(),
            ]))
            ->merge(CoachingTask::where('life_coach_id', $coachId)->where('is_done', true)->latest('completed_at')->get()->map(fn(CoachingTask $task) => [
                'type' => 'task',
                'icon' => 'check-circle',
                'label' => 'Task completed',
                'detail' => $task->description,
                'date' => optional($task->completed_at ?: $task->updated_at)->toIso8601String(),
            ]))
            ->merge(PatientRecord::where('life_coach_id', $coachId)->latest()->get()->map(fn(PatientRecord $patient) => [
                'type' => 'patient',
                'icon' => 'user-plus',
                'label' => 'Patient assigned',
                'detail' => $patient->fullname,
                'date' => optional($patient->updated_at ?: $patient->created_at)->toIso8601String(),
            ]))
            ->sortByDesc('date')
            ->values();

        return [
            'items' => $items->slice($offset, $limit)->values(),
            'has_more' => $items->count() > $offset + $limit,
        ];
    }

    // <edit-marker SimpforLyla> added medicalHistory and psychiatricHistory to eager load
    public function getAssignedPatients(?int $coachId = null): Collection
    {
        $coachId ??= $this->currentCoach()->id;

        return PatientRecord::with([
            'lifeCoach',
            'lifestyleAssessment',
            'prescriptions',
            'medicalHistory',
            'psychiatricHistory',
            'spiritualIntake',
            'interventions',
            'coachingNotes' => fn($query) => $query->where('life_coach_id', $coachId)->latest(),
            'coachingGoals' => fn($query) => $query->where('life_coach_id', $coachId)->latest(),
        ])
            ->where('life_coach_id', $coachId)
            ->orderBy('fullname')
            ->get();
    }
    // </edit-marker>

    public function findAssignedPatient(int $id, ?int $coachId = null): PatientRecord
    {
        $coachId ??= $this->currentCoach()->id;

        return PatientRecord::with([
            'lifeCoach',
            'lifestyleAssessment',
            'prescriptions',
            'medicalHistory',
            'psychiatricHistory',
            'spiritualIntake',
            'interventions',
            'coachingNotes' => fn($q) => $q->where('life_coach_id', $coachId)->latest(),
            'coachingGoals' => fn($q) => $q->where('life_coach_id', $coachId)->latest(),
        ])
            ->where('life_coach_id', $coachId)
            ->findOrFail($id);
    }

    public function getNotes(?int $coachId = null): Collection
    {
        $coachId ??= $this->currentCoach()->id;

        return CoachingNote::with('patientRecord')
            ->where('life_coach_id', $coachId)
            ->latest()
            ->get();
    }

    public function createNote(array $data, ?int $coachId = null): CoachingNote
    {
        $coachId ??= $this->currentCoach()->id;
        $this->assertAssignedPatient((int) $data['patient_record_id'], $coachId);

        return CoachingNote::create([
            'patient_record_id' => $data['patient_record_id'],
            'life_coach_id' => $coachId,
            'session_type' => $data['session_type'] ?? 'Follow-up',
            'body' => $data['body'],
        ]);
    }

    public function deleteNote(int $id, ?int $coachId = null): void
    {
        $coachId ??= $this->currentCoach()->id;

        CoachingNote::where('life_coach_id', $coachId)->findOrFail($id)->delete();
    }

    public function getTasks(?int $coachId = null): Collection
    {
        $coachId ??= $this->currentCoach()->id;

        return CoachingTask::with('patientRecord')
            ->where('life_coach_id', $coachId)
            ->orderByRaw('is_done asc')
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function createTask(array $data, ?int $coachId = null): CoachingTask
    {
        $coachId ??= $this->currentCoach()->id;
        $this->assertAssignedPatient((int) $data['patient_record_id'], $coachId);

        return CoachingTask::create([
            'patient_record_id' => $data['patient_record_id'],
            'life_coach_id' => $coachId,
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'Medium',
            'due_date' => $data['due_date'] ?? null,
            'is_done' => false,
        ]);
    }

    public function toggleTask(int $id, bool $done, ?int $coachId = null): CoachingTask
    {
        $coachId ??= $this->currentCoach()->id;
        $task = CoachingTask::where('life_coach_id', $coachId)->findOrFail($id);
        $task->update([
            'is_done' => $done,
            'completed_at' => $done ? now() : null,
        ]);

        return $task->fresh('patientRecord');
    }

    public function getWeekSchedules(?int $coachId = null): Collection
    {
        $coachId ??= $this->currentCoach()->id;
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        return CoachingSchedule::with('patientRecord')
            ->where('life_coach_id', $coachId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    }

    public function createSchedule(array $data, ?int $coachId = null): CoachingSchedule
    {
        $coachId ??= $this->currentCoach()->id;
        $this->assertAssignedPatient((int) $data['patient_record_id'], $coachId);

        return CoachingSchedule::create([
            'patient_record_id' => $data['patient_record_id'],
            'life_coach_id' => $coachId,
            'topic' => $data['topic'],
            'date' => $data['date'],
            'time' => $data['time'] ?? null,
        ]);
    }

    public function createGoal(array $data, ?int $coachId = null): CoachingGoal
    {
        $coachId ??= $this->currentCoach()->id;
        $this->assertAssignedPatient((int) $data['patient_record_id'], $coachId);

        $weeklyCheckins = $data['weekly_checkins'] ?? array_fill(0, 7, false);
        if (! is_array($weeklyCheckins)) {
            $weeklyCheckins = array_fill(0, 7, false);
        }
        $weeklyCheckins = array_values(array_map(
            fn($v) => (bool) $v,
            array_slice(array_pad($weeklyCheckins, 7, false), 0, 7)
        ));

        return CoachingGoal::create([
            'patient_record_id' => $data['patient_record_id'],
            'life_coach_id' => $coachId,
            'title' => $data['title'],
            'category' => $data['category'] ?? 'Mental Wellness',
            'description' => $data['description'] ?? null,
            'target_date' => $data['target_date'] ?? null,
            'progress' => (int) ($data['progress'] ?? 0),
            'weekly_checkins' => $weeklyCheckins,
        ]);
    }

    public function findGoalById(int $goalId, ?int $coachId = null): CoachingGoal
    {
        $coachId ??= $this->currentCoach()->id;

        return CoachingGoal::where('life_coach_id', $coachId)
            ->findOrFail($goalId);
    }

    public function updateGoal(array $data, int $goalId, ?int $coachId = null): CoachingGoal
    {
        $coachId ??= $this->currentCoach()->id;
        $goal = CoachingGoal::where('life_coach_id', $coachId)
            ->findOrFail($goalId);

        $weeklyCheckins = $data['weekly_checkins'] ?? null;
        if ($weeklyCheckins !== null) {
            if (! is_array($weeklyCheckins)) {
                $weeklyCheckins = array_fill(0, 7, false);
            }
            $weeklyCheckins = array_values(array_map(
                fn($v) => (bool) $v,
                array_slice(array_pad($weeklyCheckins, 7, false), 0, 7)
            ));
        }

        $goal->update([
            'title' => $data['title'] ?? $goal->title,
            'category' => $data['category'] ?? $goal->category,
            'description' => array_key_exists('description', $data) ? $data['description'] : $goal->description,
            'target_date' => array_key_exists('target_date', $data) ? ($data['target_date'] ?: null) : $goal->target_date,
            'progress' => array_key_exists('progress', $data) ? (int) $data['progress'] : $goal->progress,
            'weekly_checkins' => $weeklyCheckins ?? $goal->weekly_checkins ?? array_fill(0, 7, false),
        ]);

        return $goal->fresh();
    }

    public function updateGoalProgress(int $goalId, int $progress, ?int $coachId = null): CoachingGoal
    {
        $coachId ??= $this->currentCoach()->id;

        return $this->updateGoal([
            'progress' => max(0, min(100, (int) $progress)),
        ], $goalId, $coachId);
    }

    public function deleteGoal(int $goalId, ?int $coachId = null): void
    {
        $coachId ??= $this->currentCoach()->id;

        CoachingGoal::where('life_coach_id', $coachId)
            ->findOrFail($goalId)
            ->delete();
    }

    public function dashboardStats(?int $coachId = null): array
    {
        $coachId ??= $this->currentCoach()->id;
        $patients = $this->getAssignedPatients($coachId);
        $tasks = $this->getTasks($coachId);
        $pending = $tasks->where('is_done', false);
        $completedThisWeek = $tasks
            ->where('is_done', true)
            ->filter(fn(CoachingTask $t) => $t->completed_at && $t->completed_at->gte(now()->startOfWeek()))
            ->count();

        $avgProgress = $patients->isEmpty()
            ? 0
            : round(
                CoachingGoal::where('life_coach_id', $coachId)
                    ->whereIn('patient_record_id', $patients->pluck('id'))
                    ->avg('progress') ?? 0,
                1
            );

        return [
            'patient_count' => $patients->count(),
            'pending_tasks' => $pending->count(),
            'completed_this_week' => $completedThisWeek,
            'avg_progress' => $avgProgress,
        ];
    }

    // <edit-marker SimpforLyla> added medicalHistory and psychiatricHistory to loadMissing + intake key
    public function patientToArray(PatientRecord $patient): array
    {
        $patient->loadMissing([
            'lifeCoach',
            'lifestyleAssessment',
            'prescriptions',
            'coachingNotes',
            'coachingGoals',
            'medicalHistory',
            'psychiatricHistory',
            'spiritualIntake',
            'interventions',
        ]);

        $coachId = $this->currentCoach()->id;

        $notes = $patient->coachingNotes
            ->where('life_coach_id', $coachId)
            ->sortByDesc('created_at')
            ->values()
            ->map(fn(CoachingNote $n) => $this->noteToArray($n));

        $goals = $patient->coachingGoals
            ->where('life_coach_id', $coachId)
            ->sortByDesc('created_at')
            ->values()
            ->map(fn(CoachingGoal $g) => $this->goalToArray($g));

        $habitGoals = $patient->coachingGoals
            ->where('life_coach_id', $coachId)
            ->sortByDesc('created_at')
            ->values();

        $habitNames = $habitGoals
            ->map(fn(CoachingGoal $g) => $g->title)
            ->values()
            ->all();

        $habitData = $habitGoals
            ->map(fn(CoachingGoal $g) => array_values($g->weekly_checkins ?? array_fill(0, 7, false)))
            ->values()
            ->all();

        return [
            'id' => $patient->id,
            'patient_id' => $patient->patient_id,
            'name' => $patient->fullname,
            'age' => $patient->age,
            'sex' => $patient->sex ? ucfirst($patient->sex) : '—',
            'employment_status' => $patient->employment_status,
            'status' => 'Active',
            'complaint' => $patient->chief_complaint ?? '—',
            'email' => '—',
            'phone' => '—',
            'program' => $patient->primary_diagnosis ?? 'Lifestyle Coaching',
            'coach' => $patient->lifeCoach?->name ?? 'Unassigned',
            'nextAppt' => $this->nextAppointmentLabel($patient),
            'prescriptions' => $this->prescriptionsToArray($patient),
            'metrics' => $this->metricsFromLifestyle($patient),
            'compliance' => $this->complianceSeries($patient),
            'goals' => $goals,
            'habits' => $habitNames,
            'habitData' => $habitData,
            'notes' => $notes,
            'intake' => $this->intakeToArray($patient),
        ];
    }
    // </edit-marker>

    public function noteToArray(CoachingNote $note): array
    {
        $note->loadMissing('patientRecord');

        return [
            'id' => $note->id,
            'patient_record_id' => $note->patient_record_id,
            'patient' => $note->patientRecord?->fullname ?? 'Unknown',
            'patientId' => $note->patient_record_id,
            'type' => $note->session_type,
            'date' => $note->created_at?->format('M j, Y') ?? '',
            'text' => $note->body,
        ];
    }

    public function taskToArray(CoachingTask $task): array
    {
        $task->loadMissing('patientRecord');

        return [
            'id' => $task->id,
            'patient_record_id' => $task->patient_record_id,
            'patient' => $task->patientRecord?->fullname ?? 'Unknown',
            'desc' => $task->description,
            'priority' => $task->priority,
            'due' => $task->due_date?->format('Y-m-d') ?? 'TBD',
            'done' => (bool) $task->is_done,
        ];
    }

    public function scheduleToArray(CoachingSchedule $schedule): array
    {
        $schedule->loadMissing('patientRecord');

        $timeLabel = 'TBD';
        if ($schedule->time) {
            try {
                $raw = is_string($schedule->time) ? $schedule->time : (string) $schedule->time;
                $timeLabel = Carbon::parse($raw)->format('g:i A');
            } catch (\Throwable) {
                $timeLabel = (string) $schedule->time;
            }
        }

        return [
            'id' => $schedule->id,
            'patient_record_id' => $schedule->patient_record_id,
            'patient' => $schedule->patientRecord?->fullname ?? 'Unknown',
            'topic' => $schedule->topic,
            'date' => optional($schedule->date)->format('M j'),
            'date_raw' => optional($schedule->date)->format('Y-m-d'),
            'time' => $timeLabel,
        ];
    }

    public function goalToArray(CoachingGoal $goal): array
    {
        return [
            'id' => $goal->id,
            'patient_record_id' => $goal->patient_record_id,
            'title' => $goal->title,
            'cat' => $goal->category,
            'desc' => $goal->description ?? '',
            'date' => $goal->target_date?->format('M j, Y') ?? 'TBD',
            'date_raw' => $goal->target_date?->format('Y-m-d') ?? null,
            'prog' => (int) $goal->progress,
            'weekly_checkins' => array_values($goal->weekly_checkins ?? array_fill(0, 7, false)),
        ];
    }

    public function patientOptions(?int $coachId = null): SupportCollection
    {
        return $this->getAssignedPatients($coachId)
            ->map(fn(PatientRecord $p) => [
                'id' => $p->id,
                'name' => $p->fullname,
                'patient_id' => $p->patient_id,
            ])
            ->values();
    }

    private function assertAssignedPatient(int $patientId, int $coachId): void
    {
        PatientRecord::where('life_coach_id', $coachId)->findOrFail($patientId);
    }

    private function nextAppointmentLabel(PatientRecord $patient): string
    {
        $next = CoachingSchedule::where('patient_record_id', $patient->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->first();

        return $next?->date?->format('Y-m-d') ?? '—';
    }

    private function prescriptionsToArray(PatientRecord $patient): array
    {
        $items = [];
        foreach ($patient->prescriptions as $rx) {
            foreach (($rx->medications ?? []) as $med) {
                $items[] = [
                    'tag' => $rx->diagnosis ?: 'Rx',
                    'name' => is_array($med)
                        ? trim(($med['name'] ?? 'Medication') . (isset($med['dose']) ? ' ' . $med['dose'] : ''))
                        : (string) $med,
                ];
            }
        }

        return $items;
    }

    // <edit-marker SimpforLyla> new method — builds read-only intake form data for life coach view
    private function intakeToArray(PatientRecord $patient): array
    {
        $mh = $patient->medicalHistory;
        $ph = $patient->psychiatricHistory;
        $ls = $patient->lifestyleAssessment;

        // Personal
        $personal = [
            ['label' => 'Full Name', 'value' => $patient->fullname ?? '—'],
            ['label' => 'Birthday', 'value' => $patient->birthday?->format('F j, Y') ?? '—'],
            ['label' => 'Age', 'value' => $patient->age ?? '—'],
            ['label' => 'Sex', 'value' => $patient->sex ? ucfirst($patient->sex) : '—'],
            ['label' => 'Gender', 'value' => $patient->gender ?? '—'],
            ['label' => 'Marital Status', 'value' => $patient->marital_status ? ucfirst($patient->marital_status) : '—'],
            ['label' => 'Employment Status', 'value' => $patient->employment_status === 'NA' ? 'Not Applicable' : ($patient->employment_status ? ucfirst($patient->employment_status) : '—')],
            ['label' => 'Religion', 'value' => $patient->religion ?? '—'],
            ['label' => 'Occupation', 'value' => $patient->occupation ?? '—'],
            ['label' => 'Course', 'value' => $patient->course ?? '—'],
            ['label' => 'Year Level', 'value' => $patient->student_year_level ?? '—'],
            ['label' => 'Chief Complaint', 'value' => $patient->chief_complaint ?? '—'],
            ['label' => 'Diagnosis', 'value' => $patient->primary_diagnosis ?? '—'],
        ];

        $clinicalNotes = $patient->clinical_notes;

        // Medical history conditions
        $conditions = [];
        if ($mh) {
            $condMap = [
                'hypertension' => 'Hypertension',
                'stroke_tia' => 'Stroke / TIA',
                'diabetes' => 'Diabetes',
                'bronchial_asthma' => 'Bronchial Asthma',
                'tuberculosis' => 'Tuberculosis',
                'thyroid_disorders' => 'Thyroid Disorders',
                'chronic_pain_fibromyalgia' => 'Chronic Pain / Fibromyalgia',
                'epilepsy_seizure' => 'Epilepsy / Seizure',
            ];
            foreach ($condMap as $field => $label) {
                if ($mh->$field) {
                    $conditions[] = $label;
                }
            }
            if ($mh->autoimmune_disease) {
                $conditions[] = 'Autoimmune Disease' . ($mh->autoimmune_specify ? ': ' . $mh->autoimmune_specify : '');
            }
            if ($mh->cancer) {
                $conditions[] = 'Cancer' . ($mh->cancer_specify ? ': ' . $mh->cancer_specify : '');
            }
            if ($mh->other_medical) {
                $conditions[] = 'Other: ' . ($mh->other_medical_specify ?? '—');
            }
        }

        // Family history
        $familyHistory = [];
        if ($mh) {
            $famMap = [
                ['flag' => 'family_hypertension', 'label' => 'Hypertension', 'rel' => 'family_hypertension_relation'],
                ['flag' => 'family_stroke', 'label' => 'Stroke', 'rel' => 'family_stroke_relation'],
                ['flag' => 'family_diabetes', 'label' => 'Diabetes', 'rel' => 'family_diabetes_relation'],
                ['flag' => 'family_cancer', 'label' => 'Cancer', 'rel' => 'family_cancer_relation'],
                ['flag' => 'family_psychiatric_disorder', 'label' => 'Psychiatric Disorder', 'rel' => 'family_psychiatric_relation'],
                ['flag' => 'family_substance_use', 'label' => 'Substance Use', 'rel' => 'family_substance_relation'],
                ['flag' => 'family_other', 'label' => 'Other', 'rel' => 'family_other_relation'],
            ];
            foreach ($famMap as $f) {
                if ($mh->{$f['flag']}) {
                    $rel = $mh->{$f['rel']} ?? null;
                    $familyHistory[] = $f['label'] . ($rel ? ' (' . $rel . ')' : '');
                }
            }
        }

        // Psychiatric history
        $psychiatric = [];
        if ($ph) {
            $psychiatric = [
                ['label' => 'Diagnosed Mental Condition', 'value' => $ph->diagnosed_mental_condition ? ($ph->mental_condition ?? 'Yes') : 'No'],
                [
                    'label' => 'Psychiatric Hospitalization',
                    'value' => $ph->psychiatric_hospitalized
                        ? 'Yes — ' . ($ph->hospitalization_count ?? '?') . 'x, ' . ($ph->hospitalization_when ?? '—')
                        : 'No',
                ],
            ];

            $abuseTypes = [
                'physical' => 'Physical Abuse',
                'emotional' => 'Emotional Abuse',
                'sexual' => 'Sexual Abuse',
                'neglect' => 'Neglect',
            ];
            foreach ($abuseTypes as $key => $abuseLabel) {
                if ($ph->{$key . '_abuse'} ?? $ph->{$key}) {
                    $timing = [];
                    if ($ph->{$key . '_child'}) {
                        $timing[] = 'Childhood';
                    }
                    if ($ph->{$key . '_adult'}) {
                        $timing[] = 'Adulthood';
                    }
                    if ($ph->{$key . '_ongoing'}) {
                        $timing[] = 'Ongoing';
                    }
                    if ($ph->{$key . '_past'}) {
                        $timing[] = 'Past';
                    }
                    $psychiatric[] = [
                        'label' => $abuseLabel,
                        'value' => implode(', ', $timing) ?: 'Yes',
                    ];
                }
            }
        }

        // Lifestyle
        $lifestyle = [];
        $phq = [];
        $substancesUsed = [];
        $motivation = [];
        if ($ls) {
            $appendLifestyle = function ($label, $value) use (&$lifestyle): void {
                if ($value === null || (is_string($value) && trim($value) === '')) {
                    return;
                }

                $lifestyle[] = ['label' => $label, 'value' => $value];
            };

            $appendLifestyle('Health Score', $ls->health_score !== null ? $ls->health_score . '/10' : null);
            $appendLifestyle('Sleep Hours', $ls->sleep_hours !== null ? $ls->sleep_hours . ' hrs' : null);
            $appendLifestyle('Tired Frequency', $ls->tired_frequency);
            $appendLifestyle('Weight Perception', $ls->weight_perception);
            $appendLifestyle('Fast Food Frequency', $ls->fast_food_frequency);
            $appendLifestyle('Fruits/Veg Servings', $ls->fruits_veg_servings);
            $appendLifestyle('Exercise Frequency', $ls->exercise_frequency);
            $motivation[] = ['label' => 'Motivation Level', 'value' => $ls->motivation_level];
            $motivation[] = ['label' => 'Lifestyle Motivation', 'value' => $ls->lifestyle_motivation];
            $motivation = array_values(array_filter($motivation, fn(array $item): bool => $item['value'] !== null && (! is_string($item['value']) || trim($item['value']) !== '')));

            // PHQ-9
            $phqMap = [
                'phq_little_interest' => 'Little Interest / Pleasure',
                'phq_feeling_down' => 'Feeling Down / Hopeless',
                'phq_trouble_sleeping' => 'Trouble Sleeping',
                'phq_feeling_tired' => 'Feeling Tired',
                'phq_poor_appetite' => 'Poor Appetite',
                'phq_feeling_bad' => 'Feeling Bad About Self',
                'phq_trouble_concentrating' => 'Trouble Concentrating',
                'phq_moving_slow' => 'Moving / Speaking Slowly',
                'phq_thoughts_hurting' => 'Thoughts of Hurting Self',
            ];
            foreach ($phqMap as $field => $label) {
                $phq[] = ['label' => $label, 'value' => $ls->$field];
            }

            // Substance use
            $substances = [
                'sub_nicotine' => 'Nicotine',
                'sub_alcohol' => 'Alcohol',
                'sub_recreational' => 'Recreational Drugs',
                'sub_marijuana' => 'Marijuana',
                'sub_screentime' => 'Screen Time',
                'sub_gambling' => 'Gambling',
                'sub_others' => 'Other Substances',
            ];
            foreach ($substances as $field => $label) {
                if ($ls->$field) {
                    $amount = $ls->{$field . '_amount'} ?? '—';
                    $concern = $ls->{$field . '_concern'} ?? 0;
                    $substancesUsed[] = [
                        'label' => $label,
                        'value' => "Amount/Details: {$amount}\nConcern level: {$concern}/5",
                    ];
                }
            }
        }

        $spiritual = [];
        if ($patient->spiritualIntake) {
            foreach ($patient->spiritualIntake->getAttributes() as $field => $value) {
                if (in_array($field, ['id', 'patient_record_id', 'created_at', 'updated_at'], true) || $value === null || $value === '' || $value === false || $value === 0 || $value === '0') {
                    continue;
                }

                $label = ucwords(str_replace('_', ' ', preg_replace('/^(religious_background_|church_involvement_|new_age_|additional_spiritual_issues_)/', '', $field)));
                $spiritual[] = ['label' => $label, 'value' => $value === true || $value === 1 || $value === '1' ? 'Yes' : $value];
            }
        }

        $interventions = [];
        if ($patient->interventions) {
            foreach (
                [
                    'psychiatric_therapy_medication' => 'Psychiatric Therapy & Medication',
                    'lifestyle_interventions' => 'Lifestyle Interventions (Exercise, Diet, Sleep, Stress Management)',
                    'substance_use_rehabilitation' => 'Substance Use Rehabilitation',
                    'spiritual_counseling' => 'Spiritual Counseling',
                ] as $field => $label
            ) {
                $interventions[] = ['label' => $label, 'value' => $patient->interventions->{$field}];
            }
        }

        return [
            'personal' => $personal,
            'clinical_notes' => $clinicalNotes,
            'conditions' => $conditions,
            'medications' => $mh?->current_medications ?? '—',
            'family' => $familyHistory,
            'psychiatric' => $psychiatric,
            'lifestyle' => $lifestyle,
            'phq' => $phq,
            'substances' => $substancesUsed,
            'motivation' => $motivation,
            'spiritual' => $spiritual,
            'interventions' => $interventions,
        ];
    }
    // </edit-marker>

    private function metricsFromLifestyle(PatientRecord $patient): array
    {
        $ls = $patient->lifestyleAssessment;
        if (! $ls) {
            return [];
        }

        $sleep = (int) ($ls->sleep_hours ?? 0);
        $sleepPct = min(100, (int) round(($sleep / 8) * 100));

        $exercise = (string) ($ls->exercise_frequency ?? '');
        $exercisePct = match (true) {
            str_contains(strtolower($exercise), 'daily') => 90,
            str_contains($exercise, '4') || str_contains($exercise, '5') => 70,
            str_contains($exercise, '3') => 50,
            str_contains($exercise, '1') || str_contains($exercise, '2') => 30,
            default => 40,
        };

        $nutrition = (string) ($ls->fruits_veg_servings ?? '');
        $nutritionPct = match (true) {
            str_contains($nutrition, '5') || str_contains(strtolower($nutrition), 'more') => 90,
            str_contains($nutrition, '3') || str_contains($nutrition, '4') => 70,
            str_contains($nutrition, '2') => 50,
            default => 40,
        };

        $stress = (string) ($ls->phq_feeling_down ?? $ls->motivation_level ?? '');
        $stressPct = match (true) {
            str_contains(strtolower($stress), 'nearly') || str_contains(strtolower($stress), 'high') => 85,
            str_contains(strtolower($stress), 'more than') || str_contains(strtolower($stress), 'moderate') => 60,
            str_contains(strtolower($stress), 'several') || str_contains(strtolower($stress), 'low') => 40,
            default => 50,
        };

        return [
            ['name' => 'Sleep Quality', 'value' => $sleep ? $sleep . ' hrs' : '—', 'pct' => $sleepPct, 'bar' => $this->barClass($sleepPct), 'val' => $this->valClass($sleepPct), 'icon' => 'moon'],
            ['name' => 'Exercise', 'value' => $exercise ?: '—', 'pct' => $exercisePct, 'bar' => $this->barClass($exercisePct), 'val' => $this->valClass($exercisePct), 'icon' => 'activity'],
            ['name' => 'Nutrition', 'value' => $nutrition ?: '—', 'pct' => $nutritionPct, 'bar' => $this->barClass($nutritionPct), 'val' => $this->valClass($nutritionPct), 'icon' => 'heart'],
            ['name' => 'Mood / Stress', 'value' => $stress ?: '—', 'pct' => $stressPct, 'bar' => $this->barClass(100 - $stressPct), 'val' => $this->valClass(100 - $stressPct), 'icon' => 'zap'],
            [
                'name' => 'Health Score',
                'value' => $ls->health_score !== null ? $ls->health_score . '/10' : '—',
                'pct' => $ls->health_score !== null ? (int) ($ls->health_score * 10) : 0,
                'bar' => $this->barClass($ls->health_score !== null ? (int) ($ls->health_score * 10) : 0),
                'val' => $this->valClass($ls->health_score !== null ? (int) ($ls->health_score * 10) : 0),
                'icon' => 'droplet',
            ],
        ];
    }

    private function complianceSeries(PatientRecord $patient): array
    {
        $ls = $patient->lifestyleAssessment;
        $base = $ls?->health_score !== null ? (int) ($ls->health_score * 10) : 60;

        return collect(range(0, 6))
            ->map(fn($i) => max(20, min(100, $base + (($i % 3) * 5) - 5)))
            ->all();
    }

    private function barClass(int $pct): string
    {
        if ($pct >= 70) {
            return 'mbar-green';
        }
        if ($pct >= 40) {
            return 'mbar-amber';
        }

        return 'mbar-red';
    }

    private function valClass(int $pct): string
    {
        if ($pct >= 70) {
            return 'mval-green';
        }
        if ($pct >= 40) {
            return 'mval-amber';
        }

        return 'mval-red';
    }
}
