<?php

use App\Models\ClinicalTemplate;
use App\Models\PatientRecord;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

const CONVERT_MIGRATION = '2026_09_21_000003_convert_lx_clinical_templates_to_rx';
const DEFAULT_SEED_MIGRATION = '2026_09_21_000004_insert_default_rx_lifestyle_templates';

function makeRxPsychiatristUser(string $email = 'rx-lifestyle-psych@medcare.ph'): User
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

function makeRxCoachUser(string $email = 'rx-lifestyle-coach@medcare.ph'): User
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

function makeRxPatient(string $name = 'Rx Lifestyle Patient'): PatientRecord
{
    return PatientRecord::create([
        'fullname' => $name,
        'birthday' => now()->subYears(30)->toDateString(),
        'sex' => 'male',
        'marital_status' => 'single',
    ]);
}

function rxPayload(array $overrides = []): array
{
    return array_merge([
        'diagnosis' => 'GAD',
        'medications' => [
            ['name' => 'Sertraline', 'dose' => '50mg', 'frequency' => 'Morning', 'qty' => 30],
        ],
        'lifestyle_interventions' => [
            [
                'category' => 'exercise',
                'title' => 'Walking',
                'target' => '150 min/week',
                'frequency' => '5x/week',
                'duration' => '8 weeks',
                'instructions' => 'Start easy.',
            ],
        ],
        'notes' => 'Review in 2 weeks.',
        'status' => 'Draft',
    ], $overrides);
}

function rerunMigration(string $migration): void
{
    DB::table('migrations')->where('migration', $migration)->delete();

    Artisan::call('migrate', [
        '--path' => 'database/migrations/'.$migration.'.php',
        '--force' => true,
    ]);
}

it('saves a prescription with medications and lifestyle interventions', function () {
    $psychiatrist = makeRxPsychiatristUser();
    $patient = makeRxPatient('Rx Save Both');

    $response = $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.prescriptions.store', $patient->id), rxPayload());

    $response->assertOk()
        ->assertJsonPath('message', 'Prescription saved.')
        ->assertJsonPath('prescription.lifestyle_interventions.0.category', 'exercise')
        ->assertJsonPath('prescription.lifestyle_interventions.0.title', 'Walking')
        ->assertJsonPath('prescription.medications.0.name', 'Sertraline');

    $row = Prescription::where('patient_record_id', $patient->id)->first();
    expect($row)->not->toBeNull()
        ->and($row->lifestyle_interventions[0]['target'])->toBe('150 min/week')
        ->and($row->medications[0]['name'])->toBe('Sertraline');
});

it('saves a prescription with only lifestyle interventions', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-only-lifestyle@medcare.ph');
    $patient = makeRxPatient('Rx Only Lifestyle');

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.prescriptions.store', $patient->id), rxPayload([
            'medications' => [],
        ]))
        ->assertOk()
        ->assertJsonPath('prescription.lifestyle_interventions.0.title', 'Walking')
        ->assertJsonPath('prescription.medications', []);

    $row = Prescription::where('patient_record_id', $patient->id)->first();
    expect($row->lifestyle_interventions)->toHaveCount(1)
        ->and($row->medications)->toBe([]);
});

it('rejects an invalid lifestyle category', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-bad-category@medcare.ph');
    $patient = makeRxPatient('Rx Bad Category');

    $payload = rxPayload();
    $payload['lifestyle_interventions'][0]['category'] = 'meditation';

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.prescriptions.store', $patient->id), $payload)
        ->assertInvalid(['lifestyle_interventions.0.category']);
});

it('rejects a lifestyle item without a title', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-missing-title@medcare.ph');
    $patient = makeRxPatient('Rx Missing Title');

    $payload = rxPayload();
    unset($payload['lifestyle_interventions'][0]['title']);

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.prescriptions.store', $patient->id), $payload)
        ->assertInvalid(['lifestyle_interventions.0.title']);
});

it('rejects a medication with an invalid quantity', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-bad-medication@medcare.ph');
    $patient = makeRxPatient('Rx Bad Medication');

    $payload = rxPayload();
    $payload['medications'][0]['qty'] = 0;

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.prescriptions.store', $patient->id), $payload)
        ->assertInvalid(['medications.0.qty']);
});

it('overwrites the same prescription row on a second save', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-update@medcare.ph');
    $patient = makeRxPatient('Rx Update Same Row');

    $route = route('psychiatrist.prescriptions.store', $patient->id);

    $this->actingAs($psychiatrist)->postJson($route, rxPayload())->assertOk();

    $this->actingAs($psychiatrist)->postJson($route, rxPayload([
        'lifestyle_interventions' => [
            ['category' => 'sleep', 'title' => 'Fixed wake-up time', 'target' => '7-8 h sleep'],
        ],
    ]))->assertOk();

    expect(Prescription::where('patient_record_id', $patient->id)->count())->toBe(1);

    $row = Prescription::where('patient_record_id', $patient->id)->first();
    expect($row->lifestyle_interventions)->toHaveCount(1)
        ->and($row->lifestyle_interventions[0]['category'])->toBe('sleep');
});

it('denies lifecoaches from saving prescriptions', function () {
    $coach = makeRxCoachUser();
    $patient = makeRxPatient('Rx Coach Deny');

    $this->actingAs($coach)
        ->postJson(route('psychiatrist.prescriptions.store', $patient->id), rxPayload())
        ->assertForbidden();

    expect(Prescription::count())->toBe(0);
});

