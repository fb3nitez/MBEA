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

it('saves exercise and nutrition form answers with patient snapshots and the selected date', function () {
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
    $selectedDate = now()->subDay()->toDateString();

    $this->actingAs($psychiatrist)
        ->postJson(route('psychiatrist.exercise-nutrition-prescriptions.store'), [
            'type' => 'exercise',
            'patient_record_id' => $patient->id,
            'patient_name' => 'Editable Patient Name',
            'patient_age' => 33,
            'patient_sex' => 'Other',
            'prescription_date' => $selectedDate,
            'data' => $answers,
        ])
        ->assertCreated()
        ->assertJsonPath('prescription.type', 'exercise')
        ->assertJsonPath('prescription.patient_name', 'Editable Patient Name')
        ->assertJsonPath('prescription_date', $selectedDate);

    $record = ExerciseNutritionPrescription::firstOrFail();
    expect($record->patient_record_id)->toBe($patient->id)
        ->and($record->prescribed_by)->toBe($psychiatrist->id)
        ->and($record->patient_age)->toBe(33)
        ->and($record->patient_sex)->toBe('Other')
        ->and($record->prescription_date->toDateString())->toBe($selectedDate)
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

it('shows exercise and nutrition as prescription tabs without a duplicate prescriber field', function () {
    $psychiatrist = exerciseNutritionPsychiatrist();

    $this->actingAs($psychiatrist)
        ->get(route('psychiatrist.prescriptions'))
        ->assertOk()
        ->assertSee('Exercise Prescription')
        ->assertSee('Nutrition Prescription')
        ->assertSee('exercise-prescription-app')
        ->assertSee('exercise_nutrition_prescriptions.js')
        ->assertDontSee('Prescribed by:');
});

it("lists a patient's saved lifestyle prescriptions and opens the selected form", function () {
    $psychiatrist = exerciseNutritionPsychiatrist();
    $patient = PatientRecord::create([
        'fullname' => 'Prescription List Patient',
        'birthday' => now()->subYears(36)->toDateString(),
        'sex' => 'male',
        'marital_status' => 'single',
    ]);
    $exercise = ExerciseNutritionPrescription::create([
        'patient_record_id' => $patient->id,
        'prescribed_by' => $psychiatrist->id,
        'type' => 'exercise',
        'patient_name' => 'Exercise Snapshot Name',
        'patient_age' => 36,
        'patient_sex' => 'Male',
        'prescription_date' => today()->toDateString(),
        'form_data' => ['name' => 'Exercise Snapshot Name', 'age' => '36', 'sex' => 'Male'],
    ]);
    ExerciseNutritionPrescription::create([
        'patient_record_id' => $patient->id,
        'prescribed_by' => $psychiatrist->id,
        'type' => 'nutrition',
        'patient_name' => 'Nutrition Snapshot Name',
        'patient_age' => 36,
        'patient_sex' => 'Male',
        'prescription_date' => today()->subDay()->toDateString(),
        'form_data' => ['name' => 'Nutrition Snapshot Name', 'age' => '36', 'sex' => 'Male'],
    ]);

    $this->actingAs($psychiatrist)
        ->getJson(route('psychiatrist.patients.exercise-nutrition-prescriptions', $patient->id))
        ->assertOk()
        ->assertJsonCount(2, 'prescriptions')
        ->assertJsonPath('prescriptions.0.type', 'exercise')
        ->assertJsonPath('prescriptions.1.type', 'nutrition');

    $this->actingAs($psychiatrist)
        ->get(route('psychiatrist.prescriptions', ['exercise_prescription' => $exercise->id]))
        ->assertOk()
        ->assertSee('selectedExercisePrescription')
        ->assertSee('Exercise Snapshot Name');

    $this->actingAs($psychiatrist)
        ->get(route('psychiatrist.patients'))
        ->assertOk()
        ->assertSee('pm-btn-exercise-prescription')
        ->assertSee('pm-btn-nutrition-prescription')
        ->assertSee('exercise-nutrition-prescription-list-modal');
});

it('opens a saved exercise nutrition prescription in its PDF preview page', function () {
    $psychiatrist = exerciseNutritionPsychiatrist();
    $prescription = ExerciseNutritionPrescription::create([
        'prescribed_by' => $psychiatrist->id,
        'type' => 'exercise',
        'patient_name' => 'PDF Preview Patient',
        'patient_age' => 42,
        'patient_sex' => 'Female',
        'prescription_date' => today()->toDateString(),
        'form_data' => [
            'name' => 'PDF Preview Patient',
            'age' => '42',
            'sex' => 'Female',
            'goals' => ['More energy'],
        ],
    ]);

    $pdfHtml = view('psychiatrist.exercise_nutrition_prescription_pdf', [
        'prescription' => $prescription,
        'prescriberName' => $psychiatrist->name,
    ])->render();

    expect($pdfHtml)
        ->toContain('Prescribed by: ' . $psychiatrist->name)
        ->toContain('Date: ' . today()->format('m / d / Y'));

    $response = $this->actingAs($psychiatrist)
        ->get(route('psychiatrist.exercise-nutrition-prescriptions.pdf', $prescription))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');

    $nutritionPrescription = ExerciseNutritionPrescription::create([
        'prescribed_by' => $psychiatrist->id,
        'type' => 'nutrition',
        'patient_name' => 'PDF Preview Nutrition Patient',
        'patient_age' => 28,
        'patient_sex' => 'Male',
        'prescription_date' => today()->toDateString(),
        'form_data' => [
            'name' => 'PDF Preview Nutrition Patient',
            'age' => '28',
            'sex' => 'Male',
            'allergy' => 'Peanuts',
        ],
    ]);

    $nutritionPdf = $this->get(route('psychiatrist.exercise-nutrition-prescriptions.pdf', $nutritionPrescription))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect(substr($nutritionPdf->getContent(), 0, 4))->toBe('%PDF');
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
