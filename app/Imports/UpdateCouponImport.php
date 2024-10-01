<?php

namespace App\Imports;

use App\Models\Coupon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class UpdateCouponImport implements ToCollection, WithHeadingRow, WithProgressBar
{
    use Importable;

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
