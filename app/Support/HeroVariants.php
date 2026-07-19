<?php

namespace App\Support;

class HeroVariants
{
    public const DEFAULT = 'bento';

    /**
     * All selectable storefront hero styles, keyed by the value stored in the
     * `storefront_hero_variant` setting. Each key maps to a blade view at
     * resources/views/components/public/heroes/{key}.blade.php.
     *
     * @return array<string, array{name: string, description: string}>
     */
    public static function all(): array
    {
        return [
            'bento' => [
                'name' => __('Bento Grid'),
                'description' => __('Dashboard-style grid of cards with product, testimonials, sizes, and live store stats.'),
            ],
            'classic' => [
                'name' => __('Classic Split'),
                'description' => __('Timeless two-column layout — copy and buttons on the left, product image on the right.'),
            ],
            'centered' => [
                'name' => __('Centered Minimal'),
                'description' => __('Clean centered headline and call-to-action with a wide product banner underneath.'),
            ],
            'gradient' => [
                'name' => __('Gradient Glow'),
                'description' => __('Rich dark gradient panel in your brand colors with a glowing product visual.'),
            ],
            'overlay' => [
                'name' => __('Image Overlay'),
                'description' => __('Full-width hero photo with a dark overlay and bold text anchored at the bottom.'),
            ],
            'split' => [
                'name' => __('Split Screen'),
                'description' => __('Edge-to-edge 50/50 split — solid brand-color panel beside a full-bleed image.'),
            ],
            'showcase' => [
                'name' => __('Product Showcase'),
                'description' => __('Product on a center pedestal surrounded by floating stat chips and a bold headline.'),
            ],
            'editorial' => [
                'name' => __('Editorial'),
                'description' => __('Magazine-style oversized headline with thin rules, meta details, and a framed image.'),
            ],
            'collage' => [
                'name' => __('Collage Cards'),
                'description' => __('Playful stack of tilted photo cards with a floating customer review chip.'),
            ],
            'spotlight' => [
                'name' => __('Dark Spotlight'),
                'description' => __('Moody dark stage with a glowing spotlight on the product and a stats strip.'),
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
