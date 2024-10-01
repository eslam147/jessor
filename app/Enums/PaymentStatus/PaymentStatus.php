<?php
namespace App\Enums\PaymentStatus;

use App\Traits\EnumToArray;

enum PaymentStatus: string
{
    use EnumToArray;
    case PAID = 'paid';
    case FREE = 'free';
    const DEFAULT = self::PAID->value; 

    public function color()
    {
        return match ($this) {
            self::PAID => 'primary',
            self::FREE => 'success',
        };
    }
    public function translatedName()
    {
        return match ($this) {
            self::PAID => trans('paid'),
            self::FREE => trans('free'),
        };
    }
}