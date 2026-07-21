<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_attribute_id',
        'warehouse_id',
        'product_name',
        'attribute_data',
        'price',
        'quantity',
        'subtotal',
        'stock_deducted',
    ];

    protected function casts(): array
    {
        return [
            'attribute_data' => 'array',
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
            'stock_deducted' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }

    /**
     * Units already refunded on this line — used to cap a new refund's
     * quantity so the same units can't be returned twice.
     */
    public function refundedQuantity(): int
    {
        return $this->refunds()->sum('quantity');
    }
}
