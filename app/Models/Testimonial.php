<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'content_en',
        'content_bn',
        'image',
        'rating',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function getContentAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->content_bn ? $this->content_bn : $this->content_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
