<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CycleCountItem extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'cycle_count_id',
        'product_id',
        'product_attribute_id',
        'expected_quantity',
        'counted_quantity',
        'counted_by',
        'counted_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_quantity' => 'integer',
            'counted_quantity' => 'integer',
            'counted_at' => 'datetime',
        ];
    }

    public function cycleCount(): BelongsTo
    {
        return $this->belongsTo(CycleCount::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function discrepancy(): ?int
    {
        return $this->counted_quantity === null ? null : $this->counted_quantity - $this->expected_quantity;
    }
}
