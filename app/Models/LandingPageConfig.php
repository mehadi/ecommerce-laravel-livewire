<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LandingPageConfig extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'landing_pages';

    protected $fillable = [
        'name',
        'slug',
        'product_id',
        'meta_title',
        'meta_description',
        'config',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'config' => 'array',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($landingPage) {
            if (empty($landingPage->slug)) {
                $landingPage->slug = Str::slug($landingPage->name);
            }
        });

        static::updating(function ($landingPage) {
            if ($landingPage->isDirty('name') && empty($landingPage->slug)) {
                $landingPage->slug = Str::slug($landingPage->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
