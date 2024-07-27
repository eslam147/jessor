<?php

namespace App\Exports;

use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\FromCollection;

class CouponExport implements FromCollection
{
    public function __construct(
        public array $couponIds
    ) {}


    public function collection()
    {
        return Coupon::whereIn('id', $this->couponIds)->select('code')->get();
    }
}
