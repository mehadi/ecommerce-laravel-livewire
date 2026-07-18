<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingCityRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'base_rate',
        'per_kg_rate',
        'base_weight_kg',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'per_kg_rate' => 'decimal:2',
            'base_weight_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function isRestOfAllCities(): bool
    {
        return $this->city_id === null;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isRestOfAllCities()) {
            return __('Rest of All Cities');
        }

        return $this->city?->name ?? __('Unknown City');
    }
}
