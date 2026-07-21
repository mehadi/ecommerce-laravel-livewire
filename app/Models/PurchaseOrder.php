<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'order_number',
        'status',
        'notes',
        'ordered_at',
        'expected_at',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'expected_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PurchaseOrder $purchaseOrder) {
            if (! $purchaseOrder->order_number) {
                $purchaseOrder->order_number = 'PO-'.strtoupper(Str::random(8));
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function canBeReceived(): bool
    {
        return in_array($this->status, ['draft', 'ordered', 'partially_received']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'ordered', 'partially_received']);
    }
}
