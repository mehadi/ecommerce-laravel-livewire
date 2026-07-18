<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'unit',
        'is_required',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->orderBy('order');
    }

    public function activeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->where('is_active', true)->orderBy('order');
    }

    public function isWeight(): bool
    {
        return $this->slug === 'weight' || strtolower($this->name) === 'weight';
    }
}
