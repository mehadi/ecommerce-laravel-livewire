<?php

namespace App\Support;

class CheckoutVariants
{
    public const DEFAULT = 'classic';

    /**
     * All selectable checkout-content styles, keyed by the value stored in the
     * `storefront_checkout_variant` setting. Each key maps to a blade view:
     *   resources/views/components/public/checkouts/{key}.blade.php
     *
     * This is independent of `checkout_display_mode` (modal vs. slide-in
     * panel), which controls the surrounding shell rather than the content.
     *
     * `wide` marks variants that need a wider modal shell (e.g. a two-column
     * layout) than the default single-column max width.
     *
     * @return array<string, array{name: string, description: string, wide: bool}>
     */
    public static function all(): array
    {
        return [
            'classic' => [
                'name' => __('Classic'),
                'description' => __('The current design — order summary card on top, customer details form below.'),
                'wide' => false,
            ],
            'split' => [
                'name' => __('Split Columns'),
                'description' => __('Order summary and the customer form side by side — makes the most of a wide screen.'),
                'wide' => true,
            ],
            'compact' => [
                'name' => __('Compact'),
                'description' => __('Condensed spacing and smaller fields — a tighter fit for a slide-in panel.'),
                'wide' => false,
            ],
            'minimal' => [
                'name' => __('Minimal'),
                'description' => __('Stripped-down styling with plain dividers instead of cards — quiet and distraction-free.'),
                'wide' => false,
            ],
            'steps' => [
                'name' => __('Sectioned Steps'),
                'description' => __('The same single-page form broken into clearly numbered sections — summary, details, review.'),
                'wide' => false,
            ],
        ];
    }

    /**
     * Whether the given variant needs the wider modal shell.
     */
    public static function isWide(?string $key): bool
    {
        return static::all()[static::resolve($key)]['wide'];
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