it('denies guests from saving prescriptions', function () {
    $patient = makeRxPatient('Rx Guest Deny');

    $this->postJson(route('psychiatrist.prescriptions.store', $patient->id), rxPayload())
        ->assertRedirect(route('login'));

    expect(Prescription::count())->toBe(0);
});

it('creates and updates an rx template with lifestyle interventions', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-template@medcare.ph');

    $created = $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.templates.store'), [
            'type' => 'rx',
            'name' => 'GAD + Walking Plan',
            'tag' => 'Anxiety',
            'description' => 'Sertraline + walking',
            'diag' => 'F41.1',
            'meds' => [['name' => 'Sertraline', 'dose' => '50mg', 'freq' => ['Morning'], 'qty' => 30]],
            'lifestyle' => [
                ['category' => 'exercise', 'title' => 'Walking', 'target' => '150 min/week'],
            ],
        ]);

    $created->assertCreated()
        ->assertJsonPath('template.type', 'rx')
        ->assertJsonPath('template.lifestyle.0.title', 'Walking')
        ->assertJsonPath('template.payload.lifestyle.0.category', 'exercise');

    $id = $created->json('template.id');

    $updated = $this->actingAs($psychiatrist)
        ->putJson(route('psychiatrist.templates.update', $id), [
            'lifestyle' => [
                ['category' => 'sleep', 'title' => 'Fixed wake-up time', 'target' => '7-8 h sleep'],
            ],
        ]);

    $updated->assertOk()
        ->assertJsonPath('template.lifestyle.0.category', 'sleep')
        ->assertJsonPath('template.lifestyle', fn ($lifestyle) => count($lifestyle) === 1);
});

it('creates a lifestyle-only rx template', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-template-lx-only@medcare.ph');

    $created = $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.templates.store'), [
            'type' => 'rx',
            'name' => 'Sleep Hygiene Only',
            'tag' => 'Sleep',
            'lifestyle' => [
                ['category' => 'sleep', 'title' => 'No screens before bed', 'target' => '60 min buffer'],
            ],
        ]);

    $created->assertCreated()
        ->assertJsonPath('template.type', 'rx')
        ->assertJsonPath('template.lifestyle.0.title', 'No screens before bed')
        ->assertJsonPath('template.meds', []);
});

it('returns an empty lifestyle array for old-shaped templates', function () {
    $psychiatrist = makeRxPsychiatristUser('rx-template-old@medcare.ph');

    $created = $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.templates.store'), [
            'type' => 'rx',
            'name' => 'Old Shaped MDD',
            'tag' => 'Depression',
            'diag' => 'F32.1',
            'meds' => [['name' => 'Sertraline', 'dose' => '50mg', 'freq' => ['Morning'], 'qty' => 30]],
        ]);

    $created->assertCreated()
        ->assertJsonPath('template.lifestyle', [])
        ->assertJsonPath('template.meds.0.name', 'Sertraline');
});

it('converts existing lx templates to rx with lifestyle payload', function () {
    ClinicalTemplate::query()->delete();

    ClinicalTemplate::create([
        'type' => 'lx',
        'name' => 'Insomnia — Sleep Hygiene Plan',
        'tag' => 'Sleep',
        'tag_class' => 'tag-lifestyle',
        'description' => 'Old lx template',
        'payload' => [
            'focus' => 'Insomnia / poor sleep',
            'items' => [
                ['category' => 'sleep', 'title' => 'Fixed wake-up time', 'target' => '7-8 h sleep', 'frequency' => 'Daily', 'duration' => '4 weeks', 'instructions' => 'Wake same time.'],
            ],
        ],
        'sort_order' => 1,
    ]);

    rerunMigration(CONVERT_MIGRATION);

    $row = ClinicalTemplate::where('name', 'Insomnia — Sleep Hygiene Plan')->first();

    expect($row)->not->toBeNull()
        ->and($row->type)->toBe('rx')
        ->and($row->payload['diag'])->toBe('Insomnia / poor sleep')
        ->and($row->payload['meds'])->toBe([])
        ->and($row->payload['lifestyle'][0]['title'])->toBe('Fixed wake-up time')
        ->and(ClinicalTemplate::where('type', 'lx')->count())->toBe(0);
});

it('inserts default rx lifestyle templates without duplicating or overwriting', function () {
    ClinicalTemplate::query()->delete();

    ClinicalTemplate::create([
        'type' => 'rx',
        'name' => 'Insomnia — Sleep Hygiene Plan',
        'tag' => 'Sleep',
        'tag_class' => 'tag-lifestyle',
        'description' => 'User-edited version',
        'payload' => ['diag' => 'Custom', 'meds' => [], 'lifestyle' => []],
        'sort_order' => 50,
    ]);

    rerunMigration(DEFAULT_SEED_MIGRATION);

    $insomnia = ClinicalTemplate::where('name', 'Insomnia — Sleep Hygiene Plan')
        ->where('type', 'rx')
        ->get();

    expect($insomnia)->toHaveCount(1)
        ->and($insomnia->first()->description)->toBe('User-edited version')
        ->and(ClinicalTemplate::where('type', 'rx')->where('name', 'Depression — Graded Exercise')->exists())->toBeTrue()
        ->and(ClinicalTemplate::where('type', 'rx')->where('name', 'Mediterranean-Style Nutrition Plan')->exists())->toBeTrue()
        ->and(ClinicalTemplate::where('type', 'rx')->where('name', 'Depression — SSRI + Graded Exercise')->exists())->toBeTrue();
});
