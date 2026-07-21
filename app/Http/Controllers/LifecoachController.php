<?php

namespace App\Http\Controllers;

use App\Services\LifeCoachService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LifecoachController extends Controller
{
    public function __construct(private LifeCoachService $lifeCoachService) {}

    public function dashboard(): View
    {
        $stats = $this->lifeCoachService->dashboardStats();
        $patients = $this->lifeCoachService->getAssignedPatients()
            ->map(fn ($p) => $this->lifeCoachService->patientToArray($p))
            ->values();
        $tasks = $this->lifeCoachService->getTasks()
            ->map(fn ($t) => $this->lifeCoachService->taskToArray($t))
            ->values();
        $schedules = $this->lifeCoachService->getWeekSchedules()
            ->map(fn ($s) => $this->lifeCoachService->scheduleToArray($s))
            ->values();

        return view('lifecoach.dashboard', [
            'stats' => $stats,
            'patients' => $patients,
            'tasks' => $tasks,
            'schedules' => $schedules,
            'patientOptions' => $this->lifeCoachService->patientOptions(),
            'coach' => $this->coachProfile(),
        ]);
    }

    public function patients(): View
    {
        $patients = $this->lifeCoachService->getAssignedPatients()
            ->map(fn ($p) => $this->lifeCoachService->patientToArray($p))
            ->values();

        return view('lifecoach.patients', [
            'patients' => $patients,
            'patientOptions' => $this->lifeCoachService->patientOptions(),
            'coach' => $this->coachProfile(),
        ]);
    }

    public function notes(): View
    {
        $notes = $this->lifeCoachService->getNotes()
            ->map(fn ($n) => $this->lifeCoachService->noteToArray($n))
            ->values();

        return view('lifecoach.notes', [
            'notes' => $notes,
            'patients' => $this->lifeCoachService->getAssignedPatients()
                ->map(fn ($p) => $this->lifeCoachService->patientToArray($p))
                ->values(),
            'patientOptions' => $this->lifeCoachService->patientOptions(),
            'coach' => $this->coachProfile(),
        ]);
    }

    public function tasks(): View
    {
        $tasks = $this->lifeCoachService->getTasks()
            ->map(fn ($t) => $this->lifeCoachService->taskToArray($t))
            ->values();

        return view('lifecoach.tasks', [
            'tasks' => $tasks,
            'patientOptions' => $this->lifeCoachService->patientOptions(),
            'coach' => $this->coachProfile(),
        ]);
    }

    public function profile(): View
    {
        return view('lifecoach.profile', [
            'coach' => $this->coachProfile(),
            'patientOptions' => $this->lifeCoachService->patientOptions(),
            'notes' => $this->lifeCoachService->getNotes()
                ->map(fn ($n) => $this->lifeCoachService->noteToArray($n))
                ->values(),
            'stats' => $this->lifeCoachService->dashboardStats(),
        ]);
    }

    public function showPatient(int $id): JsonResponse
    {
        $patient = $this->lifeCoachService->findAssignedPatient($id);

        return response()->json([
            'patient' => $this->lifeCoachService->patientToArray($patient),
        ]);
    }

    public function storeNote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_record_id' => ['required', 'integer', 'exists:patient_records,id'],
            'session_type' => ['required', 'string', 'max:50'],
            'body' => ['required', 'string'],
        ]);

        $note = $this->lifeCoachService->createNote($data);

        return response()->json([
            'message' => 'Note saved.',
            'note' => $this->lifeCoachService->noteToArray($note),
        ], 201);
    }

    public function destroyNote(int $id): JsonResponse
    {
        $this->lifeCoachService->deleteNote($id);

        return response()->json(['message' => 'Note deleted.']);
    }

    public function storeTask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_record_id' => ['required', 'integer', 'exists:patient_records,id'],
            'description' => ['required', 'string', 'max:500'],
            'priority' => ['required', 'in:High,Medium,Low'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task = $this->lifeCoachService->createTask($data);

        return response()->json([
            'message' => 'Task added.',
            'task' => $this->lifeCoachService->taskToArray($task),
        ], 201);
    }

    public function toggleTask(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'done' => ['required', 'boolean'],
        ]);

        $task = $this->lifeCoachService->toggleTask($id, (bool) $data['done']);

        return response()->json([
            'message' => $task->is_done ? 'Task completed.' : 'Task reopened.',
            'task' => $this->lifeCoachService->taskToArray($task),
        ]);
    }

    public function storeSchedule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_record_id' => ['required', 'integer', 'exists:patient_records,id'],
            'topic' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
        ]);

        $schedule = $this->lifeCoachService->createSchedule($data);

        return response()->json([
            'message' => 'Follow-up scheduled.',
            'schedule' => $this->lifeCoachService->scheduleToArray($schedule),
        ], 201);
    }

    public function storeGoal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_record_id' => ['required', 'integer', 'exists:patient_records,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'target_date' => ['nullable', 'date'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $goal = $this->lifeCoachService->createGoal($data);

        return response()->json([
            'message' => 'Goal added.',
            'goal' => $this->lifeCoachService->goalToArray($goal),
        ], 201);
    }

    private function coachProfile(): array
    {
        $user = $this->lifeCoachService->currentCoach();
        $parts = preg_split('/\s+/', trim($user->name)) ?: [];
        $initials = collect($parts)->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => 'Life Coach',
            'clinic' => 'MedCare Clinic',
            'email' => $user->email,
            'license' => 'LC-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
            'specializations' => [
                'Cognitive Behavioral Coaching',
                'Stress Management',
                'Anxiety Management',
                'Sleep Coaching',
            ],
            'initials' => strtoupper($initials ?: 'LC'),
        ];
    }
}
