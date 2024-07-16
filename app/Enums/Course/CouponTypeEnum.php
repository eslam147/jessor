<?php
namespace App\Enums\Course;

use App\Traits\EnumToArray;

enum CouponTypeEnum: string
{
    use EnumToArray;
    case CHARGE_AMOUNT = 'charge_amount';
    case PURCHASE = 'purchase';
    case DISCOUNT = 'discount';
}