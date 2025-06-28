<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ← ADD THIS IMPORT
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;     // ← ADD THIS IMPORT
use Illuminate\Support\Facades\Cache;   // ← ADD THIS IMPORT
use Illuminate\Support\Str;             // ← ADD THIS IMPORT

class FileController extends Controller
{
    // Download Product File (GET /api/products/{product}/files/{file}/download)
    public function download(Request $request, $productId, $fileId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $file = ProductFile::where('product_id', $productId)->find($fileId);
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Check if file exists in storage
            if (!Storage::disk('private')->exists($file->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found in storage'
                ], 404);
            }

            // Security check: Verify user has access to this file
            $hasAccess = $this->checkFileAccess($request, $product, $file);
            if (!$hasAccess['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $hasAccess['message']
                ], 403);
            }

            // For preview files, allow direct access
            if ($file->is_preview) {
                return $this->streamFile($file);
            }

            // For main product files, check purchase history
            $purchaseVerified = $this->verifyPurchase($request, $product);
            if (!$purchaseVerified['verified']) {
                return response()->json([
                    'success' => false,
                    'message' => $purchaseVerified['message']
                ], 403);
            }

            // Log download
            $this->logDownload($file, $request);

            // Stream the file
            return $this->streamFile($file);

        } catch (\Exception $e) {
            Log::error('File download failed', [
                'product_id' => $productId,
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check if user has access to download file
    private function checkFileAccess(Request $request, Product $product, ProductFile $file)
    {
        // Get user from request (set by JWT middleware)
        $user = $request->get('auth_user');
        
        if (!$user) {
            return [
                'allowed' => false,
                'message' => 'Authentication required'
            ];
        }

        // Owner can always download their own files
        if ($product->seller_id == $user['id']) {
            return [
                'allowed' => true,
                'message' => 'Owner access'
            ];
        }

        // Admins can download any file
        if ($user['role'] === 'admin') {
            return [
                'allowed' => true,
                'message' => 'Admin access'
            ];
        }

        // For preview files, any authenticated user can access
        if ($file->is_preview) {
            return [
                'allowed' => true,
                'message' => 'Preview file access'
            ];
        }

        // For main files, need to check purchase
        return [
            'allowed' => true, // Will be verified by verifyPurchase
            'message' => 'Checking purchase history'
        ];
    }

    // Verify user has purchased this product
    private function verifyPurchase(Request $request, Product $product)
    {
        $user = $request->get('auth_user');

        try {
            // Call Order Service to verify purchase
            $response = Http::withHeaders([
                'Authorization' => $request->header('Authorization'),
                'Accept' => 'application/json',
            ])->get('http://localhost:8003/api/orders/verify-purchase', [
                'buyer_id' => $user['id'],
                'product_id' => $product->id
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'verified' => $data['purchased'] ?? false,
                    'message' => $data['message'] ?? 'Purchase verification failed'
                ];
            }

            return [
                'verified' => false,
                'message' => 'Unable to verify purchase'
            ];

        } catch (\Exception $e) {
            Log::error('Purchase verification failed', [
                'user_id' => $user['id'],
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            return [
                'verified' => false,
                'message' => 'Purchase verification service unavailable'
            ];
        }
    }

    // Stream file to browser - FIXED VERSION
    private function streamFile(ProductFile $file)
    {
        try {
            // Get the full file path
            $filePath = storage_path('app/private/' . $file->file_path);
            // Check if file actually exists on filesystem
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on filesystem'
                ], 404);
            }

            // Return file download response
            return response()->download(
                $filePath,
                $file->original_name,
                [
                    'Content-Type' => $file->mime_type,
                    'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"',
                    'Content-Length' => $file->file_size
                ]
            );

        } catch (\Exception $e) {
            Log::error('File streaming failed', [
                'file_id' => $file->id,
                'file_path' => $file->file_path,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File streaming failed'
            ], 500);
        }
    }

    // Log file download
    private function logDownload(ProductFile $file, Request $request)
    {
        try {
            // Increment download count
            $file->increment('download_count');

            // Log download activity
            Log::info('File downloaded', [
                'file_id' => $file->id,
                'product_id' => $file->product_id,
                'user_id' => $request->get('auth_user')['id'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log download', [
                'file_id' => $file->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Get File Info (GET /api/products/{product}/files/{file})
    public function show($productId, $fileId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $file = ProductFile::where('product_id', $productId)->find($fileId);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'file' => $file->toApiArray()
        ]);
    }

    // Get all files for a product (GET /api/products/{product}/files)
    public function index(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $query = ProductFile::where('product_id', $productId);

        // Filter by file type if requested
        if ($request->has('type')) {
            if ($request->type === 'preview') {
                $query->where('is_preview', true);
            } elseif ($request->type === 'main') {
                $query->where('is_preview', false);
            }
        }

        $files = $query->orderBy('is_preview', 'desc')
                      ->orderBy('created_at', 'asc')
                      ->get();

        return response()->json([
            'success' => true,
            'files' => $files->map(function($file) {
                return $file->toApiArray();
            })
        ]);
    }

    // Generate secure download token (POST /api/products/{product}/files/{file}/token)
    public function generateDownloadToken(Request $request, $productId, $fileId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $file = ProductFile::where('product_id', $productId)->find($fileId);
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Check access
            $hasAccess = $this->checkFileAccess($request, $product, $file);
            if (!$hasAccess['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $hasAccess['message']
                ], 403);
            }

            // For main files, verify purchase
            if (!$file->is_preview) {
                $purchaseVerified = $this->verifyPurchase($request, $product);
                if (!$purchaseVerified['verified']) {
                    return response()->json([
                        'success' => false,
                        'message' => $purchaseVerified['message']
                    ], 403);
                }
            }

            // Generate secure token
            $token = Str::random(64);
            $expiresAt = now()->addHours(24); // Token expires in 24 hours

            // Store token in cache
            Cache::put(
                "download_token:{$token}",
                [
                    'file_id' => $file->id,
                    'user_id' => $request->get('auth_user')['id'],
                    'expires_at' => $expiresAt
                ],
                $expiresAt
            );

            return response()->json([
                'success' => true,
                'download_token' => $token,
                'download_url' => route('api.products.files.download.token', [
                    'product' => $productId,
                    'file' => $fileId,
                    'token' => $token
                ]),
                'expires_at' => $expiresAt->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Token generation failed', [
                'product_id' => $productId,
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download token'
            ], 500);
        }
    }

    // Download with token (GET /api/products/{product}/files/{file}/download/{token})
    public function downloadWithToken($productId, $fileId, $token)
    {
        try {
            // Verify token
            $tokenData = Cache::get("download_token:{$token}");
            if (!$tokenData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired download token'
                ], 403);
            }

            // Verify file ID matches
            if ($tokenData['file_id'] != $fileId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token does not match file'
                ], 403);
            }

            $file = ProductFile::find($fileId);
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Check if file exists
            if (!Storage::disk('private')->exists($file->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found in storage'
                ], 404);
            }

            // Log download with token info
            Log::info('Token download', [
                'file_id' => $file->id,
                'user_id' => $tokenData['user_id'],
                'token' => substr($token, 0, 8) . '...' // Log partial token for security
            ]);

            // Increment download count
            $file->increment('download_count');

            // Stream file
            return $this->streamFile($file);

        } catch (\Exception $e) {
            Log::error('Token download failed', [
                'product_id' => $productId,
                'file_id' => $fileId,
                'token' => substr($token, 0, 8) . '...',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Download failed'
            ], 500);
        }
    }
}