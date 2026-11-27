<?php

use App\Models\CoachingGoal;
use App\Models\PatientIntervention;
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

it('includes patient interventions in the read-only intake data', function () {
    $coach = makeLifeCoachUser('coach-interventions@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Interventions Intake Patient');
    PatientIntervention::create([
        'patient_record_id' => $patient->id,
        'psychiatric_therapy_medication' => 'Therapy plan and medication review',
        'lifestyle_interventions' => 'Exercise and sleep routine',
        'substance_use_rehabilitation' => 'Outpatient rehabilitation referral',
        'spiritual_counseling' => 'Counseling by request',
    ]);

    $this->actingAs($coach);

    $intake = app(LifeCoachService::class)->patientToArray($patient)['intake'];
    $rows = collect($intake['interventions']);

    expect($rows)->toHaveCount(4)
        ->and($rows->firstWhere('label', 'Psychiatric Therapy & Medication')['value'])
        ->toBe('Therapy plan and medication review')
        ->and($rows->firstWhere('label', 'Spiritual Counseling')['value'])
        ->toBe('Counseling by request');
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

it('stores a coach-only journey setup and dated reflections for assigned patients', function () {
    $coach = makeLifeCoachUser('coach-journal@medcare.ph');
    $otherCoach = makeLifeCoachUser('coach-journal-other@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Journal Patient');
    $otherPatient = makeAssignedPatient($otherCoach, 'Other Journal Patient');
    $this->actingAs($coach);

    $url = route('lifecoach.patients.journal-entries.store', $patient->id);
    $this->postJson($url, [
        'kind' => 'setup',
        'cadence' => 'monthly',
        'entry_date' => '2026-11-27',
        'content' => [
            'intentions' => ['Build a steadier sleep routine'],
            'why' => 'More energy for daily life.',
            'pillars' => ['sleep' => ['now' => 'Irregular', 'wish' => 'Rested', 'step' => 'Wind-down alarm']],
            'recovery' => ['vision' => 'Feel more balanced.'],
            'challenge' => ['habit' => 'Read before bed'],
        ],
    ])->assertCreated()->assertJsonPath('entry.kind', 'setup');

    $this->postJson($url, [
        'kind' => 'review',
        'entry_date' => '2026-12-27',
        'content' => [
            'reflection' => ['went_well' => 'Kept the routine three nights.'],
            'challenge' => [
                'days_practiced' => 12,
                'daily_status' => array_merge(array_fill(0, 12, 'met'), array_fill(0, 3, 'not_met'), array_fill(0, 15, null)),
                'rating' => 4,
            ],
        ],
    ])->assertCreated()->assertJsonPath('entry.kind', 'review');

    $this->postJson(route('lifecoach.patients.journal-entries.store', $otherPatient->id), [
        'kind' => 'setup',
        'content' => ['intentions' => ['Not accessible']],
    ])->assertNotFound();

    $entries = app(LifeCoachService::class)->patientToArray($patient)['journalEntries'];
    expect($entries)->toHaveCount(2)
        ->and($entries[0]['kind'])->toBe('review')
        ->and($entries[0]['content']['challenge']['daily_status'])->toHaveCount(30)
        ->and(collect($entries[0]['content']['challenge']['daily_status'])->filter(fn ($state) => $state === 'met')->count())->toBe(12)
        ->and(collect($entries[0]['content']['challenge']['daily_status'])->filter(fn ($state) => $state === 'not_met')->count())->toBe(3)
        ->and($entries[1]['cadence'])->toBe('monthly')
        ->and($entries[1]['content']['intentions'][0])->toBe('Build a steadier sleep routine');
});

it('saves weekly goal practice with the dated journal reflection', function () {
    $coach = makeLifeCoachUser('coach-journal-goal-checkins@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Journal Goal Check-in Patient');
    $goal = app(LifeCoachService::class)->createGoal([
        'patient_record_id' => $patient->id,
        'title' => 'Take an evening walk',
        'category' => 'Exercise',
        'progress' => 0,
    ], $coach->id);
    $this->actingAs($coach);

    $url = route('lifecoach.patients.journal-entries.store', $patient->id);
    $this->postJson($url, [
        'kind' => 'setup',
        'content' => ['intentions' => ['Build a movement routine']],
    ])->assertCreated();

    $checkins = [true, false, true, true, false, false, false];
    $response = $this->postJson($url, [
        'kind' => 'review',
        'entry_date' => '2026-11-27',
        'content' => [
            'reflection' => ['went_well' => 'Made time for short walks.'],
            'goal_checkins' => [[
                'goal_id' => $goal->id,
                'weekly_checkins' => $checkins,
            ]],
        ],
    ])->assertCreated()->assertJsonPath('entry.content.goal_checkins.0.title', 'Take an evening walk');

    $goal->refresh();
    expect($goal->weekly_checkins)->toBe($checkins)
        ->and($goal->progress)->toBe(43)
        ->and($response->json('entry.content.goal_checkins.0.progress'))->toBe(43);
});

it('renders lifecoach dashboard with assigned patient data', function () {
    $coach = makeLifeCoachUser('coach-dash@medcare.ph');
    makeAssignedPatient($coach, 'Dashboard Patient');

    $this->actingAs($coach)
        ->get(route('lifecoach.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Patient', false);
});

it('renders the unified journal workspace for an assigned life coach', function () {
    $coach = makeLifeCoachUser('coach-journal-view@medcare.ph');
    makeAssignedPatient($coach, 'Journal View Patient');

    $this->actingAs($coach)
        ->get(route('lifecoach.patients'))
        ->assertOk()
        ->assertSee('Guided Wellness Journal')
        ->assertSee('Start journey setup')
        ->assertSee('Goals for this journey')
        ->assertSee('Connect each goal to a small weekly practice')
        ->assertSee('Lifestyle context')
        ->assertDontSee('COACH-ONLY WORKSPACE')
        ->assertDontSee('Visible to the assigned Life Coach only');
});

it('includes dietary history in the read-only intake data', function () {
    $coach = makeLifeCoachUser('coach-dietary@medcare.ph');
    $patient = makeAssignedPatient($coach, 'Dietary Intake Patient');
    \App\Models\LifestyleAssessment::create([
        'patient_record_id' => $patient->id,
        'dietary_history' => [
            'overview' => ['diet' => 'Balanced', 'home' => '80%', 'time' => '1', 'foods' => 'None'],
            'day1' => ['meals' => ['breakfast' => 'Eggs', 'am_snack' => 'Fruit', 'lunch' => 'Rice meal', 'pm_snack' => 'Yogurt', 'dinner' => 'Fish']],
            'day2' => ['meals' => ['breakfast' => 'Oats', 'am_snack' => 'Nuts', 'lunch' => 'Chicken', 'pm_snack' => 'Banana', 'dinner' => 'Soup']],
            'day3' => ['meals' => ['breakfast' => 'Toast', 'am_snack' => 'Apple', 'lunch' => 'Salad', 'pm_snack' => 'Cheese', 'dinner' => 'Pasta']],
        ],
    ]);

    $this->actingAs($coach);

    $history = app(LifeCoachService::class)->patientToArray($patient)['intake']['dietary_history'];

    expect($history['overview']['diet'])->toBe('Balanced')
        ->and($history['day1']['meals']['breakfast'])->toBe('Eggs')
        ->and($history['day3']['meals']['dinner'])->toBe('Pasta');
});
