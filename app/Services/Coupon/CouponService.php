<?php

namespace App\Services\Coupon;

use App\Models\User;
use Spatie\Tags\Tag;
use App\Models\Coupon;
use App\Models\Lesson;
use App\Exports\CouponExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\Course\CouponTypeEnum;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Conditionable;
use Bavix\Wallet\Services\AtomicServiceInterface;
use App\Http\Requests\Dashboard\Coupon\CouponRequest;

class CouponService
{
    use Conditionable;
    public function __construct(private Coupon $model)
    {
    }

    public function findCoupon($couponId, CouponTypeEnum $type = CouponTypeEnum::PURCHASE): ?Coupon
    {
        return $this->model->where('code', $couponId)->where('type', $type)->first();
    }

    public function isCouponAvailableToWallet(Coupon $coupon, User $user): array
    {
        // Step 1: Load related data for coupon and action
        $coupon->load(['usages']);

        // Step 2: Check if the coupon is disabled
        if ($coupon->is_disabled) {
            return $this->responseContent(__('coupon_errors_disabled'), false);
        }

        // Step 3: Verify the coupon's applicability to the specific user
        if (
            $coupon->usages->where(function ($query) use ($user) {
                $query->whereNotNull('used_by_user_id')
                    ->where('used_by_user_id', '!=', $user->id);
            })->count()
        ) {
            return $this->responseContent(__('coupon_errors_used_by_others'), false);
        }

        // Step 4: Check if the coupon is expired
        if (filled($coupon->expiry_date) && Carbon::parse($coupon->expiry_date)->isPast()) {
            return $this->responseContent(__('coupon_errors_expired'), false);
        }

        // Step 5: Check if the coupon has reached its usage limit
        if ($coupon->usages->count() >= $coupon->maximum_usage) {
            return $this->responseContent(__('coupon_errors_limited'), false);
        }

        return $this->responseContent(__('coupon_is_available'), true);
    }
    public function isCouponAvailable(Coupon $coupon, User $user, Lesson $lesson): array
    {
        // Step 1: Load related data for coupon and lesson
        $coupon->load(['usages', 'classModel', 'onlyAppliedTo']);
        $lesson->load(['teacher', 'subject', 'class.allSubjects']);

        // Step 2: Check if the coupon is disabled
        if ($coupon->is_disabled) {
            return $this->responseContent(__('coupon_errors_disabled'), false);
        }

        // Step 3: Verify the coupon's applicability to the specific lesson
        if (! empty($coupon->onlyAppliedTo) && $coupon->onlyAppliedTo()->isNot($lesson)) {
            return $this->responseContent(__('coupon_errors_not_can_use'), false);
        }

        // Step 3: Verify the coupon's applicability to the specific user
        if (
            $coupon->usages->where(function ($query) use ($user) {
                $query->whereNotNull('used_by_user_id') // Ensure used_by_user_id is not null
                    ->where('used_by_user_id', '!=', $user->id); // Check it's not the current user
            })->count()
        ) {
            return $this->responseContent(__('coupon_errors_used_by_others'), false);
        }
        if (! is_null($coupon->price)) {
            if ($coupon->usages->sum('amount') >= $coupon->price) {
                return $this->responseContent(__('coupon_errors_usage_price_limit'), false);
            }
            if ($lesson->price > $coupon->price) {
                return $this->responseContent(__('coupon_errors_price_limit'), false);
            }
        }

        // Step 4: Check if the coupon is expired
        if (filled($coupon->expiry_date) && Carbon::parse($coupon->expiry_date)->isPast()) {
            return $this->responseContent(__('coupon_errors_expired'), false);
        }

        // Step 5: Check if the coupon has reached its usage limit
        if ($coupon->usages->count() >= $coupon->maximum_usage) {
            return $this->responseContent(__('coupon_errors_limited'), false);
        }

        // ------------------------- \\
        $usageCount = $coupon->usages->filter(function ($usage) use ($user, $lesson) {
            return $usage->usedByUser->is($user) && $usage->appliedTo->is($lesson);
        })->count();

        // Step 6: Check if the coupon has already been used by the user for this lesson
        if ($usageCount) {
            return $this->responseContent(__('coupon_errors_already_used'), false);
        }
        //   // Step 7: Validate the coupon's teacher association
        if (! empty($coupon->teacher_id) && $coupon->teacher_id != $lesson->teacher_id) {
            return $this->responseContent(__('coupon_errors_not_related_to_teacher'), false);
        }

        // Step 8: Validate the coupon's class association
        if (isset($coupon->class_id) && $coupon->classModel->id != $lesson->class->id) {
            return $this->responseContent(__('coupon_errors_not_related_to_class'), false);
        }

        // Step 9: Validate the coupon's subject association
        if (isset($coupon->subject_id) && $coupon->subject_id != $lesson->subject_id && empty($this->model->classModel->allSubjects->firstWhere('subject_id', $coupon->subject_id))) {
            return $this->responseContent(__('coupon_errors_not_related_to_subject'), false);
        }

        // Final Step: Return success if all checks are passed
        return $this->responseContent(__('coupon_is_available'), true);
    }
    public function applyCouponToWallet(User $user, string $couponCode)
    {
        $coupon = $this->findCoupon($couponCode, CouponTypeEnum::CHARGE_AMOUNT);
        if (! $coupon) {
            return $this->responseContent(__('coupon_errors_is_not_available'), false);
        }
        $wallet = $user->wallet;

        $couponCheck = $this->isCouponAvailableToWallet($coupon, $user);
        if (! $couponCheck['status']) {
            return $this->responseContent($couponCheck['message'] ?? __('coupon_errors_is_not_available'), false);
        }
        app(AtomicServiceInterface::class)->block($wallet, function () use ($wallet, $user, $coupon) {
            $wallet->deposit($coupon->price, meta: [
                'description' => "Coupon {$coupon->code} applied to wallet"
            ]);
            $this->usageStore($coupon, $user, $wallet, $coupon->price);
        });

        return $this->responseContent(__('coupon_applied_successfully'), true);
    }
    public function redeemCoupon(User $user, string $couponCode, Model $action)
    {
        $coupon = $this->findCoupon($couponCode);
        if (! $coupon) {
            return $this->responseContent(__('coupon_errors_is_not_available'), false);
        }

        $couponCheck = $this->isCouponAvailable($coupon, $user, $action);

        if (! $couponCheck['status']) {
            return $this->responseContent($couponCheck['message'] ?? __('coupon_errors_is_not_available'), false);
        }

        $this->usageStore($coupon, $user, $action, $action->price ?? 0);

        return $this->responseContent(__('coupon_applied_successfully'), true);
    }

