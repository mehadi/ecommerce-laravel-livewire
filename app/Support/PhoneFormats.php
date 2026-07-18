<?php

namespace App\Support;

class PhoneFormats
{
    /**
     * @var array<string, array{label: string, regex: string, placeholder: string}>
     */
    public const PRESETS = [
        'bd' => [
            'label' => 'Bangladesh (+880)',
            'regex' => '^01[0-9]{9}$',
            'placeholder' => '01XXXXXXXXX',
        ],
        'in' => [
            'label' => 'India (+91)',
            'regex' => '^[6-9][0-9]{9}$',
            'placeholder' => '9XXXXXXXXX',
        ],
        'intl' => [
            'label' => 'Generic International',
            'regex' => '^\+?[0-9]{7,15}$',
            'placeholder' => '+1234567890',
        ],
    ];

    public const DEFAULT_PRESET = 'bd';

    public static function regexFor(?string $preset): string
    {
        return static::PRESETS[$preset]['regex'] ?? static::PRESETS[static::DEFAULT_PRESET]['regex'];
    }

    public static function placeholderFor(?string $preset): string
    {
        return static::PRESETS[$preset]['placeholder'] ?? static::PRESETS[static::DEFAULT_PRESET]['placeholder'];
    }
}
