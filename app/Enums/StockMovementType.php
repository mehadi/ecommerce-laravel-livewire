<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Sale = 'sale';
    case Return = 'return';
    case Adjustment = 'adjustment';
    case TransferOut = 'transfer_out';
    case TransferIn = 'transfer_in';
    case Receiving = 'receiving';
    case CycleCount = 'cycle_count';
    case Reservation = 'reservation';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Sale',
            self::Return => 'Return',
            self::Adjustment => 'Adjustment',
            self::TransferOut => 'Transfer Out',
            self::TransferIn => 'Transfer In',
            self::Receiving => 'Receiving',
            self::CycleCount => 'Cycle Count',
            self::Reservation => 'Reservation',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Sale => 'subtle',
            self::Return => 'success',
            self::Adjustment => 'warning',
            self::TransferOut => 'info',
            self::TransferIn => 'info',
            self::Receiving => 'success',
            self::CycleCount => 'primary',
            self::Reservation => 'subtle',
        };
    }
}
