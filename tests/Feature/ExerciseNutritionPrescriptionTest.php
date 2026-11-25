<?php

use App\Models\ExerciseNutritionPrescription;
use App\Models\PatientRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

function exerciseNutritionPsychiatrist(): User
{
    Role::findOrCreate('psychiatrist');

    $psychiatrist = User::create([
        'name' => 'Exercise Nutrition Psychiatrist',
        'email' => 'exercise-nutrition-psychiatrist@example.test',
        'password' => Hash::make('password'),
    ]);
    $psychiatrist->assignRole('psychiatrist');

    return $psychiatrist;
}

it('saves exercise and nutrition form answers with patient snapshots and today date', function () {
    $psychiatrist = exerciseNutritionPsychiatrist();
    $patient = PatientRecord::create([
        'fullname' => 'Exercise Nutrition Patient',
        'birthday' => now()->subYears(32)->toDateString(),
        'sex' => 'female',
        'marital_status' => 'single',
    ]);
    $answers = [
        'goals' => ['More energy'],
        'tracker' => [
            [today()->toDateString(), 'Walk, 20 minutes', 4, 6, 7, 'Felt better'],
        ],
    ];

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.exercise-nutrition-prescriptions.store'), [
            'type' => 'exercise',
            'patient_record_id' => $patient->id,
            'patient_name' => 'Editable Patient Name',
            'patient_age' => 33,
            'patient_sex' => 'Other',
            'prescription_date' => now()->subDay()->toDateString(),
            'data' => $answers,
        ])
        ->assertCreated()
        ->assertJsonPath('prescription.type', 'exercise')
        ->assertJsonPath('prescription.patient_name', 'Editable Patient Name')
        ->assertJsonPath('prescription_date', today()->toDateString());

    $record = ExerciseNutritionPrescription::firstOrFail();
    expect($record->patient_record_id)->toBe($patient->id)
        ->and($record->prescribed_by)->toBe($psychiatrist->id)
        ->and($record->patient_age)->toBe(33)
        ->and($record->patient_sex)->toBe('Other')
        ->and($record->prescription_date->toDateString())->toBe(today()->toDateString())
        ->and($record->form_data['tracker'][0][1])->toBe('Walk, 20 minutes')
        ->and($record->form_data['goals'])->toBe(['More energy']);

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.exercise-nutrition-prescriptions.store'), [
            'type' => 'nutrition',
            'patient_name' => 'Manual Entry',
            'patient_age' => 28,
            'patient_sex' => 'Female',
            'data' => ['allergy' => 'Peanuts'],
        ])
        ->assertCreated()
        ->assertJsonPath('prescription.type', 'nutrition');

    expect(ExerciseNutritionPrescription::count())->toBe(2)
        ->and(ExerciseNutritionPrescription::latest('id')->first()->patient_record_id)->toBeNull()
        ->and(ExerciseNutritionPrescription::latest('id')->first()->form_data['allergy'])->toBe('Peanuts');
});

it('shows exercise and nutrition as prescription tabs with no editable prescription date or prescriber field', function () {
    $psychiatrist = exerciseNutritionPsychiatrist();

    $this->actingAs($psychiatrist)
        ->get(route('psychiatrist.prescriptions'))
        ->assertOk()
        ->assertSee('Exercise Prescription')
        ->assertSee('Nutrition Prescription')
        ->assertSee('exercise-prescription-app')
        ->assertSee('exercise_nutrition_prescriptions.js')
        ->assertDontSee('Prescribed by:')
        ->assertDontSee('id="exercise-date"')
        ->assertDontSee('id="nutrition-date"');
});

it('rejects invalid prescription types and more than three goals', function () {
    $psychiatrist = exerciseNutritionPsychiatrist();
    $payload = [
        'type' => 'exercise',
        'patient_name' => 'Goal Limit Patient',
        'patient_age' => 30,
        'patient_sex' => 'Female',
        'data' => ['goals' => ['One', 'Two', 'Three', 'Four']],
    ];

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.exercise-nutrition-prescriptions.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data.goals']);

    $payload['type'] = 'other';
    $payload['data']['goals'] = [];
    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.exercise-nutrition-prescriptions.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);

    expect(ExerciseNutritionPrescription::count())->toBe(0);
});
