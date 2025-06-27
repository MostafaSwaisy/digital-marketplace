<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'filename',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'is_preview',
        'download_count',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_preview' => 'boolean',
        'download_count' => 'integer',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Generate secure filename
    public static function generateSecureFilename($originalName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . $extension;
    }

    // Get file size in human readable format
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Check if file exists in storage
    public function fileExists()
    {
        return Storage::disk('private')->exists($this->file_path);
    }

    // Delete file from storage
    public function deleteFile()
    {
        if ($this->fileExists()) {
            return Storage::disk('private')->delete($this->file_path);
        }
        return true;
    }

    // API response format
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_name' => $this->original_name,
            'file_type' => $this->file_type,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'formatted_size' => $this->getFormattedSizeAttribute(),
            'is_preview' => $this->is_preview,
            'download_count' => $this->download_count,
            'download_url' => $this->is_preview ? 
                route('api.products.files.download', ['product' => $this->product_id, 'file' => $this->id]) : 
                null, // Only show download URL for previews in API
            'exists' => $this->fileExists(),
            'created_at' => $this->created_at,
        ];
    }
}