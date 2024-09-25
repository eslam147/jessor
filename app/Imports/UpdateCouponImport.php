<?php

namespace App\Imports;

use App\Models\Coupon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\Coupon\CouponService;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class UpdateCouponImport implements ToCollection, WithHeadingRow, WithProgressBar
{
    use Importable;
    private readonly CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }


    public function collection(Collection $coupons)
    {
        if ($coupons->count() > 0) {
            DB::beginTransaction();
            foreach ($coupons->chunk(500) as $chunks) {
                Coupon::query()->whereIn('code', $chunks->pluck('coupon_code'))->update([
                    'expiry_date' => now()->addYear(),
                ]);
            }
            DB::commit();
        }
    }
}
