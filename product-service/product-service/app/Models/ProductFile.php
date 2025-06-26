<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_preview',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_preview' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // API response format
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_name' => $this->original_name,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'is_preview' => $this->is_preview,
            'download_url' => $this->is_preview ? null : route('api.products.files.download', ['product' => $this->product_id, 'file' => $this->id]),
        ];
    }
}
