<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'parent_id',
        'name_en',
        'name_bn',
        'slug',
        'description_en',
        'description_bn',
        'image',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getAllProductsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        $products = $this->products;
        foreach ($this->children as $child) {
            $products = $products->merge($child->allProducts);
        }

        return $products;
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->name_bn ? $this->name_bn : $this->name_en;
    }

    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'bn' && $this->description_bn ? $this->description_bn : $this->description_en;
    }

    public function isSubcategory(): bool
    {
        return $this->parent_id !== null;
    }

    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_path.' > '.$this->name_en;
        }

        return $this->name_en;
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    /**
     * Check if category is in navigation.
     */
    public function isInNavigation(): bool
    {
        $query = \Illuminate\Support\Facades\DB::table('navigation_categories')
            ->where('category_id', $this->id);

        if (\App\Support\Tenancy::check()) {
            $query->where('tenant_id', \App\Support\Tenancy::id());
        }

        return $query->exists();
    }
}
