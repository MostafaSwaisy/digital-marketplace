<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FileController;

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'Product Service API is working!',
        'service' => 'Product Service',
        'version' => '1.0',
        'timestamp' => now()
    ]);
});

// Public product routes
Route::apiResource('products', ProductController::class);

// File routes
Route::get('/products/{product}/files/{file}/download', [FileController::class, 'download'])
    ->name('api.products.files.download');
Route::get('/products/{product}/files/{file}', [FileController::class, 'show']);

// Internal routes (for microservice communication)
Route::prefix('internal')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});