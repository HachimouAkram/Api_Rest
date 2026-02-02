<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\AdminController;

/* ==========================================================
|  ESPACE PUBLIC (SANS AUTHENTIFICATION)
|==========================================================*/

// Recherche & contenu
Route::get('/search', [PublicController::class, 'search']);
Route::get('/pages/{slug}', [PublicController::class, 'page']);
Route::get('/special-offers', [PublicController::class, 'specialOffers']);
Route::get('/special-offers/{id}', [PublicController::class, 'specialOffer']);

// Annonces publiques
Route::get('/listings', [PublicController::class, 'listings']);
Route::get('/listings/{id}', [PublicController::class, 'showListing']);
Route::get('/listings/{id}/reviews', [PublicController::class, 'reviews']);


/* ==========================================================
|  AUTHENTIFICATION
|==========================================================*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


/* ==========================================================
|  ESPACE CLIENT (AUTH REQUISE)
|==========================================================*/

Route::prefix('client')
    ->middleware(['auth:sanctum', 'role:client'])
    ->group(function () {

    // Profil
    Route::get('/profile', [ClientController::class, 'profile']);
    Route::patch('/profile', [ClientController::class, 'updateProfile']);

    // Réservations
    Route::get('/bookings', [ClientController::class, 'bookings']);
    Route::get('/bookings/{id}', [ClientController::class, 'showBooking']);
    Route::post('/bookings', [ClientController::class, 'createBooking']);
    Route::patch('/bookings/{id}/cancel', [ClientController::class, 'cancelBooking']);

    // Paiements
    Route::post('/payments/intent', [ClientController::class, 'createPaymentIntent']);

    // Avis
    Route::post('/reviews', [ClientController::class, 'createReview']);

    // Favoris
    Route::get('/favorites', [ClientController::class, 'favorites']);
    Route::post('/favorites/{listingId}', [ClientController::class, 'addFavorite']);
    Route::delete('/favorites/{listingId}', [ClientController::class, 'removeFavorite']);
});


/* ==========================================================
|  ESPACE PARTENAIRE (AUTH + RÔLE PARTENAIRE)
|==========================================================*/

Route::prefix('partner')
    ->middleware(['auth:sanctum', 'role:partenaire'])
    ->group(function () {

    // Profil
    Route::get('/profile', [PartnerController::class, 'profile']);
    Route::patch('/profile', [PartnerController::class, 'updateProfile']);

    // Dashboard
    Route::get('/dashboard/stats', [PartnerController::class, 'dashboardStats']);

    // Annonces (PARTENAIRE)
    Route::get('/listings', [PartnerController::class, 'listings']);
    Route::post('/listings', [PartnerController::class, 'createListing']);
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

    // Réservations & revenus
    Route::get('/bookings', [PartnerController::class, 'bookings']);
    Route::get('/revenues', [PartnerController::class, 'revenues']);
});


/* ==========================================================
|  ESPACE ADMIN (AUTH + RÔLE ADMIN)
|==========================================================*/

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats']);

    // Utilisateurs
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/{id}', [AdminController::class, 'showUser']);
    Route::patch('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

    // Annonces
    Route::get('/listings', [AdminController::class, 'listings']);
    Route::patch('/listings/{id}/toggle-status', [AdminController::class, 'toggleListingStatus']);
    Route::patch('/listings/{id}/approve', [AdminController::class, 'approveListing']);
    Route::delete('/listings/{id}', [AdminController::class, 'deleteListing']);

    // Réservations
    Route::get('/bookings', [AdminController::class, 'bookings']);
    Route::patch('/bookings/{id}/status', [AdminController::class, 'updateBookingStatus']);
});
