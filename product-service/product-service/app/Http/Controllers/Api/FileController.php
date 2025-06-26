<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Download Product File (GET /api/products/{product}/files/{file}/download)
    public function download(Request $request, $productId, $fileId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $file = ProductFile::where('product_id', $productId)->find($fileId);
        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // For now, just return file info (we'll handle actual file download later)
        return response()->json([
            'message' => 'File download would start here',
            'file' => $file->toApiArray()
        ]);
    }

    // Get File Info (GET /api/products/{product}/files/{file})
    public function show($productId, $fileId)
    {
        $file = ProductFile::where('product_id', $productId)->find($fileId);
        
        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->json($file->toApiArray());
    }
}