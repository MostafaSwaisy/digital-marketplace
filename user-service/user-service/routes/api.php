<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'User Service API is working!',
        'service' => 'User Service',
        'version' => '1.0',
        'timestamp' => now()
    ]);
});

// Public routes (no authentication required)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']); // User registration

// Get all users (for the frontend)
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']); // Move this here (public)
// Add this to the public routes section
Route::delete('/users/{id}', [UserController::class, 'destroy']);
// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    // Route::put('/users/{id}', [UserController::class, 'update']); // Remove from here
});

// Internal routes (for microservice communication)
Route::prefix('internal')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});