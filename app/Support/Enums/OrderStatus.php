<?php

namespace App\Support\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled, self::Expired], true);
    }

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->headline()->value();
    }
}
