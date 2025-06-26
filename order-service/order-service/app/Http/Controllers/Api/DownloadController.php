<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DownloadController extends Controller
{
    // Get User Downloads (GET /api/downloads)
    public function index(Request $request)
    {
        $query = Download::query();

        // Filter by buyer
        if ($request->has('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Only show valid downloads
        if ($request->get('valid_only', false)) {
            $query->where('download_count', '<', 'max_downloads')
                  ->where(function ($q) {
                      $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                  });
        }

        $downloads = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'downloads' => $downloads->map(fn($download) => $download->toApiArray())
        ]);
    }

    // Generate Secure Download Link (POST /api/downloads/{token}/link)
    public function generateDownloadLink($token)
    {
        $download = Download::where('download_token', $token)->first();

        if (!$download) {
            return response()->json(['message' => 'Invalid download token'], 404);
        }

        if (!$download->isValid()) {
            return response()->json(['message' => 'Download token expired or limit reached'], 403);
        }

        // Generate temporary download URL
        $downloadUrl = route('api.downloads.file', ['token' => $token]);

        return response()->json([
            'download_url' => $downloadUrl,
            'expires_in' => 300, // URL expires in 5 minutes
            'remaining_downloads' => $download->max_downloads - $download->download_count,
        ]);
    }

    // Secure File Download (GET /api/downloads/{token}/file)
    public function downloadFile($token)
    {
        $download = Download::where('download_token', $token)->first();

        if (!$download) {
            return response()->json(['message' => 'Invalid download token'], 404);
        }

        if (!$download->isValid()) {
            return response()->json(['message' => 'Download token expired or limit reached'], 403);
        }

        try {
            // Call Product Service to get the actual file
            $response = Http::get("http://localhost:8002/api/products/{$download->product_id}/files/{$download->file_id}/download");

            if ($response->successful()) {
                // Increment download count
                $download->increment('download_count');

                // Mark order item as downloaded
                $download->orderItem->update([
                    'is_downloaded' => true,
                    'downloaded_at' => now(),
                ]);

                // Return the file from Product Service
                return response($response->body())
                    ->header('Content-Type', $response->header('Content-Type'))
                    ->header('Content-Disposition', $response->header('Content-Disposition'));
            } else {
                return response()->json(['message' => 'File not found'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Download failed'], 500);
        }
    }

    // Get Download Details (GET /api/downloads/{token})
    public function show($token)
    {
        $download = Download::with('orderItem')->where('download_token', $token)->first();

        if (!$download) {
            return response()->json(['message' => 'Invalid download token'], 404);
        }

        return response()->json($download->toApiArray());
    }
}