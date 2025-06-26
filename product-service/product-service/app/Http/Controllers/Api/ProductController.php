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
    // Get All Products (GET /api/products)
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
                'products' => $products->map(fn($product) => $product->toApiArray())
            ]);
        }

        // For regular API calls, use pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'products' => collect($products->items())->map(fn($product) => $product->toApiArray()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    // Create Product (POST /api/products)// In the store method, make sure is_featured is included
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'seller_id' => 'required|integer',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'in:draft,published',
            'is_featured' => 'boolean',  // Add this validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'seller_id' => $request->seller_id,
            'category' => $request->category,
            'tags' => $request->tags ?? [],
            'status' => $request->status ?? 'draft',
            'is_featured' => $request->boolean('is_featured'),  // Use boolean() helper
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('files')->toApiArray()
        ], 201);
    }

    // Get Single Product (GET /api/products/{id})
    public function show($id)
    {
        $product = Product::with(['files'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product->toApiArray());
    }

    // Update Product (PUT /api/products/{id})
  // Update this method in ProductController
public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'price' => 'sometimes|numeric|min:0',
        'category' => 'nullable|string|max:100',
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
        'status' => 'in:draft,published,suspended',
        'is_featured' => 'boolean',  // Add this validation
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update the product with is_featured handling
    $updateData = $request->only([
        'name', 'description', 'price', 'category', 'tags', 'status'
    ]);
    
    // Handle is_featured specifically
    if ($request->has('is_featured')) {
        $updateData['is_featured'] = $request->boolean('is_featured');
    }

    $product->update($updateData);

    return response()->json([
        'message' => 'Product updated successfully',
        'product' => $product->load('files')->toApiArray()
    ]);
}
    // Delete Product (DELETE /api/products/{id})
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete associated files from storage
        foreach ($product->files as $file) {
            Storage::delete($file->file_path);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}