<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NavbarComponent extends Model
{
    protected $fillable = [
        'key',
        'label',
        'zone_desktop',
        'order_desktop',
        'span_desktop',
        'is_visible_desktop',
        'order_mobile',
        'span_mobile',
        'is_visible_mobile',
    ];

    protected $casts = [
        'order_desktop' => 'integer',
        'span_desktop' => 'integer',
        'is_visible_desktop' => 'boolean',
        'order_mobile' => 'integer',
        'span_mobile' => 'integer',
        'is_visible_mobile' => 'boolean',
    ];

    /**
     * Scope to get visible components for a zone, ordered for that zone.
     */
    public function scopeForZone(Builder $query, string $zone): Builder
    {
        return $query->where("is_visible_{$zone}", true)->orderBy("order_{$zone}");
    }
}
