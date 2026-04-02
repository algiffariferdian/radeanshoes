<?php

namespace App\Support\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return in_array($this, [self::Paid, self::Failed, self::Expired, self::Cancelled], true);
    }

    public function label(): string
    {
        return str($this->value)->headline()->value();
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Paid => 'bg-emerald-100 text-emerald-800',
            self::Failed => 'bg-rose-100 text-rose-700',
            self::Expired => 'bg-stone-200 text-stone-700',
            self::Cancelled => 'bg-rose-100 text-rose-700',
        };
    }
}
