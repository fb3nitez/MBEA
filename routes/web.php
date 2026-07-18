<?php

use Illuminate\Support\Facades\Route;

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


//psychiatrist
Route::get('/psychiatrist/dashboard', function () {
    return view('psychiatrist');
});

//lifecoach
Route::get('/lifecoach/dashboard', function () {
    return view('lifecoach_dashboard');
});

Route::get('/lifecoach/patients', function () {
    return view('lifecoach_patients');
});

Route::get('/lifecoach/notes', function () {
    return view('lifecoach_notes');
});

Route::get('/lifecoach/tasks', function () {
    return view('lifecoach_tasks');
});

Route::get('/lifecoach/profile', function () {
    return view('lifecoach_profile');
});