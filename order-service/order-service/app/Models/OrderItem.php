<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
       protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'product_name',
        'price',
        'seller_amount',
        'is_downloaded',
        'downloaded_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'is_downloaded' => 'boolean',
        'downloaded_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    // API response format
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'seller_id' => $this->seller_id,
            'product_name' => $this->product_name,
            'price' => $this->price,
            'seller_amount' => $this->seller_amount,
            'is_downloaded' => $this->is_downloaded,
            'downloaded_at' => $this->downloaded_at,
        ];
    }
}
