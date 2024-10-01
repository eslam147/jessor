<?php

namespace App\Imports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\Coupon\CouponService;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class CouponImport implements ToCollection, WithHeadingRow, WithProgressBar
{
    use Importable;
    private readonly CouponService $couponService;
    public array $tags = [
        'importedBySemiColon',
    ];
    // php artisan import:coupons --tenant=infin1 --file=2000_all --tags=at_12_september_2024 --tags=importedBySemiColon 
    public $subjectId = null;
    public $teacherId = null;
    public $classId = null;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }
    public function setData($tags, $classId, $subjectId, $teacherId)
    {
        $this->tags = $tags;
        $this->subjectId = $subjectId;
        $this->classId = $classId;
        $this->teacherId = $teacherId;
    }

    public function collection(Collection $coupons)
    {
        $maxUsageLimit = 5;
        if ($coupons->count() > 0) {
            DB::beginTransaction();
            foreach ($coupons as $row) {
                if (isset($row['student_used']) && ! empty($row['student_used'])) {
                    continue;
                }
                $expiryDate = now()->addYear();
                $this->couponService->storePurchaseCoupon(
                    teacherId: $this->teacherId,
                    subjectId: $this->subjectId,
                    classId: $this->classId,
                    expiryDate: $expiryDate,
                    price: $row['price'],
                    maxUsageLimit: $maxUsageLimit,
                    code: $row['coupon_code'],
                    tags: $this->tags,
                );
            }
            DB::commit();
        }
    }
}
