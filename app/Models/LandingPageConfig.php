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

    /**
     * The historical fixed order/visibility, used as the starting point for
     * brand-new pages and as the fallback shape for pages saved before the
     * ordered block builder existed. Hero is intentionally excluded — it's
     * pinned first and non-draggable (tied to the buy-box/add-to-cart flow).
     *
     * @return array<int, array{type: string, enabled: bool}>
     */
    public static function defaultBlocks(): array
    {
        return [
            ['type' => 'trust_badges', 'enabled' => true],
            ['type' => 'product_details', 'enabled' => true],
            ['type' => 'features', 'enabled' => true, 'section_ids' => []],
            ['type' => 'testimonials', 'enabled' => true, 'testimonial_ids' => []],
            ['type' => 'about', 'enabled' => false, 'section_ids' => []],
            ['type' => 'benefits', 'enabled' => false, 'section_ids' => []],
            ['type' => 'faq', 'enabled' => true, 'section_ids' => []],
            ['type' => 'cta', 'enabled' => true],
        ];
    }

    /**
     * The page's ordered blocks. Reads the `blocks` key when present;
     * otherwise derives it from the legacy show_ and _ids config keys in
     * the historical fixed order, so pages saved before the block builder
     * existed keep rendering identically without a data migration.
     *
     * @return array<int, array<string, mixed>>
     */
    public function normalizedBlocks(): array
    {
        $config = $this->config ?? [];

        if (isset($config['blocks']) && is_array($config['blocks'])) {
            return $config['blocks'];
        }

        return [
            ['type' => 'trust_badges', 'enabled' => $config['show_trust_badges'] ?? true],
            ['type' => 'product_details', 'enabled' => $config['show_product_details'] ?? true],
            ['type' => 'features', 'enabled' => $config['show_features'] ?? true, 'section_ids' => $config['features_section_ids'] ?? []],
            ['type' => 'testimonials', 'enabled' => $config['show_testimonials'] ?? true, 'testimonial_ids' => $config['testimonial_ids'] ?? []],
            ['type' => 'about', 'enabled' => $config['show_about'] ?? false, 'section_ids' => $config['about_section_ids'] ?? []],
            ['type' => 'benefits', 'enabled' => $config['show_benefits'] ?? false, 'section_ids' => $config['benefits_section_ids'] ?? []],
            ['type' => 'contact', 'enabled' => $config['show_contact'] ?? false, 'section_ids' => $config['contact_section_ids'] ?? []],
            ['type' => 'products', 'enabled' => $config['show_products'] ?? false, 'section_ids' => $config['products_section_ids'] ?? []],
            ['type' => 'faq', 'enabled' => $config['show_faq'] ?? true, 'section_ids' => $config['faq_section_ids'] ?? []],
            ['type' => 'cta', 'enabled' => $config['show_cta'] ?? true],
        ];
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