    private function responseContent(string $msg, bool $status = false): array
    {
        return [
            'message' => $msg,
            'status' => $status
        ];
    }
    public function updateCoupon(
        CouponRequest $request,
        Coupon $coupon
    ) {
        $expiryDate = $this->when($request->expiry_date, fn() => Carbon::parse("{$request->expiry_date} {$request->expiry_time}"));


        $couponData = [
            'type' => CouponTypeEnum::PURCHASE,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'expiry_date' => optional($expiryDate)->toDateTimeString(),
            'price' => $request->price,
            'maximum_usage' => $request->usage_limit,
        ];
        // ----------------------------------------------- #
        $lesson = Lesson::find($request->lesson_id);
        if ($lesson) {
            if ($coupon->onlyAppliedTo()->isNot($lesson)) {
                $couponData['only_applied_to_id'] = $lesson->id;
                $couponData['only_applied_to_type'] = get_class($lesson);
            }
        }
        // ----------------------------------------------- #
        $coupon = tap($coupon, function ($coupon) use ($couponData) {
            $coupon->update($couponData);
        });
        $this->insertTags(explode(',', $request->tags), $coupon);
        return $coupon;
    }
    public function usageStore(
        Coupon $coupon,
        User $user,
        Model $action,
        $amount = 0
    ) {
        return $coupon->usages()->create([
            'applied_to_id' => $action->id,
            'applied_to_type' => get_class($action),
            // ----------------------------------------------- #
            'used_by_user_id' => $user->id,
            'used_by_user_type' => get_class($user),
            'amount' => $amount
        ]);
    }
    public function exportCouponCode($coupons)
    {
        $fileName = "coupons_" . time() . ".xlsx";

        $fullTenantStoragePath = "temp/exports/coupons/" . $fileName;

        Excel::store(new CouponExport($coupons), $fullTenantStoragePath, 'public');

        return [
            'url' => tenant_asset($fullTenantStoragePath),
            'name' => $fileName
        ];
    }

