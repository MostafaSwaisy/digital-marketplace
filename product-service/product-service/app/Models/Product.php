<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'seller_id',
        'category',
        'tags',
        'status',
        'is_featured',  // Make sure this is here
        'downloads_count',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'tags' => 'array',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }

    public function downloadableFiles()
    {
        return $this->hasMany(ProductFile::class)->where('is_preview', false);
    }

    public function previewFiles()
    {
        return $this->hasMany(ProductFile::class)->where('is_preview', true);
    }

    // API response format
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'seller_id' => $this->seller_id,
            'category' => $this->category,
            'tags' => $this->tags,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'downloads_count' => $this->downloads_count,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'files' => $this->files->map(fn($file) => $file->toApiArray()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
