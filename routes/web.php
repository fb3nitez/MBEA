<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntakeFormController;

//public
Route::get('/', function () {
    return view('index');
});

//intake form
Route::get('/intake', function () {
    return view('intake_form');
});

//login
Route::get('/staff', function () {
    return view('staff_login');
});

Route::get('/login', function () {
    return view('staff_login');
});


Route::get('/lifecoach/dashboard', fn() => view('lifecoach'));

Route::get('/intake-form', [IntakeFormController::class, 'create']);
Route::post('/submit-intake', [IntakeFormController::class, 'store'])->name('intake.submit');

