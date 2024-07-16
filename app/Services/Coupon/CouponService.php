<?php

namespace App\Services\Coupon;

use App\Models\User;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\Course\CouponTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;
use App\Http\Requests\Dashboard\Coupon\CouponRequest;
use App\Models\LessonTopic;

class CouponService
{
    use Conditionable;
    public function __construct(private Coupon $model)
    {
    }

    public function findCoupon($couponId)
    {
        return $this->model->find($couponId);
    }

    public function isCouponAvailable(Coupon $coupon, User $user, Model $action): array
    {
        #Todo Set This Errors Message And Success In The Language File Arabic And English 
        $coupon->loadCount(['usages']);
        if ($coupon->is_disabled) {
            return $this->responseContent(__('coupon_errors.disabled'), false);
        }
        if ($coupon->onlyAppliedTo()->isNot($action)) {
            return $this->responseContent(__('coupon_errors.not_can_use'), false);
        }
        if (filled($coupon->expiry_date) && Carbon::parse($coupon->expiry_date)->isPast()) {
            return $this->responseContent(__('coupon_errors.expired'), false);
        }
        if ($coupon->usages_count >= $coupon->maximum_usage) {
            return $this->responseContent(__('coupon_errors.limited'), false);
        }
        $couponUsage = $coupon->usages()->whereMorphedTo('usedByUser', $user);
        if ($couponUsage->whereMorphedTo('appliedTo', $action)->exists()) {
            return $this->responseContent(__('coupon_errors.already_used'), false);
        }
        if ($couponUsage->sum('amount') >= $coupon->price) {
            return $this->responseContent(__('coupon_errors.insufficient'), false);
        }

        return $this->responseContent(__('coupon_is_available'), true);
    }

    public function useStudentCoupon(User $user, string $couponId, Model $action)
    {
        $coupon = $this->findCoupon($couponId);
        $couponCheck = $this->isCouponAvailable($coupon, $user, $action);
        if (! $couponCheck) {
            return $this->responseContent($couponCheck ?? __('coupon_errors.is_not_available'), false);
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
            'code' => $request->code,
            'expiry_date' => $expiryDate->toDateTimeString(),
            'price' => $request->price,
            'type' => CouponTypeEnum::PURCHASE,
            'maximum_usage' => $maxUsegeLimit,
        ];
        $lessonTopic = LessonTopic::find($request->topic_id);
        if ($coupon->onlyAppliedTo()->isNot($lessonTopic)) {
            $couponData['only_applied_to_id'] = $lessonTopic->id;
            $couponData['only_applied_to_type'] = get_class($lessonTopic);
        }
        $coupon->update($couponData);
    }
    public function savePurchaseCoupons(CouponRequest $request)
    {
        $ids = [];
        $lessonTopic = LessonTopic::find($request->topic_id);
        DB::transaction(function () use ($request, $lessonTopic, &$ids) {
            $expiryDate = $this->when($request->expiry_date, fn() => Carbon::parse($request->expiry_date));

            for ($i = 0; $i < $request->coupons_count; $i++) {
                array_push(
                    $ids,
                    $this->storePurchaseCoupon(
                        $request->teacher_id,
                        $expiryDate,
                        $request->price,
                        $request->usage_limit,
                        $lessonTopic
                    )->id
                );
            }

        });

        return $ids;
    }
    private function storePurchaseCoupon($teacherId, ?Carbon $expiryDate, $price, $maxUsegeLimit, $appliedTo = null)
    {
        $couponData = [
            'teacher_id' => $teacherId,
            'code' => $this->generateCouponCode(),
            'expiry_date' => $expiryDate->toDateString(),
            'price' => $price,
            'type' => CouponTypeEnum::PURCHASE,
            'maximum_usage' => $maxUsegeLimit,
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