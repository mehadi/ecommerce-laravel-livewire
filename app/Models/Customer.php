<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'store_credit_balance',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'store_credit_balance' => 'decimal:2',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function hasSufficientStoreCredit(float $amount): bool
    {
        return $this->store_credit_balance >= $amount;
    }
}
