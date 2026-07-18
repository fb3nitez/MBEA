<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntakeFormController;
use App\Http\Controllers\PsychiatristController;
use App\Http\Controllers\LifecoachController;

// Public routes
Route::view('/', 'index');
Route::view('/test', 'test');

Route::get('/intake-form', [IntakeFormController::class, 'create'])->name('intake');
Route::post('/submit-intake', [IntakeFormController::class, 'store'])->name('intake.submit');


// Auth routes
Route::middleware('guest')->get('/login', fn () => view('staff_login'))->name('login');
Route::post('/auth/login',[AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout',[AuthController::class, 'logout'])->name('auth.logout');


// Psychiatrist routes
Route::middleware(['auth', 'role:psychiatrist'])
    ->prefix('psychiatrist')
    ->name('psychiatrist.')
    ->controller(PsychiatristController::class)
    ->group(function () {

    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/patients', 'patients')->name('patients');
    Route::get('/consultations', 'consultations')->name('consultations');
    Route::get('/records', 'records')->name('records');
    Route::get('/lifestyle', 'lifestyle')->name('lifestyle');
    Route::get('/assessments', 'assessments')->name('assessments');
    Route::get('/prescriptions', 'prescriptions')->name('prescriptions');
});


// Lifecoach routes
Route::middleware(['auth', 'role:lifecoach'])
    ->prefix('lifecoach')
    ->name('lifecoach.')
    ->controller(LifecoachController::class)
    ->group(function () {

    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/patients', 'patients')->name('patients');
    Route::get('/notes', 'notes')->name('notes');
    Route::get('/tasks', 'tasks')->name('tasks');
    Route::get('/profile', 'profile')->name('profile');
});

