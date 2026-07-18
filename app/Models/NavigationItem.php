<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    protected $fillable = [
        'parent_id',
        'label_en',
        'label_bn',
        'icon',
        'url',
        'type',
        'route_name',
        'order',
        'is_active',
        'open_in_new_tab',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    /**
     * Get the label based on current locale.
     */
    public function getLabelAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'bn' => $this->label_bn ?? $this->label_en,
            default => $this->label_en,
        };
    }

    /**
     * Get the resolved URL.
     */
    public function getResolvedUrlAttribute(): string
    {
        return match ($this->type) {
            'route' => $this->route_name ? route($this->route_name) : $this->url,
            'section' => $this->url,
            default => $this->url,
        };
    }

    /**
     * Scope to get only active items ordered by order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Get the parent navigation item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    /**
     * Get the child navigation items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get active child navigation items.
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Check if this item has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->where('is_active', true)->exists();
    }
}
