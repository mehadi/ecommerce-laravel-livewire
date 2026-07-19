<?php

namespace App\Support;

class FooterVariants
{
    public const DEFAULT = 'classic';

    /**
     * All selectable storefront footer styles, keyed by the value stored in
     * the `storefront_footer_variant` setting. Each key maps to a blade view:
     *   resources/views/components/public/footers/{key}.blade.php
     *
     * @return array<string, array{name: string, description: string}>
     */
    public static function all(): array
    {
        return [
            'classic' => [
                'name' => __('Classic Columns'),
                'description' => __('The current design — brand block, link columns and contact details with icon chips.'),
            ],
            'centered' => [
                'name' => __('Centered Minimal'),
                'description' => __('Logo, tagline, inline links and social icons stacked in a single centered column. Clean and understated.'),
            ],
            'noir' => [
                'name' => __('Noir'),
                'description' => __('A premium near-black footer with high-contrast typography — regardless of light or dark mode.'),
            ],
            'mega' => [
                'name' => __('Mega CTA'),
                'description' => __('A bold call-to-action panel on top, then link columns and a trust-badge strip. Built to convert.'),
            ],
            'editorial' => [
                'name' => __('Editorial'),
                'description' => __('Oversized brand wordmark with hairline dividers and airy link rows — a fashion-magazine look.'),
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
