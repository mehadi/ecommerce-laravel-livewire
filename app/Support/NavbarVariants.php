<?php

namespace App\Support;

class NavbarVariants
{
    public const DEFAULT = 'classic';

    /**
     * All selectable storefront header styles, keyed by the value stored in the
     * `storefront_header_variant` setting. Each key maps to a blade view at
     * resources/views/components/public/navbars/{key}.blade.php.
     *
     * @return array<string, array{name: string, description: string}>
     */
    public static function all(): array
    {
        return [
            'classic' => [
                'name' => __('Floating Pill'),
                'description' => __('The signature floating rounded bar that lifts off the page, with fully drag-and-drop configurable components.'),
            ],
            'minimal' => [
                'name' => __('Minimal Line'),
                'description' => __('Clean full-width bar with a thin bottom border, text-only links, and no shadows — quiet and boutique.'),
            ],
            'centered' => [
                'name' => __('Centered Logo'),
                'description' => __('Two-tier header: a slim utility row on top, then links flanking a perfectly centered logo below.'),
            ],
            'transparent' => [
                'name' => __('Transparent Overlay'),
                'description' => __('Starts see-through on top of your hero image, then fades to a solid blurred bar as the visitor scrolls.'),
            ],
            'bold' => [
                'name' => __('Bold Statement'),
                'description' => __('High-contrast dark bar with a prominent search field and a secondary category strip underneath.'),
            ],
        ];
    }

    public static function keys(): array
    {
        return array_keys(static::all());
    }

    public static function resolve(?string $key): string
    {
        return array_key_exists((string) $key, static::all()) ? $key : static::DEFAULT;
    }
}
