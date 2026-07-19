<?php

namespace App\Support;

class CategoryGridVariants
{
    public const DEFAULT = 'cards';

    /**
     * All selectable category-listing styles, keyed by the value stored in the
     * `storefront_category_grid_variant` setting. Each key maps to a blade view:
     *   resources/views/components/public/category-grids/{key}.blade.php
     *
     * `columns` marks variants that honor the storefront "grid columns"
     * picker; the rest bring their own fixed responsive layout.
     *
     * @return array<string, array{name: string, description: string, columns: bool}>
     */
    public static function all(): array
    {
        return [
            'cards' => [
                'name' => __('Classic Cards'),
                'description' => __('The current design — image cards with product counts and subcategory chips.'),
                'columns' => true,
            ],
            'overlay' => [
                'name' => __('Photo Overlay'),
                'description' => __('Full-bleed category photo with the name and count on a bottom gradient scrim.'),
                'columns' => true,
            ],
            'minimal' => [
                'name' => __('Minimal Tiles'),
                'description' => __('No card background or border — just the image, a name, and a thin divider.'),
                'columns' => true,
            ],
            'circles' => [
                'name' => __('Circle Icons'),
                'description' => __('Round category images with centered names — a light, boutique directory look.'),
                'columns' => false,
            ],
            'list' => [
                'name' => __('List Rows'),
                'description' => __('Single-column horizontal rows — thumbnail left, name and subcategories right. Dense and scannable.'),
                'columns' => false,
            ],
            'banner' => [
                'name' => __('Wide Banners'),
                'description' => __('Panoramic two-per-row banners with the photo behind a large category name.'),
                'columns' => false,
            ],
            'compact' => [
                'name' => __('Compact Grid'),
                'description' => __('Small square tiles with tight spacing and more categories per row — best for large catalogs.'),
                'columns' => false,
            ],
            'showcase' => [
                'name' => __('Showcase'),
                'description' => __('The first category renders as a large feature banner, the rest in a grid below it.'),
                'columns' => false,
            ],
            'split' => [
                'name' => __('Split Cards'),
                'description' => __('Horizontal cards split in half — image on the left, name and subcategories on the right.'),
                'columns' => false,
            ],
            'noir' => [
                'name' => __('Noir Cards'),
                'description' => __('Cards on a near-black chip for a high-contrast, premium department look.'),
                'columns' => true,
            ],
        ];
    }

    /**
     * Whether the given variant honors the user-facing "grid columns" picker.
     */
    public static function supportsColumns(?string $key): bool
    {
        return static::all()[static::resolve($key)]['columns'];
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
