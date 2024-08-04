<?php

namespace App\Services\Coupon;

use App\Models\User;
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
use App\Http\Requests\Dashboard\Coupon\CouponRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CouponService
{
    use Conditionable;
    public function __construct(private Coupon $model)
    {
    }

    public function findCoupon($couponId) : ?Coupon
    {
        return $this->model->where('code', $couponId)->first();
    }

    public function isCouponAvailable(Coupon $coupon, User $user, Lesson $action): array
    {
        $coupon->loadCount(['usages']);
        $action->load(['teacher', 'subject', 'classModel.allSubjects']);
        if ($coupon->is_disabled) {
            return $this->responseContent(__('coupon_errors_disabled'), false);
        }
        if ($coupon->onlyAppliedTo()->isNot($action)) {
            return $this->responseContent(__('coupon_errors_not_can_use'), false);
        }
        if (filled($coupon->expiry_date) && Carbon::parse($coupon->expiry_date)->isPast()) {
            return $this->responseContent(__('coupon_errors_expired'), false);
        }
        if ($coupon->usages_count >= $coupon->maximum_usage) {
            return $this->responseContent(__('coupon_errors_limited'), false);
        }

        if ($coupon->usages()->whereMorphedTo('usedByUser', $user)->whereMorphedTo('appliedTo', $action)->exists()) {
            return $this->responseContent(__('coupon_errors_already_used'), false);
        }

        if ($coupon->teacher_id != $action->teacher_id) {
            return $this->responseContent(__('coupon_errors_not_related_to_teacher'), false);
        }
        if ($this->model->classModel->id != $action->classModel->class_id) {
            return $this->responseContent(__('coupon_errors_not_related_to_class'), false);
        }
        if (isset($coupon->subject_id) && $coupon->subject_id != $action->subject_id && empty($this->model->classModel->allSubjects->firstWhere('subject_id', $coupon->subject_id))) {
            return $this->responseContent(__('coupon_errors_not_related_to_subject'), false);
        }

        return $this->responseContent(__('coupon_is_available'), true);
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
        $coupon->usages()->create([
            'applied_to_id' => $action->id,
            'applied_to_type' => get_class($action),
            // ----------------------------------------------- #
            'used_by_user_id' => $user->id,
            'used_by_user_type' => get_class($user),
        ]);
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

        $maxUsegeLimit = $request->maximum_usage;

        $couponData = [
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,

            'code' => $request->code,
            'expiry_date' => optional($expiryDate)->toDateTimeString(),
            'price' => null,
            'type' => CouponTypeEnum::PURCHASE,
            'maximum_usage' => $maxUsegeLimit,
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
        return tap($coupon, function ($coupon) use ($couponData) {
            $coupon->update($couponData);
        });
    }

    public function exportCouponCode($coupons)
    {
        $filePath = "exports/coupons.xlsx";
        Excel::store(new CouponExport($coupons), $filePath, 'public');
        return tenant_asset(Storage::url($filePath));
    }

    public function savePurchaseCoupons($request)
    {
        $ids = [];
        $lesson = Lesson::find($request->lesson_id);
        DB::transaction(function () use ($request, $lesson, &$ids) {
            $expiryDate = $this->when($request->expiry_date, fn() => Carbon::parse($request->expiry_date));

            for ($i = 0; $i < $request->coupons_count; $i++) {
                $ids[] = $this->storePurchaseCoupon(
                    teacherId: $request->teacher_id,
                    classId: $request->class_id,
                    subjectId: $request->subject_id,
                    expiryDate: $expiryDate,
                    price: $request->price,
                    maxUsageLimit: $request->usage_limit,
                    appliedTo: $lesson
                )->id;
            }

        });

        return $ids;
    }
    private function storePurchaseCoupon($teacherId = null, $subjectId = null, $classId = null, ?Carbon $expiryDate, $price, $maxUsageLimit, $appliedTo = null)
    {
        $couponData = [
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'class_id' => $classId,
            'code' => $this->generateCouponCode(),
            'expiry_date' => $expiryDate->toDateString(),
            // 'price' => 0,
            'type' => CouponTypeEnum::PURCHASE,
            'maximum_usage' => $maxUsageLimit,
        ];

        if (! is_null($appliedTo)) {
            $couponData['only_applied_to_id'] = $appliedTo->id;
            $couponData['only_applied_to_type'] = get_class($appliedTo);
        }

        return Coupon::create($couponData);
    }
    private function generateCouponCode($prefix = null)
    {
        $code = $prefix . fake()->numberBetween(2500);
        if ($this->model->where('code', $code)->exists()) {
            return $this->generateCouponCode();
        }
        return $code;
    }
}