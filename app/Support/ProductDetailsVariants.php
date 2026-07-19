<?php

namespace App\Support;

class ProductDetailsVariants
{
    public const DEFAULT = 'classic';

    /**
     * All selectable product-details page styles, keyed by the value stored
     * in the `storefront_product_details_variant` setting. Each key maps to
     * a blade view: resources/views/components/public/product-details/{key}.blade.php
     *
     * @return array<string, array{name: string, description: string}>
     */
    public static function all(): array
    {
        return [
            'classic' => [
                'name' => __('Classic'),
                'description' => __('The current design — sticky image with thumbnails on the left, details on the right.'),
            ],
            'gallery-focus' => [
                'name' => __('Gallery Focus'),
                'description' => __('A larger image with a vertical thumbnail rail and a zoom-on-hover effect.'),
            ],
            'minimal' => [
                'name' => __('Minimal'),
                'description' => __('A smaller image and text-first details with plain dividers instead of cards.'),
            ],
            'split-sticky' => [
                'name' => __('Split Sticky'),
                'description' => __('The price and buy buttons stay pinned in view while the description scrolls below.'),
            ],
            'editorial' => [
                'name' => __('Editorial'),
                'description' => __('A magazine-style full-width image with a centered buy box and tabbed description/details.'),
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
