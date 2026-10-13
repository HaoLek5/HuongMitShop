<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'cost_price',
        'stock_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_values',
            'variant_id',
            'attribute_value_id'
        );
    }

    public function purchaseReceiptItems(): HasMany
    {
        return $this->hasMany(
            PurchaseReceiptItem::class,
            'variant_id'
        );
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(
            InventoryTransaction::class,
            'variant_id'
        );
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(
            CartItem::class,
            'variant_id'
        );
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(
            OrderItem::class,
            'variant_id'
        );
    }
}