<?php

use App\Models\ClinicalTemplate;
use App\Models\LifestyleAssessment;
use App\Models\PatientRecord;
use App\Services\PatientService;

it('searches patients by name and returns suggestions when query is empty', function () {
    PatientRecord::whereIn('fullname', ['Alpha Search Patient', 'Beta Search Patient'])->delete();

    PatientRecord::create([
        'fullname' => 'Alpha Search Patient',
        'birthday' => now()->subYears(30)->toDateString(),
        'sex' => 'female',
    ]);

    PatientRecord::create([
        'fullname' => 'Beta Search Patient',
        'birthday' => now()->subYears(28)->toDateString(),
        'sex' => 'male',
    ]);

    $service = app(PatientService::class);

    $suggestions = $service->searchPatients(null, 12);
    expect($suggestions->count())->toBeGreaterThan(0);

    $matches = $service->searchPatients('Alpha Search', 12);
    expect($matches->pluck('name')->all())->toContain('Alpha Search Patient')
        ->and($matches->pluck('name')->all())->not->toContain('Beta Search Patient');
});

it('seeds default clinical templates and supports create/update/delete', function () {
    ClinicalTemplate::query()->delete();

    $service = app(PatientService::class);
    $templates = $service->getClinicalTemplates();

    expect($templates->where('type', 'rx')->count())->toBeGreaterThan(0)
        ->and($templates->where('type', 'dx')->count())->toBeGreaterThan(0);

    $created = $service->createClinicalTemplate([
        'type' => 'rx',
        'name' => 'Custom Anxiety Rx',
        'tag' => 'Anxiety',
        'description' => 'Escitalopram starter',
        'diag' => 'F41.1',
        'meds' => [['name' => 'Escitalopram', 'dose' => '10mg', 'freq' => ['Morning'], 'qty' => 30]],
    ]);

    expect($created->name)->toBe('Custom Anxiety Rx')
        ->and($created->payload['meds'][0]['name'])->toBe('Escitalopram');

    $updated = $service->updateClinicalTemplate($created, [
        'name' => 'Custom Anxiety Rx Updated',
        'meds' => [['name' => 'Escitalopram', 'dose' => '20mg', 'freq' => ['Morning'], 'qty' => 30]],
    ]);

    expect($updated->name)->toBe('Custom Anxiety Rx Updated')
        ->and($updated->payload['meds'][0]['dose'])->toBe('20mg');

    $service->deleteClinicalTemplate($updated);
    expect(ClinicalTemplate::find($created->id))->toBeNull();
});

it('returns lifestyle patients with assessment payloads for monitoring', function () {
    $patient = PatientRecord::create([
        'fullname' => 'Lifestyle Monitor Patient',
        'birthday' => now()->subYears(32)->toDateString(),
        'sex' => 'female',
    ]);

    LifestyleAssessment::create([
        'patient_record_id' => $patient->id,
        'health_score' => 7,
        'sleep_hours' => 7,
        'exercise_frequency' => '3 times / week',
        'fruits_veg_servings' => '2-3 servings',
        'phq_feeling_down' => 'Several days',
        'motivation_level' => 'Moderate',
    ]);

    $rows = app(PatientService::class)->getLifestylePatients();
    $match = $rows->firstWhere('id', $patient->id);

    expect($match)->not->toBeNull()
        ->and($match['name'])->toBe('Lifestyle Monitor Patient')
        ->and($match['lifestyle_assessment']->sleep_hours)->toBe(7);
});
