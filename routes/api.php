<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HebergementController;
use Illuminate\Support\Facades\Route;

// Routes pour les hébergements
Route::get('/hebergements', [HebergementController::class, 'index']);
Route::get('/hebergements/{id}', [HebergementController::class, 'show']);

// Routes pour l'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
