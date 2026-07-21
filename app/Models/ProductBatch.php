<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A specific received batch/lot of a `tracks_batches` product at a
 * warehouse. Scoped to simple (non-attribute) products for now — combining
 * batch tracking with attribute-based variants is a real future need, but
 * not one this build addresses.
 */
class ProductBatch extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'batch_number',
        'quantity',
        'expires_at',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'expires_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $withinDays = 30): bool
    {
        return $this->expires_at !== null
            && ! $this->isExpired()
            && $this->expires_at->isBefore(now()->addDays($withinDays));
    }
}
