<?php

use App\Models\PatientRecord;
use App\Services\PatientService;

it('stores a biopsychosocial assessment for a patient', function () {
    $patient = PatientRecord::create([
        'fullname' => 'Assessment Patient',
        'birthday' => now()->subYears(30)->toDateString(),
        'sex' => 'female',
    ]);

    $service = app(PatientService::class);
    $assessment = $service->saveAssessment($patient, [
        'biological' => ['bp' => '120/80'],
        'psychological' => ['mood' => 'Calm and cooperative'],
    ]);

    expect($assessment->patient_record_id)->toBe($patient->id)
        ->and($assessment->assessment_data['biological']['bp'])->toBe('120/80')
        ->and($assessment->assessment_data['psychological']['mood'])->toBe('Calm and cooperative');
});

it('merges assessment sections without wiping other sections', function () {
    $patient = PatientRecord::create([
        'fullname' => 'Merge Assessment Patient',
        'birthday' => now()->subYears(29)->toDateString(),
        'sex' => 'female',
    ]);

    $service = app(PatientService::class);
    $service->saveAssessment($patient, [
        'biological' => ['bp' => '118/76', 'cc' => 'Anxiety'],
        'psychological' => ['mood' => 'Anxious'],
        'social' => ['living' => 'Alone'],
        'spiritual' => ['beliefs' => 'Faith-based'],
        'prayer_points' => [['date' => 'Jul 21, 2026', 'text' => 'Pray for peace', 'author' => 'Dr. Maria Santos']],
        'intervention' => ['plan' => 'CBT + SSRI'],
    ]);

    $updated = $service->saveAssessment($patient, [
        'intervention' => [
            'plan' => 'Updated plan',
            'timeline' => [['date' => 'Jul 21, 2026', 'text' => 'Started Sertraline']],
        ],
        'prayer_points' => [['date' => 'Jul 21, 2026', 'text' => 'Pray for rest', 'author' => 'Dr. Maria Santos']],
    ]);

    $data = $updated->assessment_data;
    expect($data['biological']['bp'])->toBe('118/76')
        ->and($data['psychological']['mood'])->toBe('Anxious')
        ->and($data['social']['living'])->toBe('Alone')
        ->and($data['spiritual']['beliefs'])->toBe('Faith-based')
        ->and($data['intervention']['plan'])->toBe('Updated plan')
        ->and($data['prayer_points'][0]['text'])->toBe('Pray for rest');
});

it('paginates assessment list without embedding full assessment payloads', function () {
    $patient = PatientRecord::create([
        'fullname' => 'Light Assessment Patient',
        'birthday' => now()->subYears(40)->toDateString(),
        'sex' => 'male',
        'primary_diagnosis' => 'GAD',
    ]);

    app(PatientService::class)->saveAssessment($patient, [
        'biological' => ['cc' => 'Worry and insomnia'],
    ]);

    $page = app(PatientService::class)->getPaginatedAssessmentPatients(12, 'Light Assessment');
    $row = collect($page->items())->firstWhere('id', $patient->id);

    expect($row)->not->toBeNull()
        ->and($row['summary'])->toBe('Worry and insomnia')
        ->and($row)->not->toHaveKey('assessment');
});

it('stores a prescription for a patient', function () {
    $patient = PatientRecord::create([
        'fullname' => 'Prescription Patient',
        'birthday' => now()->subYears(35)->toDateString(),
        'sex' => 'male',
    ]);

    $service = app(PatientService::class);
    $prescription = $service->savePrescription($patient, [
        'diagnosis' => 'GAD',
        'medications' => [
            ['name' => 'Sertraline', 'dose' => '50mg', 'frequency' => 'Daily'],
        ],
    ]);

    expect($prescription->patient_record_id)->toBe($patient->id)
        ->and($prescription->diagnosis)->toBe('GAD')
        ->and($prescription->medications[0]['name'])->toBe('Sertraline');
});
