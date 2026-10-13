<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'user_id',
        'address_id',
        'receiver_name',
        'receiver_phone',
        'shipping_address',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'deposit_required',
        'deposit_amount',
        'paid_amount',
        'remaining_amount',
        'order_status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deposit_required' => 'boolean',
            'deposit_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    /**
     * Order thuộc về user đã đặt hàng.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Địa chỉ được chọn khi checkout.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(
            Address::class,
            'address_id'
        );
    }

    /**
     * Các sản phẩm trong order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Các lần thanh toán của order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}