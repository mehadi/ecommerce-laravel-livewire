<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'type',
        'flat_rate',
        'base_weight_kg',
        'base_rate',
        'per_kg_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'flat_rate' => 'decimal:2',
            'base_weight_kg' => 'decimal:2',
            'base_rate' => 'decimal:2',
            'per_kg_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function isFlat(): bool
    {
        return $this->type === 'flat';
    }

    public function isWeight(): bool
    {
        return $this->type === 'weight';
    }

    public function isCity(): bool
    {
        return $this->type === 'city';
    }
}
