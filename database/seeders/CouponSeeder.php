<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\Coupon\CouponService;

class CouponSeeder extends Seeder
{
    public function __construct(
        private CouponService $couponService
    ) {
    }

    public function run()
    {
        for ($i = 0; $i < 3; $i++) {
            $lesson = Lesson::inRandomOrder()->with('class_section')->first();
            DB::transaction(function () use ($lesson) {
                for ($i = 0; $i < rand(1, 10); $i++) {
                    $services = [
                        'lesson_id' => $lesson->id,
                        'expiry_date' => now()->addDays(rand(1, 30))->toDateString(),
                        'coupons_count' => rand(1, 10),
                        'teacher_id' => $lesson->teacher_id,
                        'price' => rand(100, 1000),
                        'usage_limit' => rand(1, 10),
                        'class_id' => $lesson->class_section->class_id,
                        'subject_id' => $lesson->subject_id,
                    ];
                    $this->couponService->savePurchaseCoupons((object) $services);
                }
            });
        }

    }
}
