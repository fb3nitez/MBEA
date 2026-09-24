<?php

namespace App\Http\Controllers;

use App\Services\CoachUpdateService;
use App\Services\LifeCoachService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LifecoachController extends Controller
{
    public function __construct(private LifeCoachService $lifeCoachService, private CoachUpdateService $coachUpdateService) {}

    public function dashboard(): View
    {
        $this->coachUpdateService->sync(auth()->user());
        $stats = $this->lifeCoachService->dashboardStats();
        $updates = $this->coachUpdateService->feed(auth()->user());
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
            'updates' => $updates,
        ]);
    }

    public function updates(Request $request): JsonResponse
    {
        return response()->json($this->coachUpdateService->feed(auth()->user(), $request->string('type')->toString() ?: null, 20, max(0, $request->integer('offset', 0))));
    }

    public function markUpdateRead(int $id): JsonResponse
    {
        $this->coachUpdateService->markRead(auth()->user(), $id);

        return response()->json(['message' => 'Update marked as read.']);
    }

    public function markAllUpdatesRead(): JsonResponse
    {
        $this->coachUpdateService->markAllRead(auth()->user());

        return response()->json(['message' => 'Updates marked as read.']);
    }

    public function dismissUpdate(int $id): JsonResponse
    {
        $this->coachUpdateService->dismiss(auth()->user(), $id);

        return response()->json(['message' => 'Update dismissed.']);
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
        $coach = $this->coachProfile();

        return view('lifecoach.profile', [
            'coach' => $coach,
            'patientOptions' => $this->lifeCoachService->patientOptions(),
            'notes' => $this->lifeCoachService->getNotes()
                ->map(fn ($n) => $this->lifeCoachService->noteToArray($n))
                ->values(),
            'stats' => $this->lifeCoachService->dashboardStats(),
            'activity' => $this->lifeCoachService->profileActivity()['items'],
        ]);
    }

    public function updateProfileAccount(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
            'phone' => ['nullable', 'regex:/^09\d{2} \d{3} \d{4}$/'],
            'bio' => ['nullable', 'string', 'max:250'],
        ]);

        $user = $this->lifeCoachService->currentCoach();
        $user->update($data);

        return response()->json(['message' => 'Account details saved.', 'coach' => $this->coachProfile()]);
    }

    public function updateProfileSecurity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->lifeCoachService->currentCoach();
        $user->update(['password' => $data['password']]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function updateProfileNotifications(Request $request): JsonResponse
    {
        $data = $request->validate([
            'task_reminders' => ['required', 'boolean'],
            'patient_assignments' => ['required', 'boolean'],
            'note_followups' => ['required', 'boolean'],
        ]);

        $user = $this->lifeCoachService->currentCoach();
        $user->update(['notification_preferences' => $data]);

        return response()->json(['message' => 'Notification preferences saved.', 'preferences' => $data]);
    }

    public function uploadProfileAvatar(Request $request): JsonResponse
    {
        $image = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ])['avatar'];

        $user = $this->lifeCoachService->currentCoach();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }
        $path = $image->store('profile-avatars', 'public');
        $user->update(['avatar_path' => $path]);

        return response()->json(['message' => 'Profile photo updated.', 'avatar_url' => asset('storage/'.$path)]);
    }

    public function profileActivity(Request $request): JsonResponse
    {
        $offset = max(0, $request->integer('offset', 0));

        return response()->json($this->lifeCoachService->profileActivity(offset: $offset));
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

    public function updateGoal(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'target_date' => ['nullable', 'date'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $goal = $this->lifeCoachService->updateGoal($data, $id);

        return response()->json([
            'message' => 'Goal updated.',
            'goal' => $this->lifeCoachService->goalToArray($goal),
        ]);
    }

    public function updateGoalProgress(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'weekly_checkins' => ['nullable', 'array'],
            'weekly_checkins.*' => ['boolean'],
        ]);

        $goal = null;
        if (array_key_exists('weekly_checkins', $data)) {
            $goal = $this->lifeCoachService->updateGoal([
                'weekly_checkins' => $data['weekly_checkins'],
                'progress' => $data['progress'] ?? null,
            ], $id);
        } else {
            $goal = $this->lifeCoachService->updateGoalProgress($id, (int) ($data['progress'] ?? 0));
        }

        return response()->json([
            'message' => 'Goal progress updated.',
            'goal' => $this->lifeCoachService->goalToArray($goal),
        ]);
    }

    public function destroyGoal(int $id): JsonResponse
    {
        $this->lifeCoachService->deleteGoal($id);

        return response()->json([
            'message' => 'Goal deleted.',
        ]);
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
            'clinic' => 'MB.EA Wellness Center',
            'email' => $user->email,
            'phone' => $user->phone,
            'bio' => $user->bio,
            'avatar_url' => $user->avatar_path ? asset('storage/'.$user->avatar_path) : null,
            'notification_preferences' => $user->notification_preferences ?: [
                'task_reminders' => true,
                'patient_assignments' => true,
                'note_followups' => true,
            ],
            'created_at' => optional($user->created_at)->format('M j, Y'),
            'license' => 'LC-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
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
