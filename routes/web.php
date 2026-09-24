<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntakeFormController;
use App\Http\Controllers\LifecoachController;
use App\Http\Controllers\PsychiatristController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

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
        Route::put('/clinical-templates/{id}/favorite', 'toggleClinicalTemplateFavorite')->name('templates.favorite');
        Route::post('/clinical-templates/{id}/used', 'markClinicalTemplateUsed')->name('templates.used');
        Route::post('/clinical-templates/{id}/duplicate', 'duplicateClinicalTemplate')->name('templates.duplicate');
        Route::put('/clinical-template-tags', 'renameClinicalTemplateTag')->name('templates.tags.rename');
        Route::delete('/clinical-template-tags', 'deleteClinicalTemplateTag')->name('templates.tags.delete');
        Route::post('/clinical-templates/bulk-delete', 'bulkDeleteClinicalTemplates')->name('templates.bulk-delete');
        Route::post('/clinical-templates/bulk-tag', 'bulkTagClinicalTemplates')->name('templates.bulk-tag');
        Route::get('/profile', 'profile')->name('profile');
        Route::put('/profile/account', 'updateProfileAccount')->name('profile.account');
        Route::put('/profile/security', 'updateProfileSecurity')->name('profile.security');
        Route::put('/profile/notifications', 'updateProfileNotifications')->name('profile.notifications');
        Route::post('/profile/avatar', 'uploadProfileAvatar')->name('profile.avatar');
        Route::get('/profile/activity', 'profileActivity')->name('profile.activity');

        Route::post('/save-note/{id}', 'saveNote')->name('notes.save');
        Route::post('/patients/{id}/clinical-images', 'uploadClinicalImage')->name('notes.images.store');

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
        Route::get('/updates', 'updates')->name('updates');
        Route::put('/updates/{id}/read', 'markUpdateRead')->name('updates.read');
        Route::put('/updates/read-all', 'markAllUpdatesRead')->name('updates.read-all');
        Route::delete('/updates/{id}', 'dismissUpdate')->name('updates.dismiss');
        Route::get('/patients', 'patients')->name('patients');
        Route::get('/notes', 'notes')->name('notes');
        Route::get('/tasks', 'tasks')->name('tasks');
        Route::get('/profile', 'profile')->name('profile');
        Route::put('/profile/account', 'updateProfileAccount')->name('profile.account');
        Route::put('/profile/security', 'updateProfileSecurity')->name('profile.security');
        Route::put('/profile/notifications', 'updateProfileNotifications')->name('profile.notifications');
        Route::post('/profile/avatar', 'uploadProfileAvatar')->name('profile.avatar');
        Route::get('/profile/activity', 'profileActivity')->name('profile.activity');

        Route::get('/patients/{id}', 'showPatient')->name('patients.show');

        Route::post('/notes', 'storeNote')->name('notes.store');
        Route::delete('/notes/{id}', 'destroyNote')->name('notes.destroy');

        Route::post('/tasks', 'storeTask')->name('tasks.store');
        Route::put('/tasks/{id}/toggle', 'toggleTask')->name('tasks.toggle');

        Route::post('/schedules', 'storeSchedule')->name('schedules.store');
        Route::post('/goals', 'storeGoal')->name('goals.store');
        Route::put('/goals/{id}', 'updateGoal')->name('goals.update');
        Route::put('/goals/{id}/progress', 'updateGoalProgress')->name('goals.progress');
        Route::delete('/goals/{id}', 'destroyGoal')->name('goals.destroy');
    });

// test route for role selection
Route::post('/auth/check-role', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['role' => null]);
    }

    if ($user->hasRole('psychiatrist')) {
        return response()->json(['role' => 'Psychiatrist']);
    }

    if ($user->hasRole('lifecoach')) {
        return response()->json(['role' => 'Life Coach']);
    }

    return response()->json(['role' => null]);
});
