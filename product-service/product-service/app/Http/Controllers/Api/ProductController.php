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
    // Create Product with File Upload (POST /api/products) - FIXED VALIDATION
    public function store(Request $request)
    {
        // Debug incoming request
        \Illuminate\Support\Facades\Log::info('Product creation request', [
            'all_data' => $request->all(),
            'files' => $request->allFiles(),
            'has_product_files' => $request->hasFile('product_files'),
            'has_preview_files' => $request->hasFile('preview_files'),
        ]);

        // FIXED: Make file uploads optional and adjust validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'seller_id' => 'required|integer',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string', // JSON string from frontend
            'status' => 'nullable|in:draft,published',
            'is_featured' => 'nullable|boolean',

            // FIXED: Made file validation optional and more permissive
            'product_files' => 'nullable|array',
            'product_files.*' => 'nullable|file|max:102400|mimes:zip,rar,pdf,psd,ai,eps,doc,docx,xls,xlsx,ppt,pptx',
            'preview_files' => 'nullable|array',
            'preview_files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp',
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Product validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Parse tags if provided
            $tags = [];
            if ($request->has('tags') && $request->tags) {
                $tagsData = json_decode($request->tags, true);
                $tags = is_array($tagsData) ? $tagsData : [];
            }

            // Convert is_featured to boolean
            $isFeatured = false;
            if ($request->has('is_featured')) {
                $isFeatured = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN);
            }

            \Illuminate\Support\Facades\Log::info('Creating product with data', [
                'name' => $request->name,
                'seller_id' => $request->seller_id,
                'price' => $request->price,
                'is_featured' => $isFeatured,
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

            \Illuminate\Support\Facades\Log::info('Product created successfully', ['product_id' => $product->id]);

            // Handle file uploads if any files were provided
            if ($request->hasFile('product_files') || $request->hasFile('preview_files')) {
                $this->handleFileUploads($request, $product);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product->load('files')->toApiArray()
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
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