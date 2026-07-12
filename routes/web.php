<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/intake', fn() => view('intake_form'));

Route::get('/staff',  fn() => view('staff_login'));
Route::get('/login',  fn() => view('staff_login'));

Route::get('/psychiatrist/dashboard', fn() => view('psychiatrist'));

Route::get('/lifecoach/dashboard', fn() => view('lifecoach'));