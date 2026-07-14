<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/intake_form', function () {
    return view('intake-form');
});

Route::get('/staff',  fn() => view('staff_login'));
Route::get('/login',  fn() => view('staff_login'));
Route::get('/psychiatrist/dashboard', fn() => view('psychiatrist'));

Route::get('/intake-form', fn() => view('intake-form'));

Route::get('/patient-intake-form', function () {
    return view('patient-intake-form');
})->name('patient.intake.form');