    public function saveWalletCoupons($request)
    {
        $ids = [];

        DB::transaction(function () use ($request, &$ids) {
            $expiryDate = $this->when($request->expiry_date, fn() => Carbon::parse($request->expiry_date));

            for ($i = 0; $i < $request->coupons_count; $i++) {
                $ids[] = $this->storeWalletCoupon(
                    expiryDate: $expiryDate,
                    price: $request->price,
                    tags: explode(',', $request->tags),
                )->id;
            }
        });

        return $ids;
    }

    public function savePurchaseCoupons($request)
    {
        $ids = [];
        $lesson = Lesson::find($request->lesson_id);

        DB::transaction(function () use ($request, $lesson, &$ids) {
            $expiryDate = $this->when($request->expiry_date, fn() => Carbon::parse($request->expiry_date));
            $tags = explode(',', $request->tags ?? "");
            for ($i = 0; $i < $request->coupons_count; $i++) {
                $couponCode = $this->generateCouponCode();
                $ids[] = $this->storePurchaseCoupon(
                    teacherId: $request->teacher_id,
                    classId: $request->class_id,
                    subjectId: $request->subject_id,
                    expiryDate: $expiryDate,
                    price: $request->price,
                    maxUsageLimit: $request->usage_limit,
                    appliedTo: $lesson,
                    tags: $tags,
                    code: $couponCode,
                )->id;
            }
        });

        return $ids;
    }
    private function storeWalletCoupon(
        ?Carbon $expiryDate,
        $price = null,
        array $tags = []
    ) {
        $couponData = [
            'code' => $this->generateCouponCode(),
            'expiry_date' => $expiryDate->toDateString(),
            'price' => $price,
            'type' => CouponTypeEnum::CHARGE_AMOUNT,
            'maximum_usage' => 1,
        ];

        // if (! is_null($appliedTo)) {
        //     $couponData['only_applied_to_id'] = $appliedTo->id;
        //     $couponData['only_applied_to_type'] = get_class($appliedTo);
        // }
        $coupon = Coupon::create($couponData);
        $this->insertTags($tags, $coupon);
        return $coupon;
    }
    public function storePurchaseCoupon(
        $teacherId = null,
        $subjectId = null,
        $classId = null,
        ?Carbon $expiryDate,
        $price = null,
        $maxUsageLimit,
        $appliedTo = null,
        array $tags = [],
        $code = null
    ) {
        $couponData = [
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'class_id' => $classId,
            'expiry_date' => $expiryDate->toDateString(),
            'price' => $price,
            'type' => CouponTypeEnum::PURCHASE,
            'maximum_usage' => $maxUsageLimit,
        ];
        if (! empty($code)) {
            if ($this->model->where('code', $code)->exists()) {
                return null;
            }
            $couponData['code'] = $code;
        } else {
            $couponData['code'] = $this->generateCouponCode();
        }
        if (! is_null($appliedTo)) {
            $couponData['only_applied_to_id'] = $appliedTo->id;
            $couponData['only_applied_to_type'] = get_class($appliedTo);
        }
        $coupon = Coupon::create($couponData);
        $this->insertTags($tags, $coupon);
        return $coupon;
    }
    private function generateCouponCode($prefix = null)
    {
        $code = $prefix . fake()->numberBetween(2500);
        if ($this->model->where('code', $code)->exists()) {
            return $this->generateCouponCode();
        }
        return $code;
    }
    private function insertTags(array $tags, Coupon $coupon)
    {
        $tags = array_filter(array_map('trim', $tags));
        $coupon->syncTags($tags);
    }
}