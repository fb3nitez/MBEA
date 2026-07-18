<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntakeFormController;

//public
Route::get('/', function () {
    return view('index');
});

Route::get('/test', function () {
    return view('test');
});

Route::middleware(['auth', 'role:psychiatrist'])
    ->prefix('psychiatrist')
    ->name('psychiatrist.')
    ->group(function () {

        Route::get('dashboard', fn () => view('psychiatrist.dashboard'))->name('dashboard');
        Route::get('patients', fn () => view('psychiatrist.patients'))->name('patients');
        Route::get('consultations', fn () => view('psychiatrist.consultations'))->name('consultations');
        Route::get('records', fn () => view('psychiatrist.records'))->name('records');
        Route::get('lifestyle', fn () => view('psychiatrist.lifestyle'))->name('lifestyle');
        Route::get('assessments', fn () => view('psychiatrist.assessments'))->name('assessments');
        Route::get('prescriptions', fn () => view('psychiatrist.prescriptions'))->name('prescriptions');
});

Route::middleware(['auth', 'role:lifecoach'])
    ->prefix('lifecoach')
    ->name('lifecoach.')
    ->group(function () {

    Route::get('/dashboard', fn () => view('lifecoach.dashboard'))->name('dashboard');
    Route::get('/patients', fn () => view('lifecoach.patients'))->name('patients');
    Route::get('/notes', fn () => view('lifecoach.notes'))->name('notes');
    Route::get('/tasks', fn () => view('lifecoach.tasks'))->name('tasks');
    Route::get('/profile', fn () => view('lifecoach.profile'))->name('profile');
});

Route::middleware('guest')->get('/login', fn () => view('staff_login'))->name('login');
Route::post('/auth/login',[AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout',[AuthController::class, 'logout'])->name('auth.logout');

Route::get('/intake-form', [IntakeFormController::class, 'create'])->name('intake');
Route::post('/submit-intake', [IntakeFormController::class, 'store'])->name('intake.submit');
