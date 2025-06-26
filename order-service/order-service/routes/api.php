<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\AnalyticsController;

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'Order Service API is working!',
        'service' => 'Order Service',
        'version' => '1.0',
        'timestamp' => now()
    ]);
});

// Other routes...
Route::post('/test-order', function (Request $request) {
    return response()->json([
        'message' => 'Test endpoint working',
        'received_data' => $request->all(),
    ]);
});

// Order routes// Public order routes
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::put('/orders/{id}', [OrderController::class, 'update']); // Add this
Route::delete('/orders/{id}', [OrderController::class, 'destroy']); // Add this
Route::post('/orders/{id}/refund', [OrderController::class, 'refund']); // Add this
// Download routes
Route::get('/downloads', [DownloadController::class, 'index']);
Route::get('/downloads/{token}', [DownloadController::class, 'show']);
Route::post('/downloads/{token}/link', [DownloadController::class, 'generateDownloadLink']);
Route::get('/downloads/{token}/file', [DownloadController::class, 'downloadFile'])
    ->name('api.downloads.file');

// Analytics routes
Route::get('/analytics/sales', [AnalyticsController::class, 'sales']);
Route::get('/analytics/seller/{sellerId}', [AnalyticsController::class, 'seller']);

// Internal routes (for microservice communication)
Route::prefix('internal')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});
// Debug route to test request format
Route::post('/debug', function (Request $request) {
    return response()->json([
        'message' => 'Debug endpoint',
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'all_data' => $request->all(),
        'items_specific' => $request->input('items'),
        'items_type' => gettype($request->input('items')),
        'json_data' => $request->json()->all(),
    ]);
});