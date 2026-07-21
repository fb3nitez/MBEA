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
Route::middleware('guest')->get('/login', fn() => view('staff_login'))->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');


// Psychiatrist routes
Route::middleware(['auth', 'role:psychiatrist'])
    ->prefix('psychiatrist')
    ->name('psychiatrist.')
    ->controller(PsychiatristController::class)
    ->group(function () {

        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/patients', 'patients')->name('patients');
        Route::get('/consultations', 'consultations')->name('consultations');
        Route::get('/lifestyle', 'lifestyle')->name('lifestyle');
        Route::get('/assessments', 'assessments')->name('assessments');
        Route::get('/prescriptions', 'prescriptions')->name('prescriptions');

        Route::get('/patients/search', 'searchPatients')->name('patients.search');
        Route::get('/patients/{id}', 'showPatient')->name('patients.show');
        Route::post('/patients', 'storePatient')->name('patients.store');
        Route::put('/patients/{id}', 'updatePatient')->name('patients.update');
        Route::put('/patients/{id}/medical-history', 'updateMedicalHistory')->name('patients.medical-history');
        Route::put('/patients/{id}/psychiatric-history', 'updatePsychiatricHistory')->name('patients.psychiatric-history');
        Route::put('/patients/{id}/lifestyle', 'updateLifestyle')->name('patients.lifestyle');

        Route::post('/consultations', 'storeConsultation')->name('consultations.store');
        Route::put('/consultations/{id}', 'updateConsultation')->name('consultations.update');
        Route::delete('/consultations/{id}', 'destroyConsultation')->name('consultations.destroy');

        Route::post('/patients/{id}/assessment', 'storeAssessment')->name('assessments.store');
        Route::get('/patients/{id}/assessment', 'showAssessment')->name('assessments.show');
        Route::post('/patients/{id}/prescription', 'storePrescription')->name('prescriptions.store');

        Route::post('/clinical-templates', 'storeClinicalTemplate')->name('templates.store');
        Route::put('/clinical-templates/{id}', 'updateClinicalTemplate')->name('templates.update');
        Route::delete('/clinical-templates/{id}', 'destroyClinicalTemplate')->name('templates.destroy');
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
