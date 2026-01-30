<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\PartnerController;
use Illuminate\Support\Facades\Route;

// ===========================================
// ESPACE PUBLIC (sans authentification)
// ===========================================

Route::prefix('public')->group(function () {
    Route::get('/search', [PublicController::class, 'search']);
    Route::get('/pages/{slug}', [PublicController::class, 'page']);
    Route::get('/special-offers', [PublicController::class, 'specialOffers']);
    Route::get('/special-offers/{id}', [PublicController::class, 'specialOffer']);
});

// Routes publiques directes
Route::get('/search', [PublicController::class, 'search']);
Route::get('/pages/{slug}', [PublicController::class, 'page']);
Route::get('/special-offers', [PublicController::class, 'specialOffers']);
Route::get('/special-offers/{id}', [PublicController::class, 'specialOffer']);

// ===========================================
// AUTHENTIFICATION
// ===========================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
});

// ===========================================
// ESPACE PARTENAIRE (authentification requise)
// ===========================================

Route::prefix('partner')->middleware(['auth:sanctum', 'role:partenaire,admin'])->group(function () {
    // Profil
    Route::get('/profile', [PartnerController::class, 'profile']);
    Route::patch('/profile', [PartnerController::class, 'updateProfile']);
    
    // Annonces
    Route::post('/listings', [PartnerController::class, 'createListing']);
    Route::get('/listings', [PartnerController::class, 'listings']);
    Route::get('/listings/{id}', [PartnerController::class, 'showListing']);
    Route::patch('/listings/{id}', [PartnerController::class, 'updateListing']);
    Route::delete('/listings/{id}', [PartnerController::class, 'deleteListing']);
    
    // Photos
    Route::post('/listings/{id}/photos', [PartnerController::class, 'uploadPhotos']);
    Route::delete('/photos/{photoId}', [PartnerController::class, 'deletePhoto']);
    
    // Disponibilités
    Route::post('/availability', [PartnerController::class, 'setAvailability']);
    Route::get('/availability/{listingId}', [PartnerController::class, 'getAvailability']);
    Route::delete('/availability/{id}', [PartnerController::class, 'deleteAvailability']);
    
    // Réservations et revenus
    Route::get('/bookings', [PartnerController::class, 'bookings']);
    Route::get('/revenues', [PartnerController::class, 'revenues']);
});

// ===========================================
// ROUTES LEGACY (compatibilité)
// ===========================================

// Routes pour les hébergements (existantes)
Route::get('/hebergements', [App\Http\Controllers\Api\HebergementController::class, 'index']);
Route::get('/hebergements/{id}', [App\Http\Controllers\Api\HebergementController::class, 'show']);
