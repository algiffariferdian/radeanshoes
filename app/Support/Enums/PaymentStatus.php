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
}
