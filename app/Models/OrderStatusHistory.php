<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per observed order status transition (see OrderObserver), plus one
 * backfilled row per pre-existing order at the time this table was created.
 * A row count of exactly 1 for a given order means "no transition has been
 * observed since this table started tracking" — not real fulfillment history.
 */
class OrderStatusHistory extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
