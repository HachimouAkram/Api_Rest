<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes de test pour l'API
Route::get('/test-api', function () {
    return view('test-api.index');
});

Route::get('/test-api/auth', function () {
    return view('test-api.auth');
});

Route::get('/test-api/partner', function () {
    return view('test-api.partner');
});

Route::get('/test-api/public', function () {
    return view('test-api.public');
});
