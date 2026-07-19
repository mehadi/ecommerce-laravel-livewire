<?php

namespace App\Support;

class ProductGridVariants
{
    public const DEFAULT = 'grid';

    /**
     * All selectable product-listing styles, keyed by the value stored in the
     * `storefront_shop_grid_variant` / `storefront_featured_grid_variant` settings.
     * Each key maps to a pair of blade views:
     *   resources/views/components/public/product-grids/{key}.blade.php (arrangement)
     *   resources/views/components/public/product-cards/{key}.blade.php (card design)
     *
     * @return array<string, array{name: string, description: string}>
     */
    public static function all(): array
    {
        return [
            'grid' => [
                'name' => __('Classic Grid'),
                'description' => __('The current design — balanced cards in a responsive grid with a quick-add button.'),
            ],
            'minimal' => [
                'name' => __('Minimal Grid'),
                'description' => __('No card background or border, just a thin divider — clean and understated.'),
            ],
            'list' => [
                'name' => __('List View'),
                'description' => __('Single-column horizontal rows — image on the left, details on the right. Dense and scannable.'),
            ],
            'compact' => [
                'name' => __('Compact Grid'),
                'description' => __('Tighter spacing and more items per row — best for large, browsable catalogs.'),
            ],
            'scrim' => [
                'name' => __('Scrim Card'),
                'description' => __('Full-bleed product photo with the name and price on a bottom gradient overlay.'),
            ],
            'feature' => [
                'name' => __('Feature Grid'),
                'description' => __('The first product renders as a large feature card, the rest in a grid below it.'),
            ],
            'masonry' => [
                'name' => __('Masonry Grid'),
                'description' => __('Pinterest-style columns with variable card heights.'),
            ],
            'outline' => [
                'name' => __('Outline Cards'),
                'description' => __('Flat cards with just a border outline — no fill or shadow.'),
            ],
            'noir' => [
                'name' => __('Noir Cards'),
                'description' => __('Cards on a near-black chip for a high-contrast, premium catalog look.'),
            ],
            'promo' => [
                'name' => __('Promo Grid'),
                'description' => __('Bigger discount ribbons and price-forward layout — built for sales and clearance.'),
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(static::all());
    }

    /**
     * Return a valid variant key, falling back to the default when the stored
     * value is missing or no longer exists.
     */
    public static function resolve(?string $key): string
    {
        return array_key_exists((string) $key, static::all()) ? $key : static::DEFAULT;
    }
}
