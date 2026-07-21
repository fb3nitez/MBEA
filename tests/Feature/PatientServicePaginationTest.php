<?php

use App\Models\ConsultationSchedule;
use App\Models\PatientRecord;
use App\Services\PatientService;

it('paginates patients in oldest-first order for management views', function () {
    PatientRecord::whereIn('fullname', ['Older Patient', 'Newer Patient'])->delete();

    PatientRecord::create([
        'fullname' => 'Older Patient',
        'birthday' => now()->subYears(30)->toDateString(),
        'sex' => 'female',
    ]);

    PatientRecord::create([
        'fullname' => 'Newer Patient',
        'birthday' => now()->subYears(20)->toDateString(),
        'sex' => 'male',
    ]);

    $page = app(PatientService::class)->getPaginatedPatients(1000);
    $names = collect($page->items())
        ->filter(fn ($item) => in_array($item->fullname, ['Older Patient', 'Newer Patient'], true))
        ->pluck('fullname')
        ->values()
        ->all();

    expect($names)->toBe(['Older Patient', 'Newer Patient']);
});

it('paginates todays patient intakes newest-first', function () {
    PatientRecord::where('fullname', 'like', 'Today Intake %')->delete();

    $older = PatientRecord::create([
        'fullname' => 'Today Intake Older',
        'birthday' => now()->subYears(30)->toDateString(),
        'sex' => 'female',
    ]);
    $older->forceFill([
        'created_at' => now()->subHours(2),
        'updated_at' => now()->subHours(2),
    ])->saveQuietly();

    $newer = PatientRecord::create([
        'fullname' => 'Today Intake Newer',
        'birthday' => now()->subYears(25)->toDateString(),
        'sex' => 'male',
    ]);
    $newer->forceFill([
        'created_at' => now()->subMinutes(10),
        'updated_at' => now()->subMinutes(10),
    ])->saveQuietly();

    $page = app(PatientService::class)->getPaginatedTodayPatients(1000);
    $names = collect($page->items())
        ->filter(fn ($item) => in_array($item->fullname, ['Today Intake Older', 'Today Intake Newer'], true))
        ->pluck('fullname')
        ->values()
        ->all();

    expect($page->total())->toBeGreaterThanOrEqual(2)
        ->and($names)->toBe(['Today Intake Newer', 'Today Intake Older']);

    $older->delete();
    $newer->delete();
});

it('paginates consultations in oldest-first order', function () {
    ConsultationSchedule::query()->delete();
    PatientRecord::where('fullname', 'Consultation Patient')->delete();

    $olderPatient = PatientRecord::create([
        'fullname' => 'Consultation Patient',
        'birthday' => now()->subYears(25)->toDateString(),
        'sex' => 'female',
    ]);

    ConsultationSchedule::create([
        'patient_record_id' => $olderPatient->id,
        'date' => now()->subDay()->toDateString(),
        'time' => '10:00:00',
        'type' => 'Follow-up',
        'status' => 'Scheduled',
    ]);

    ConsultationSchedule::create([
        'patient_record_id' => $olderPatient->id,
        'date' => now()->toDateString(),
        'time' => '14:00:00',
        'type' => 'Initial',
        'status' => 'Scheduled',
    ]);

    $page = app(PatientService::class)->getPaginatedConsultations(10);
    $dates = collect($page->items())
        ->pluck('date')
        ->map(fn($value) => $value?->toDateString())
        ->all();

    expect($dates)->toBe([now()->subDay()->toDateString(), now()->toDateString()]);
});
