<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    // FIXED STORE METHOD FOR ProductController 
// Replace the existing store method in product-service/app/Http/Controllers/Api/ProductController.php

    public function store(Request $request)
    {
        // Enhanced logging for debugging
        \Illuminate\Support\Facades\Log::info('=== PRODUCT CREATION REQUEST ===', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'has_files' => $request->hasFile('product_files') || $request->hasFile('preview_files'),
            'all_data' => $request->except(['product_files', 'preview_files']), // Exclude files from log
            'file_counts' => [
                'product_files' => $request->hasFile('product_files') ? count($request->file('product_files')) : 0,
                'preview_files' => $request->hasFile('preview_files') ? count($request->file('preview_files')) : 0,
            ]
        ]);

        $validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'seller_id' => 'required|integer',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
            'is_featured' => 'nullable',

            // SIMPLIFIED file validation - more permissive for testing
            // 'product_files' => 'nullable|array',
            // 'product_files.*' => 'nullable|file|max:102400', // Just check if it's a file and size
            // 'preview_files' => 'nullable|array',
            // 'preview_files.*' => 'nullable|file|max:10240', // Just check if it's a file and size
        ];

        $customMessages = [
            'name.required' => 'Product name is required.',
            'description.required' => 'Product description is required.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Product price must be a number.',
            'seller_id.required' => 'Seller ID is required.',
        ];


        // ENHANCED DEBUG LOGGING
        \Illuminate\Support\Facades\Log::info('=== FILE UPLOAD DEBUG ===', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'has_product_files' => $request->hasFile('product_files'),
            'has_preview_files' => $request->hasFile('preview_files'),
            'all_files' => $request->allFiles(),
            'request_size' => strlen(serialize($request->all())),
        ]);
        // Debug each file individually
        if ($request->hasFile('product_files')) {
            foreach ($request->file('product_files') as $index => $file) {
                \Illuminate\Support\Facades\Log::info("Product file {$index}", [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'is_valid' => $file->isValid(),
                    'error_code' => $file->getError(),
                    'real_path' => $file->getRealPath(),
                    'is_uploaded_file' => is_uploaded_file($file->getRealPath()),
                ]);
            }
        }
        if ($request->hasFile('preview_files')) {
            foreach ($request->file('preview_files') as $index => $file) {
                \Illuminate\Support\Facades\Log::info("Preview file {$index}", [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'is_valid' => $file->isValid(),
                    'error_code' => $file->getError(),
                    'real_path' => $file->getRealPath(),
                    'is_uploaded_file' => is_uploaded_file($file->getRealPath()),
                ]);
            }
        }

        $validator = Validator::make($request->all(), $validationRules, $customMessages);
        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Product validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['product_files', 'preview_files'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Additional seller_id validation
        if (!$request->seller_id || $request->seller_id < 1) {
            \Illuminate\Support\Facades\Log::error('Invalid seller_id provided', [
                'seller_id' => $request->seller_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid seller ID provided',
                'errors' => ['seller_id' => ['Valid seller ID is required']]
            ], 422);
        }

        try {
            // Parse tags safely
            $tags = [];
            if ($request->has('tags') && $request->tags) {
                $tagsInput = $request->tags;
                if (is_string($tagsInput)) {
                    // Try to decode as JSON first
                    $decodedTags = json_decode($tagsInput, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedTags)) {
                        $tags = $decodedTags;
                    } else {
                        // Fall back to comma-separated string
                        $tags = array_map('trim', explode(',', $tagsInput));
                        $tags = array_filter($tags, function ($tag) {
                            return !empty($tag);
                        });
                    }
                }
            }

            // Convert is_featured safely
            $isFeatured = false;
            if ($request->has('is_featured')) {
                $isFeatured = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN);
            }

            \Illuminate\Support\Facades\Log::info('Creating product with processed data', [
                'name' => $request->name,
                'seller_id' => $request->seller_id,
                'price' => $request->price,
                'is_featured' => $isFeatured,
                'tags_count' => count($tags),
                'tags' => $tags
            ]);

            // Create product
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'seller_id' => $request->seller_id,
                'category' => $request->category,
                'tags' => $tags,
                'status' => $request->status ?? 'draft',
                'is_featured' => $isFeatured,
            ]);

            \Illuminate\Support\Facades\Log::info('Product created successfully', [
                'product_id' => $product->id,
                'name' => $product->name
            ]);

            // Handle file uploads with enhanced error handling
            $fileUploadResults = [];
            if ($request->hasFile('product_files') || $request->hasFile('preview_files')) {
                $fileUploadResults = $this->handleFileUploadsEnhanced($request, $product);
            }

            // Load the product with files for response
            $product->load('files');

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product->toApiArray(),
                'file_upload_results' => $fileUploadResults
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Enhanced file upload handling method
    private function handleFileUploadsEnhanced(Request $request, Product $product)
    {
        $results = [
            'product_files' => [],
            'preview_files' => [],
            'summary' => [
                'total_uploaded' => 0,
                'failed_uploads' => 0,
                'errors' => []
            ]
        ];

        try {
            // Ensure storage directories exist
            $this->ensureStorageDirectories();

            // Handle main product files
            if ($request->hasFile('product_files')) {
                \Illuminate\Support\Facades\Log::info('Processing main product files', [
                    'count' => count($request->file('product_files'))
                ]);

                foreach ($request->file('product_files') as $index => $file) {
                    try {
                        if ($file && $file->isValid()) {
                            $result = $this->storeProductFileEnhanced($file, $product, false);
                            $results['product_files'][] = $result;
                            if ($result['success']) {
                                $results['summary']['total_uploaded']++;
                            } else {
                                $results['summary']['failed_uploads']++;
                                $results['summary']['errors'][] = $result['error'];
                            }
                        } else {
                            $error = 'Invalid file at index ' . $index;
                            $results['product_files'][] = ['success' => false, 'error' => $error];
                            $results['summary']['failed_uploads']++;
                            $results['summary']['errors'][] = $error;
                        }
                    } catch (\Exception $e) {
                        $error = 'Failed to process file at index ' . $index . ': ' . $e->getMessage();
                        $results['product_files'][] = ['success' => false, 'error' => $error];
                        $results['summary']['failed_uploads']++;
                        $results['summary']['errors'][] = $error;
                    }
                }
            }

            // Handle preview files
            if ($request->hasFile('preview_files')) {
                \Illuminate\Support\Facades\Log::info('Processing preview files', [
                    'count' => count($request->file('preview_files'))
                ]);

                foreach ($request->file('preview_files') as $index => $file) {
                    try {
                        if ($file && $file->isValid()) {
                            $result = $this->storeProductFileEnhanced($file, $product, true);
                            $results['preview_files'][] = $result;
                            if ($result['success']) {
                                $results['summary']['total_uploaded']++;
                            } else {
                                $results['summary']['failed_uploads']++;
                                $results['summary']['errors'][] = $result['error'];
                            }
                        } else {
                            $error = 'Invalid preview file at index ' . $index;
                            $results['preview_files'][] = ['success' => false, 'error' => $error];
                            $results['summary']['failed_uploads']++;
                            $results['summary']['errors'][] = $error;
                        }
                    } catch (\Exception $e) {
                        $error = 'Failed to process preview file at index ' . $index . ': ' . $e->getMessage();
                        $results['preview_files'][] = ['success' => false, 'error' => $error];
                        $results['summary']['failed_uploads']++;
                        $results['summary']['errors'][] = $error;
                    }
                }
            }

            \Illuminate\Support\Facades\Log::info('File upload summary', $results['summary']);

            return $results;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File upload handling failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            $results['summary']['errors'][] = 'File upload system error: ' . $e->getMessage();
            return $results;
        }
    }

    // Enhanced individual file storage method
    private function storeProductFileEnhanced($file, Product $product, bool $isPreview = false)
    {
        try {
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            \Illuminate\Support\Facades\Log::info('Storing file', [
                'original_name' => $originalName,
                'extension' => $extension,
                'size' => $size,
                'is_preview' => $isPreview
            ]);

            // Validate file size and type again
            $maxSize = $isPreview ? 10 * 1024 * 1024 : 100 * 1024 * 1024;
            if ($size > $maxSize) {
                return [
                    'success' => false,
                    'error' => "File '{$originalName}' is too large. Maximum size: " . ($maxSize / 1024 / 1024) . "MB"
                ];
            }

            // Generate secure filename
            $secureFilename = \Illuminate\Support\Str::uuid() . '_' . time() . '.' . $extension;

            // Determine storage path
            $storagePath = $isPreview ? 'previews/' : 'products/';

            // Store file
            $path = $file->storeAs($storagePath, $secureFilename, 'private');

            if (!$path) {
                return [
                    'success' => false,
                    'error' => "Failed to store file '{$originalName}'"
                ];
            }

            // Create database record
            $productFile = ProductFile::create([
                'product_id' => $product->id,
                'filename' => $secureFilename,
                'original_name' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $size,
                'mime_type' => $mimeType,
                'is_preview' => $isPreview,
                'download_count' => 0,
            ]);

            \Illuminate\Support\Facades\Log::info('File stored successfully', [
                'file_id' => $productFile->id,
                'original_name' => $originalName,
                'stored_path' => $path,
                'is_preview' => $isPreview
            ]);

            return [
                'success' => true,
                'file_id' => $productFile->id,
                'original_name' => $originalName,
                'stored_path' => $path
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to store individual file', [
                'original_name' => $originalName ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => "Failed to store '{$originalName}': " . $e->getMessage()
            ];
        }
    }

    // Ensure storage directories exist
    private function ensureStorageDirectories()
    {
        $directories = ['products', 'previews'];

        foreach ($directories as $dir) {
            if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($dir)) {
                \Illuminate\Support\Facades\Storage::disk('private')->makeDirectory($dir);
                \Illuminate\Support\Facades\Log::info("Created storage directory: {$dir}");
            }
        }
    }
    // Handle file uploads for product - ENHANCED WITH BETTER ERROR HANDLING
    private function handleFileUploads(Request $request, Product $product)
    {
        try {
            // Handle main product files
            if ($request->hasFile('product_files')) {
                \Illuminate\Support\Facades\Log::info('Processing main product files', [
                    'count' => count($request->file('product_files'))
                ]);

                foreach ($request->file('product_files') as $file) {
                    if ($file && $file->isValid()) {
                        $this->storeProductFile($file, $product, false);
                    } else {
                        \Illuminate\Support\Facades\Log::warning('Invalid product file skipped', [
                            'file' => $file ? $file->getClientOriginalName() : 'null'
                        ]);
                    }
                }
            }

            // Handle preview files
            if ($request->hasFile('preview_files')) {
                \Illuminate\Support\Facades\Log::info('Processing preview files', [
                    'count' => count($request->file('preview_files'))
                ]);

                foreach ($request->file('preview_files') as $file) {
                    if ($file && $file->isValid()) {
                        $this->storeProductFile($file, $product, true);
                    } else {
                        \Illuminate\Support\Facades\Log::warning('Invalid preview file skipped', [
                            'file' => $file ? $file->getClientOriginalName() : 'null'
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File upload handling failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception here - product was created successfully
            // Just log the error and continue
        }
    }

    // Store individual product file - ENHANCED WITH BETTER VALIDATION
    private function storeProductFile($file, Product $product, bool $isPreview = false)
    {
        try {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            // Validate file size and type
            $maxSize = $isPreview ? 10 * 1024 * 1024 : 100 * 1024 * 1024; // 10MB for previews, 100MB for main files
            if ($size > $maxSize) {
                \Illuminate\Support\Facades\Log::warning('File too large, skipping', [
                    'file' => $originalName,
                    'size' => $size,
                    'max_size' => $maxSize
                ]);
                return;
            }

            // Generate secure filename
            $secureFilename = Str::uuid() . '_' . time() . '.' . $extension;

            // Determine storage path
            $storagePath = $isPreview ? 'previews/' : 'products/';

            // Create directories if they don't exist
            if (!Storage::disk('private')->exists($storagePath)) {
                Storage::disk('private')->makeDirectory($storagePath);
            }

            // Store file
            $path = $file->storeAs($storagePath, $secureFilename, 'private');

            if (!$path) {
                \Illuminate\Support\Facades\Log::error('Failed to store file', [
                    'original_name' => $originalName,
                    'path' => $storagePath . $secureFilename
                ]);
                return;
            }

            // Create database record
            $productFile = ProductFile::create([
                'product_id' => $product->id,
                'filename' => $secureFilename,
                'original_name' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $size,
                'mime_type' => $mimeType,
                'is_preview' => $isPreview,
                'download_count' => 0,
            ]);

            \Illuminate\Support\Facades\Log::info('File stored successfully', [
                'file_id' => $productFile->id,
                'original_name' => $originalName,
                'stored_path' => $path,
                'is_preview' => $isPreview
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to store individual file', [
                'original_name' => $originalName ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Get All Products (GET /api/products) - UNCHANGED
    public function index(Request $request)
    {
        $query = Product::with('files');

        // Handle comma-separated IDs for internal API calls
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        // Apply filters
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Creator filter for dashboard
        if ($request->has('creator_filter') && $request->creator_filter === 'my_products') {
            if ($request->has('auth_user')) {
                $user = $request->auth_user;
                $query->where('seller_id', $user['id']);
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // For internal API calls, return all results without pagination
        if ($request->has('ids')) {
            $products = $query->get();
            return response()->json([
                'success' => true,
                'products' => $products->map(fn($product) => $product->toApiArray())
            ]);
        }

        // For regular API calls, use pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'products' => collect($products->items())->map(fn($product) => $product->toApiArray()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    // Get Single Product (GET /api/products/{id})
    public function show($id)
    {
        $product = Product::with(['files'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product->toApiArray()
        ]);
    }

    // Update Product (PUT /api/products/{id})
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'status' => 'in:draft,published,suspended',
            'is_featured' => 'nullable|boolean',
            // File validation for updates
            'product_files' => 'nullable|array',
            'product_files.*' => 'nullable|file|max:102400|mimes:zip,rar,pdf,psd,ai,eps,doc,docx,xls,xlsx,ppt,pptx',
            'preview_files' => 'nullable|array',
            'preview_files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Parse tags if provided
            $updateData = $request->only([
                'name',
                'description',
                'price',
                'category',
                'status'
            ]);

            if ($request->has('tags') && $request->tags) {
                $tagsData = json_decode($request->tags, true);
                $updateData['tags'] = is_array($tagsData) ? $tagsData : [];
            }

            // Handle is_featured specifically
            if ($request->has('is_featured')) {
                $updateData['is_featured'] = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN);
            }

            $product->update($updateData);

            // Handle new file uploads if provided
            if ($request->hasFile('product_files') || $request->hasFile('preview_files')) {
                $this->handleFileUploads($request, $product);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $product->load('files')->toApiArray()
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete Product (DELETE /api/products/{id})
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        try {
            // Delete associated files from storage
            foreach ($product->files as $file) {
                Storage::disk('private')->delete($file->file_path);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product deletion failed', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete specific product file (DELETE /api/products/{id}/files/{fileId})
    public function deleteFile($id, $fileId)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $file = ProductFile::where('product_id', $id)->find($fileId);
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        try {
            // Delete from storage
            Storage::disk('private')->delete($file->file_path);

            // Delete from database
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File deletion failed', [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }
}