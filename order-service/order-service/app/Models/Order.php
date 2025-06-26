<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'total_amount',
        'platform_fee',
        'status',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_details',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'payment_details' => 'array',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Generate unique order number
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    // API response format
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'buyer_id' => $this->buyer_id,
            'total_amount' => $this->total_amount,
            'platform_fee' => $this->platform_fee,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_transaction_id' => $this->payment_transaction_id,
            'completed_at' => $this->completed_at,
            'items' => $this->items->map(fn($item) => $item->toApiArray()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
