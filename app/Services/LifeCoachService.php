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

    public function getAssignedPatients(?int $coachId = null): Collection
    {
        $coachId ??= $this->currentCoach()->id;

        return PatientRecord::with(['lifeCoach', 'lifestyleAssessment', 'prescriptions'])
            ->where('life_coach_id', $coachId)
            ->orderBy('fullname')
            ->get();
    }

    public function findAssignedPatient(int $id, ?int $coachId = null): PatientRecord
    {
        $coachId ??= $this->currentCoach()->id;

        return PatientRecord::with([
            'lifeCoach',
            'lifestyleAssessment',
            'prescriptions',
            'coachingNotes' => fn ($q) => $q->where('life_coach_id', $coachId)->latest(),
            'coachingGoals' => fn ($q) => $q->where('life_coach_id', $coachId)->latest(),
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

        return CoachingGoal::create([
            'patient_record_id' => $data['patient_record_id'],
            'life_coach_id' => $coachId,
            'title' => $data['title'],
            'category' => $data['category'] ?? 'Mental Wellness',
            'description' => $data['description'] ?? null,
            'target_date' => $data['target_date'] ?? null,
            'progress' => (int) ($data['progress'] ?? 0),
        ]);
    }

    public function dashboardStats(?int $coachId = null): array
    {
        $coachId ??= $this->currentCoach()->id;
        $patients = $this->getAssignedPatients($coachId);
        $tasks = $this->getTasks($coachId);
        $pending = $tasks->where('is_done', false);
        $completedThisWeek = $tasks
            ->where('is_done', true)
            ->filter(fn (CoachingTask $t) => $t->completed_at && $t->completed_at->gte(now()->startOfWeek()))
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

    public function patientToArray(PatientRecord $patient): array
    {
        $patient->loadMissing([
            'lifeCoach',
            'lifestyleAssessment',
            'prescriptions',
            'coachingNotes',
            'coachingGoals',
        ]);

        $coachId = $this->currentCoach()->id;

        $notes = $patient->coachingNotes
            ->where('life_coach_id', $coachId)
            ->sortByDesc('created_at')
            ->values()
            ->map(fn (CoachingNote $n) => $this->noteToArray($n));

        $goals = $patient->coachingGoals
            ->where('life_coach_id', $coachId)
            ->sortByDesc('created_at')
            ->values()
            ->map(fn (CoachingGoal $g) => $this->goalToArray($g));

        return [
            'id' => $patient->id,
            'patient_id' => $patient->patient_id,
            'name' => $patient->fullname,
            'age' => $patient->age,
            'sex' => $patient->sex ? ucfirst($patient->sex) : '—',
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
            'habits' => [],
            'habitData' => [],
            'notes' => $notes,
        ];
    }

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
            'title' => $goal->title,
            'cat' => $goal->category,
            'desc' => $goal->description ?? '',
            'date' => $goal->target_date?->format('M j, Y') ?? 'TBD',
            'prog' => (int) $goal->progress,
        ];
    }

    public function patientOptions(?int $coachId = null): SupportCollection
    {
        return $this->getAssignedPatients($coachId)
            ->map(fn (PatientRecord $p) => [
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
            [
                'name' => 'Sleep Quality',
                'value' => $sleep ? $sleep . ' hrs' : '—',
                'pct' => $sleepPct,
                'bar' => $this->barClass($sleepPct),
                'val' => $this->valClass($sleepPct),
                'icon' => 'moon',
            ],
            [
                'name' => 'Exercise',
                'value' => $exercise ?: '—',
                'pct' => $exercisePct,
                'bar' => $this->barClass($exercisePct),
                'val' => $this->valClass($exercisePct),
                'icon' => 'activity',
            ],
            [
                'name' => 'Nutrition',
                'value' => $nutrition ?: '—',
                'pct' => $nutritionPct,
                'bar' => $this->barClass($nutritionPct),
                'val' => $this->valClass($nutritionPct),
                'icon' => 'heart',
            ],
            [
                'name' => 'Mood / Stress',
                'value' => $stress ?: '—',
                'pct' => $stressPct,
                'bar' => $this->barClass(100 - $stressPct),
                'val' => $this->valClass(100 - $stressPct),
                'icon' => 'zap',
            ],
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
            ->map(fn ($i) => max(20, min(100, $base + (($i % 3) * 5) - 5)))
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
