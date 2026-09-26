<?php

use App\Models\CoachingGoal;
use App\Models\PatientRecord;
use App\Models\User;
use App\Services\LifeCoachService;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

function makeLifeCoachUser(string $email = 'coach-test@medcare.ph'): User
{
    Role::findOrCreate('lifecoach');

    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => 'Test Life Coach',
            'password' => Hash::make('password'),
        ]
    );

    if (! $user->hasRole('lifecoach')) {
        $user->assignRole('lifecoach');
    }

    return $user;
}

function makeAssignedPatient(User $coach, string $name = 'Assigned Coach Patient'): PatientRecord
{
    return PatientRecord::create([
        'fullname' => $name,
        'birthday' => now()->subYears(30)->toDateString(),
        'sex' => 'female',
        'marital_status' => 'single',
        'chief_complaint' => 'Stress management',
        'life_coach_id' => $coach->id,
    ]);
}

it('lists only patients assigned to the current life coach', function () {
    $coach = makeLifeCoachUser('coach-a@medcare.ph');
    $other = makeLifeCoachUser('coach-b@medcare.ph');

    $mine = makeAssignedPatient($coach, 'My Coach Patient');
    makeAssignedPatient($other, 'Other Coach Patient');

    $this->actingAs($coach);

    $patients = app(LifeCoachService::class)->getAssignedPatients();

    expect($patients->pluck('id')->all())->toContain($mine->id)
        ->and($patients->pluck('fullname')->all())->not->toContain('Other Coach Patient');
});

it('includes intake employment status in the assigned patient view data', function () {
    $coach = makeLifeCoachUser('coach-employment@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Employment Status Patient');
    $patient->update(['employment_status' => 'student']);

    $this->actingAs($coach);

    $data = app(LifeCoachService::class)->patientToArray($patient);

    expect($data['employment_status'])->toBe('student')
        ->and(collect($data['intake']['personal'])->firstWhere('label', 'Employment Status')['value'])
        ->toBe('Student');
});

it('creates coaching notes for assigned patients only', function () {
    $coach = makeLifeCoachUser('coach-notes@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Note Patient');

    $this->actingAs($coach);

    $note = app(LifeCoachService::class)->createNote([
        'patient_record_id' => $patient->id,
        'session_type' => 'Follow-up',
        'body' => 'Patient improving sleep routine.',
    ]);

    expect($note->body)->toBe('Patient improving sleep routine.')
        ->and($note->life_coach_id)->toBe($coach->id)
        ->and(app(LifeCoachService::class)->getNotes()->pluck('body')->all())
        ->toContain('Patient improving sleep routine.');
});

it('creates and toggles coaching tasks', function () {
    $coach = makeLifeCoachUser('coach-tasks@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Task Patient');

    $this->actingAs($coach);

    $service = app(LifeCoachService::class);
    $task = $service->createTask([
        'patient_record_id' => $patient->id,
        'description' => 'Review breathing exercises',
        'priority' => 'High',
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    $toggled = $service->toggleTask($task->id, true);

    expect($toggled->is_done)->toBeTrue()
        ->and($toggled->completed_at)->not->toBeNull();
});

it('schedules a follow-up for an assigned patient', function () {
    $coach = makeLifeCoachUser('coach-sched@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Schedule Patient');

    $this->actingAs($coach);

    $schedule = app(LifeCoachService::class)->createSchedule([
        'patient_record_id' => $patient->id,
        'topic' => 'Sleep hygiene review',
        'date' => now()->toDateString(),
        'time' => '14:00',
    ]);

    expect($schedule->topic)->toBe('Sleep hygiene review')
        ->and($schedule->life_coach_id)->toBe($coach->id);
});

it('creates a coaching goal for an assigned patient', function () {
    $coach = makeLifeCoachUser('coach-goals@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Goal Patient');

    $this->actingAs($coach);

    $goal = app(LifeCoachService::class)->createGoal([
        'patient_record_id' => $patient->id,
        'title' => 'Walk 30 minutes daily',
        'category' => 'Exercise',
        'description' => 'Morning walk before work',
        'target_date' => now()->addMonth()->toDateString(),
    ]);

    expect($goal->title)->toBe('Walk 30 minutes daily')
        ->and($goal->progress)->toBe(0);
});

it('updates and deletes a coaching goal and synchronises it into habit progress', function () {
    $coach = makeLifeCoachUser('coach-goals-edit@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Editable Goal Patient');

    $this->actingAs($coach);

    $service = app(LifeCoachService::class);
    $goal = $service->createGoal([
        'patient_record_id' => $patient->id,
        'title' => 'Hydrate daily',
        'category' => 'Nutrition',
        'description' => 'Drink water during the day',
        'target_date' => now()->addWeek()->toDateString(),
        'progress' => 0,
    ]);

    $updated = $service->updateGoal([
        'title' => 'Hydrate daily and track water intake',
        'category' => 'Nutrition',
        'description' => 'Drink water during the day and note intake',
        'target_date' => now()->addWeek()->toDateString(),
        'progress' => 4,
    ], $goal->id);

    expect($updated->title)->toBe('Hydrate daily and track water intake')
        ->and((int) $updated->progress)->toBe(4);

    $service->updateGoalProgress($goal->id, 55);
    $fresh = $service->findGoalById($goal->id);

    expect((int) $fresh->progress)->toBe(55)
        ->and($service->goalToArray($fresh)['prog'])->toBe(55);

    $service->deleteGoal($goal->id);

    expect(CoachingGoal::whereKey($goal->id)->exists())->toBeFalse();
});

it('renders lifecoach dashboard with assigned patient data', function () {
    $coach = makeLifeCoachUser('coach-dash@medcare.ph');
    makeAssignedPatient($coach, 'Dashboard Patient');

    $this->actingAs($coach)
        ->get(route('lifecoach.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Patient', false);
});
