<?php

use Illuminate\Support\Facades\View;

test('psychiatric modal mirrors the intake trauma layout', function () {
    $html = View::make('psychiatrist.partials.modals')->render();

    expect($html)->toContain('data-expands="ph-physical-expand"');
    expect($html)->toContain('As a child');
    expect($html)->toContain('When did this occur?');
});

test('lifestyle modal uses a PHQ-9 table matching the intake form', function () {
    $html = View::make('psychiatrist.partials.modals')->render();

    expect($html)->toContain('class="pm-phq-table"');
    expect($html)->toContain('class="ls-phq-radio"');
    expect($html)->toContain('name="ls-phq-phq_little_interest"');
    expect($html)->toContain('value="Several days"');
    expect($html)->toContain('id="ls-motivation_level"');
    expect($html)->toContain('id="ls-sub_nicotine_amount"');
    expect($html)->toContain('Level of concern (0 = No concern, 5 = Very concerned)');
});
