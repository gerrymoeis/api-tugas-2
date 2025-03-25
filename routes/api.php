<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\AddressController;

// Auth routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes
Route::middleware('api.auth')->group(function () {
    // User routes
    Route::get('/user', [UserController::class, 'get']);
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Contact routes
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::put('/contacts/{id}', [ContactController::class, 'update']);
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);

    // Address routes
    Route::get('/contacts/{contactId}/addresses', [AddressController::class, 'index']);
    Route::post('/contacts/{contactId}/addresses', [AddressController::class, 'store']);
    Route::get('/contacts/{contactId}/addresses/{id}', [AddressController::class, 'show']);
    Route::put('/contacts/{contactId}/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/contacts/{contactId}/addresses/{id}', [AddressController::class, 'destroy']);
});