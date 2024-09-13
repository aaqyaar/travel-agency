<?php

namespace App\Enums;

enum PaymentMethod
{
    case Cash;
    case MMT;
    case Bank;
    case Other;

    // EnumsPaymentMethod::from($state)->label())

    public static function from(string $state): self
    {
        return match ($state) {
            'cash' => self::Cash,
            'mmt' => self::MMT,
            'bank' => self::Bank,
            'other' => self::Other,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::MMT => 'MMT',
            self::Bank => 'Bank',
            self::Other => 'Other',
        };
    }
}
