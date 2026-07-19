<?php

namespace App\Support;

class CartVariants
{
    public const DEFAULT = 'classic';

    /**
     * All selectable cart-content styles, keyed by the value stored in the
     * `storefront_cart_variant` setting. Each key maps to a blade view:
     *   resources/views/components/public/carts/{key}.blade.php
     *
     * This is independent of `cart_display_mode` (modal vs. slide-in panel),
     * which controls the surrounding shell rather than the content inside it.
     *
     * @return array<string, array{name: string, description: string}>
     */
    public static function all(): array
    {
        return [
            'classic' => [
                'name' => __('Classic'),
                'description' => __('The current design — image cards with a quantity stepper, coupon field, and a totals box.'),
            ],
            'compact' => [
                'name' => __('Compact'),
                'description' => __('Dense single-line rows with small thumbnails and inline controls — fits more items on screen.'),
            ],
            'minimal' => [
                'name' => __('Minimal'),
                'description' => __('Text-first rows with no product images or card backgrounds — quiet and understated.'),
            ],
            'detailed' => [
                'name' => __('Detailed'),
                'description' => __('Larger imagery with variant chips (size, color, etc.) and a per-line subtotal.'),
            ],
            'sidebar' => [
                'name' => __('Sidebar Dark'),
                'description' => __('High-contrast dark totals panel with a sticky checkout button — built for a slide-in drawer.'),
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
