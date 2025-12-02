<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionCategory: string
{
case Donation = 'donation';
case Loan = 'loan';
case Refund = 'refund';
case Membership = 'membership';
case Other = 'other';

    public function label(): string
{
    return match ($this) {
        self::Donation => 'Дарунок',
        self::Loan => 'Позика',
        self::Refund => 'Повернення оплати',
        self::Membership => 'Членський внесок',
        self::Other => 'Інше',
    };
}

    public function color(): string
{
    return match ($this) {
        self::Donation => 'success',
        self::Loan => 'warning',
        self::Refund => 'info',
        self::Membership => 'primary',
        self::Other => 'gray',
    };
}

    public static function options(): array
{
    return collect(self::cases())
        ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
        ->toArray();
}
}
