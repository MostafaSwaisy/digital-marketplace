<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GatewayController;

// API Gateway test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Gateway is working!',
        'services' => [
            'user_service' => 'http://localhost:8001',
            'product_service' => 'http://localhost:8002',
            'order_service' => 'http://localhost:8003',
        ],
        'timestamp' => now()
    ]);
});

// Combined dashboard endpoint
Route::get('/dashboard', [GatewayController::class, 'dashboard']);


// User service routes
Route::get('/users', [GatewayController::class, 'userProxy']);
Route::post('/users', [GatewayController::class, 'userProxy']);
Route::get('/users/{id}', [GatewayController::class, 'userProxy']);
Route::put('/users/{id}', [GatewayController::class, 'userProxy']); // Make sure this exists
Route::delete('/users/{id}', [GatewayController::class, 'userProxy']);

Route::get('/products', [GatewayController::class, 'productProxy']);
Route::post('/products', [GatewayController::class, 'productProxy']);
Route::get('/products/{id}', [GatewayController::class, 'productProxy']);
Route::put('/products/{id}', [GatewayController::class, 'productProxy']);

// Update the order routes section
Route::get('/orders', [GatewayController::class, 'orderProxy']);
Route::post('/orders', [GatewayController::class, 'orderProxy']);
Route::get('/orders/{id}', [GatewayController::class, 'orderProxy']);
Route::put('/orders/{id}', [GatewayController::class, 'orderProxy']); // Add this
Route::delete('/orders/{id}', [GatewayController::class, 'orderProxy']); // Add this
Route::post('/orders/{id}/refund', [GatewayController::class, 'orderProxy']);
// Catch-all routes (keep as backup)
Route::any('/users/{path?}', [GatewayController::class, 'userProxy'])->where('path', '.*');
Route::any('/products/{path?}', [GatewayController::class, 'productProxy'])->where('path', '.*');
Route::any('/orders/{path?}', [GatewayController::class, 'orderProxy'])->where('path', '.*');