<?php

namespace App\Enums;

enum WastageReason: string
{
    case Damage = 'damage';
    case Expiry = 'expiry';
    case Theft = 'theft';
    case Spoilage = 'spoilage';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Damage => 'Damage',
            self::Expiry => 'Expiry',
            self::Theft => 'Theft',
            self::Spoilage => 'Spoilage',
            self::Other => 'Other',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Damage => 'warning',
            self::Expiry => 'danger',
            self::Theft => 'danger',
            self::Spoilage => 'warning',
            self::Other => 'subtle',
        };
    }
}
