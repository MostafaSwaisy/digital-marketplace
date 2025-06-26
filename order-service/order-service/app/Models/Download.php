<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'buyer_id',
        'product_id',
        'file_id',
        'download_token',
        'download_count',
        'max_downloads',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Generate secure download token
    public static function generateToken()
    {
        do {
            $token = Str::random(64);
        } while (self::where('download_token', $token)->exists());

        return $token;
    }

    // Check if download is still valid
    public function isValid()
    {
        return $this->download_count < $this->max_downloads
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    // API response format
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'file_id' => $this->file_id,
            'download_token' => $this->download_token,
            'download_count' => $this->download_count,
            'max_downloads' => $this->max_downloads,
            'expires_at' => $this->expires_at,
            'is_valid' => $this->isValid(),
        ];
    }
}
