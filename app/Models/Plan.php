<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    public const BILLING_PERIODS = ['monthly', 'yearly'];

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_period',
        'max_products',
        'max_admin_users',
        'max_custom_domains',
        'features',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'max_products' => 'integer',
            'max_admin_users' => 'integer',
            'max_custom_domains' => 'integer',
            'features' => 'array',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * "$29.00/mo" or "$290.00/yr" — the formatted price plus its billing
     * cadence, for consistent display across the plans table, pricing cards,
     * and every tenant/billing plan picker.
     */
    public function priceLabel(): string
    {
        $suffix = $this->billing_period === 'yearly' ? __('/yr') : __('/mo');

        return '$'.number_format((float) $this->price, 2).$suffix;
    }

    /**
     * Monthly-equivalent price, for MRR math that sums plans regardless of
     * billing cadence — a yearly plan contributes price/12 to MRR, not the
     * full sticker price.
     */
    public function monthlyPrice(): float
    {
        return $this->billing_period === 'yearly' ? ((float) $this->price) / 12 : (float) $this->price;
    }

    /**
     * Whether this plan has a given capability flag turned on. `features` is
     * stored as a plain list of enabled flag keys (e.g. ['coupons_enabled']),
     * not a key => bool map.
     *
     * Note the inverted polarity vs. the max_* limit columns: there, null means
     * unlimited (permissive by default). Here, a missing key means the feature
     * is off (restrictive by default) — a flag must be explicitly enabled.
     */
    public function hasFeature(string $key): bool
    {
        return in_array($key, $this->features ?? [], true);
    }

    /**
     * Human-readable bullet strings for this plan's limits + feature flags, for
     * display on a pricing card. Null limits mean unlimited (see the cast/feature
     * doc comment above on the inverted null-polarity vs. features).
     */
    public function highlights(): array
    {
        $featureLabels = [
            'coupons_enabled' => __('Coupons & discounts'),
            'landing_pages_enabled' => __('Custom landing pages'),
            'advanced_analytics_enabled' => __('Advanced analytics'),
        ];

        $highlights = [
            $this->max_products === null
                ? __('Unlimited products')
                : __(':count products', ['count' => $this->max_products]),
            $this->max_admin_users === null
                ? __('Unlimited admin users')
                : trans_choice(':count admin user|:count admin users', $this->max_admin_users, ['count' => $this->max_admin_users]),
            $this->max_custom_domains === null
                ? __('Unlimited custom domains')
                : ($this->max_custom_domains > 0
                    ? trans_choice(':count custom domain|:count custom domains', $this->max_custom_domains, ['count' => $this->max_custom_domains])
                    : __('No custom domain')),
        ];

        foreach ($this->features ?? [] as $feature) {
            $highlights[] = $featureLabels[$feature] ?? $feature;
        }

        return $highlights;
    }
}
