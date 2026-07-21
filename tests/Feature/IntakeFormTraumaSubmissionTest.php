<?php

use App\Models\PatientRecord;
use App\Models\PsychiatricHistory;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

it('accepts trauma checkbox values submitted as on from the browser', function () {
    $this->withoutMiddleware(PreventRequestForgery::class);

    $payload = [
        'name' => 'Trauma Test Patient',
        'birthday' => '1995-05-15',
        'sex' => 'female',
        'maritalStatus' => 'single',
        'chiefComplaint' => 'Feeling anxious and overwhelmed',
        'healthScore' => 7,
        'traumaPhysical' => 'on',
        'tpChild' => 'on',
        'tpPast' => 'on',
        'tpDetails' => 'Occurred during childhood',
        'traumaEmotional' => 'on',
        'teAdult' => 'on',
        'traumaSexual' => 'on',
        'tsOngoing' => 'on',
        'traumaNeglect' => 'on',
        'tnPast' => 'on',
    ];

    $response = $this->postJson('/submit-intake', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
        ]);

    $patient = PatientRecord::where('fullname', 'Trauma Test Patient')->first();
    expect($patient)->not->toBeNull();

    $history = PsychiatricHistory::where('patient_record_id', $patient->id)->first();
    expect($history)->not->toBeNull()
        ->and($history->physical_abuse)->toBeTrue()
        ->and($history->physical_child)->toBeTrue()
        ->and($history->physical_past)->toBeTrue()
        ->and($history->physical_notes)->toBe('Occurred during childhood')
        ->and($history->emotional_abuse)->toBeTrue()
        ->and($history->emotional_adult)->toBeTrue()
        ->and($history->sexual_abuse)->toBeTrue()
        ->and($history->sexual_ongoing)->toBeTrue()
        ->and($history->neglect)->toBeTrue()
        ->and($history->neglect_past)->toBeTrue();
});
