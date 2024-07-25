<?php
namespace App\Enums\Course;

use App\Traits\EnumToArray;

use function PHPUnit\Framework\matches;

enum CouponTypeEnum: string
{
    use EnumToArray;
    case CHARGE_AMOUNT = 'charge_amount';
    case PURCHASE = 'purchase';
    case DISCOUNT = 'discount';
    public function translatedName(){
        return match($this){
            self::CHARGE_AMOUNT => 'Charge Amount',
            self::PURCHASE => 'Purchase',
            self::DISCOUNT => 'Discount',
        };
    }
}