<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Storage;

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
// ADD THIS ROUTE TO routes/api.php for diagnostics
// Route::get('/upload-info', function () {

Route::get('/upload-info', function () {
    $uploadInfo = [
        'php_version' => PHP_VERSION,
        'upload_settings' => [
            'file_uploads' => ini_get('file_uploads') ? 'Enabled' : 'Disabled',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: 'System default',
        ],
        'storage_info' => [
            'private_disk_exists' => Storage::disk('private')->exists(''),
            'storage_path' => storage_path('app/private'),
            'storage_writable' => is_writable(storage_path('app/private')),
        ],
        'recommended_settings' => [
            'upload_max_filesize' => '100M',
            'post_max_size' => '100M', 
            'max_file_uploads' => '20',
            'max_execution_time' => '300',
            'memory_limit' => '256M',
        ]
    ];
    
    return response()->json($uploadInfo);
});

// ADD THIS SIMPLE TEST UPLOAD ROUTE
Route::post('/test-upload', function (Request $request) {
    $result = [
        'has_files' => $request->hasFile('test_file'),
        'files_received' => [],
        'errors' => [],
    ];
    
    if ($request->hasFile('test_file')) {
        $file = $request->file('test_file');
        $result['files_received'][] = [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'is_valid' => $file->isValid(),
            'error' => $file->getError(),
            'error_message' => $file->getErrorMessage(),
        ];
        
        // Try to store the file
        try {
            $path = $file->store('test', 'private');
            $result['storage_success'] = true;
            $result['stored_path'] = $path;
        } catch (\Exception $e) {
            $result['storage_success'] = false;
            $result['storage_error'] = $e->getMessage();
        }
    }
    
    return response()->json($result);
});