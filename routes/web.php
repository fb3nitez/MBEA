<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntakeFormController;

Route::get('/', function () {
    return view('index');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/staff',  fn() => view('staff_login'));
Route::get('/login',  fn() => view('staff_login'));
Route::get('/psychiatrist/dashboard', fn() => view('psychiatrist'));

Route::get('/intake-form', [IntakeFormController::class, 'create']);
Route::post('/submit-intake', [IntakeFormController::class, 'store'])->name('intake.submit');

