<?php

it('renders the spiritual intake questionnaire in the patient intake form', function () {
    $response = $this->get('/intake-form');

    $response->assertOk()
        ->assertSee('Spiritual Intake Form')
        ->assertSee('Religious Background')
        ->assertSee('Church Involvement')
        ->assertSee('Have you ever been hypnotized')
        ->assertSee('Have you ever been involved in satanic ritual worship')
        ->assertSee('summary-spiritual-religion');
});
