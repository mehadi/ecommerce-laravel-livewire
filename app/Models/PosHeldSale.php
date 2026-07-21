<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosHeldSale extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'register_id',
        'held_by',
        'customer_id',
        'cart_snapshot',
        'note',
        'held_at',
    ];

    protected function casts(): array
    {
        return [
            'cart_snapshot' => 'array',
            'held_at' => 'datetime',
        ];
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(PosRegister::class, 'register_id');
    }

    public function heldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'held_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
