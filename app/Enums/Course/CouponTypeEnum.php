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
            self::CHARGE_AMOUNT => trans('coupon.charge_amount'),
            self::PURCHASE => trans('coupon.charge_amount'),
            self::DISCOUNT => 'Discount',
        };
    }
}