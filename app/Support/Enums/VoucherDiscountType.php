<?php

namespace App\Support\Enums;

enum VoucherDiscountType: string
{
    case Fixed = 'fixed';
    case Percent = 'percent';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Potongan Nominal',
            self::Percent => 'Potongan Persen',
        };
    }
}
