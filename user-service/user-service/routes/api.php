<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'User Service API is working!',
        'service' => 'User Service',
        'version' => '1.0',
        'timestamp' => now(),
        'routes' => [
            'POST /api/auth/register',
            'POST /api/auth/login',
            'GET /api/users',
            'POST /api/users'
        ]
    ]);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('validate', [AuthController::class, 'validateToken']);
});

// User management routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'me']);
});

// Internal routes (for microservice communication)
Route::prefix('internal')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});