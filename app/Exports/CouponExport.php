<?php

namespace App\Exports;

use App\Models\Coupon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CouponExport implements FromView,ShouldAutoSize
{
    public function __construct(
        public array $couponIds
    ) {
    }

    public function view(): View
    {
        $coupons = Coupon::whereIn('id', $this->couponIds)->with('teacher','subject','classModel')->get();
        return view('exports.coupons', compact('coupons'));
    }
}
