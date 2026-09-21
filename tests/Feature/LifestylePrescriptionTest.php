<?php

use App\Models\LifestylePrescription;
use App\Models\PatientRecord;
use App\Models\User;
use App\Services\PatientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function makePsychiatristUser(string $email = 'lifecycle-psych-test@medcare.ph'): User
{
    Role::findOrCreate('psychiatrist');

    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => 'Dr. Test Psychiatrist',
            'password' => Hash::make('password'),
            'license_no' => 'LIC-0001',
        ]
    );

    if (! $user->hasRole('psychiatrist')) {
        $user->assignRole('psychiatrist');
    }

    return $user;
}

function makeLifeCoachUserForRx(string $email = 'lifecycle-coach-test@medcare.ph'): User
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

function makeLifestyleRxPatient(string $name = 'Lifestyle Rx Patient'): PatientRecord
{
    return PatientRecord::create([
        'fullname' => $name,
        'birthday' => now()->subYears(28)->toDateString(),
        'sex' => 'female',
        'marital_status' => 'single',
    ]);
}

function validLifestylePayload(array $overrides = []): array
{
    return array_merge([
        'focus' => 'Insomnia / poor sleep',
        'items' => [
            [
                'category' => 'sleep',
                'title' => 'Fixed wake-up time',
                'target' => '7-8 h sleep',
                'frequency' => 'Daily',
                'duration' => '4 weeks',
                'instructions' => 'Wake at the same time every day.',
            ],
        ],
        'notes' => 'Review at next consult.',
        'status' => 'Draft',
        'follow_up_date' => now()->addWeeks(4)->toDateString(),
    ], $overrides);
}

it('allows a psychiatrist to save a lifestyle prescription', function () {
    $psychiatrist = makePsychiatristUser('lifecycle-save@medcare.ph');
    $patient = makeLifestyleRxPatient('Lifestyle Save Patient');

    $response = $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.lifestyle-prescriptions.store', $patient->id), validLifestylePayload());

    $response->assertCreated()
        ->assertJsonPath('message', 'Lifestyle prescription saved.')
        ->assertJsonPath('prescription.focus', 'Insomnia / poor sleep')
        ->assertJsonPath('prescription.items.0.category', 'sleep')
        ->assertJsonPath('prescription.items.0.title', 'Fixed wake-up time')
        ->assertJsonPath('prescription.prescribed_by', $psychiatrist->id);

    $row = LifestylePrescription::where('patient_record_id', $patient->id)->first();
    expect($row)->not->toBeNull()
        ->and($row->items[0]['target'])->toBe('7-8 h sleep')
        ->and($row->status)->toBe('Draft')
        ->and($row->follow_up_date->toDateString())->toBe(now()->addWeeks(4)->toDateString());
});

it('denies lifecoaches from saving lifestyle prescriptions', function () {
    $coach = makeLifeCoachUserForRx('lifecycle-coach-deny@medcare.ph');
    $patient = makeLifestyleRxPatient('Lifestyle Coach Deny Patient');

    $this->actingAs($coach)
        ->postJson(route('psychiatrist.lifestyle-prescriptions.store', $patient->id), validLifestylePayload())
        ->assertForbidden();

    expect(LifestylePrescription::count())->toBe(0);
});

it('denies guests from saving lifestyle prescriptions', function () {
    $patient = makeLifestyleRxPatient('Lifestyle Guest Deny Patient');

    // The app's exception handler only renders JSON for api/*, so guests get
    // redirected to the login screen rather than a 401 JSON body.
    $this->postJson(route('psychiatrist.lifestyle-prescriptions.store', $patient->id), validLifestylePayload())
        ->assertRedirect(route('login'));

    expect(LifestylePrescription::count())->toBe(0);
});

it('rejects an empty items array', function () {
    $psychiatrist = makePsychiatristUser('lifecycle-empty@medcare.ph');
    $patient = makeLifestyleRxPatient('Lifestyle Empty Patient');

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.lifestyle-prescriptions.store', $patient->id), validLifestylePayload([
            'items' => [],
        ]))
        ->assertInvalid(['items']);

    expect(LifestylePrescription::count())->toBe(0);
});

it('rejects an invalid category', function () {
    $psychiatrist = makePsychiatristUser('lifecycle-category@medcare.ph');
    $patient = makeLifestyleRxPatient('Lifestyle Category Patient');

    $payload = validLifestylePayload();
    $payload['items'][0]['category'] = 'meditation';

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.lifestyle-prescriptions.store', $patient->id), $payload)
        ->assertInvalid(['items.0.category']);

    expect(LifestylePrescription::count())->toBe(0);
});

it('keeps multiple lifestyle prescriptions per patient instead of overwriting', function () {
    $psychiatrist = makePsychiatristUser('lifecycle-history@medcare.ph');
    $patient = makeLifestyleRxPatient('Lifestyle History Patient');

    $route = route('psychiatrist.lifestyle-prescriptions.store', $patient->id);

    $this->actingAs($psychiatrist)->postJson($route, validLifestylePayload([
        'focus' => 'Insomnia / poor sleep',
    ]))->assertCreated();

    $this->actingAs($psychiatrist)->postJson($route, validLifestylePayload([
        'focus' => 'MDD — behavioural activation',
    ]))->assertCreated();

    $rows = $patient->lifestylePrescriptions()->get();

    expect($rows)->toHaveCount(2)
        ->and($rows->pluck('focus')->all())->toContain('Insomnia / poor sleep')
        ->and($rows->pluck('focus')->all())->toContain('MDD — behavioural activation')
        ->and(app(PatientService::class)->getLifestylePrescriptions($patient)->count())->toBe(2);
});

it('returns 404 when the patient does not exist', function () {
    $psychiatrist = makePsychiatristUser('lifecycle-404@medcare.ph');

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.lifestyle-prescriptions.store', 999999), validLifestylePayload())
        ->assertNotFound();

    expect(LifestylePrescription::count())->toBe(0);
});

it('allows psychiatrists to save clinical templates of type lx', function () {
    $psychiatrist = makePsychiatristUser('lifecycle-template@medcare.ph');

    $response = $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.templates.store'), [
            'type' => 'lx',
            'name' => 'Custom Wind-Down Plan',
            'tag' => 'Stress',
            'description' => 'Breathing and stretching',
            'focus' => 'Stress / mindfulness',
            'items' => [
                [
                    'category' => 'stress',
                    'title' => 'Guided breathing',
                    'target' => '10 minutes',
                    'frequency' => 'Nightly',
                    'duration' => '4 weeks',
                    'instructions' => 'Use a guided recording.',
                ],
            ],
        ]);

    $response->assertCreated()
        ->assertJsonPath('template.type', 'lx')
        ->assertJsonPath('template.payload.items.0.category', 'stress');
});
