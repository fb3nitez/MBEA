<?php

it('renders the spiritual intake questionnaire in the patient intake form', function () {
    $response = $this->get('/intake-form');

    $response->assertOk()
        ->assertSee('SPIRITUAL INTAKE FORM')
        ->assertSee('Religious Background')
        ->assertSee('Church Involvement')
        ->assertSee('Have you ever been hypnotized')
        ->assertSee('Have you been involved in satanic ritual worship')
        ->assertSee('summary-spiritual-religion');
});

it('stores spiritual intake answers in relational columns', function () {
    $patient = app(\App\Services\IntakeFormService::class)->submit([
        'name' => 'Spiritual Test Patient',
        'birthday' => '1990-01-01',
        'sex' => 'female',
        'maritalStatus' => 'single',
        'religiousBackgroundChildhoodChristian' => true,
        'newAgeTarotCards' => true,
        'spiritualGuidanceQuestion' => 'Test answer',
    ]);

    $spiritualIntake = $patient->spiritualIntake;

    expect($spiritualIntake->religious_background_childhood_christian)->toBeTrue()
        ->and($spiritualIntake->new_age_tarot_cards)->toBeTrue()
        ->and($spiritualIntake->spiritual_guidance_question)->toBe('Test answer');
});
