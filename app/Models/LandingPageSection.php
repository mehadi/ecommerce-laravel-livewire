<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title_en',
        'title_bn',
        'content_en',
        'content_bn',
        'data',
        'image',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getTitleAttribute(): ?string
    {
        return app()->getLocale() === 'bn' && $this->title_bn ? $this->title_bn : $this->title_en;
    }

    public function getContentAttribute(): ?string
    {
        return app()->getLocale() === 'bn' && $this->content_bn ? $this->content_bn : $this->content_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
