<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'display_value',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function getDisplayValueAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        $unit = $this->attribute->unit;
        if ($unit) {
            return $this->value.' '.$unit;
        }

        return $this->value;
    }

    public function getWeightKgAttribute(): ?float
    {
        if (! $this->attribute->isWeight()) {
            return null;
        }

        return (float) $this->value;
    }
}
