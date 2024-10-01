<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Coupon\CouponService;
use App\Actions\Lesson\EnrollmentAction;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Services\Purchase\PurchaseService;

class EnrollController extends Controller
{
    public CouponService $couponService;
    public PurchaseService $purchaseService;
    public function __construct(CouponService $couponService, PurchaseService $purchaseService)
    {
        $this->couponService = $couponService;
        $this->purchaseService = $purchaseService;
    }

    public function store(Request $request, string $payment_method)
    {
        $user = Auth::user();
        try {
            $lesson = Lesson::find($request->lesson_id);
            if ($this->purchaseService->isLessonAlreadyEnrolled($lesson, $user->id)) {
                Alert::warning('warning', 'You have already enrolled this lesson.');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'lesson_id' => 'required|exists:lessons,id',
                'purchase_code' => ['string', Rule::requiredIf($payment_method == 'coupon_code')],
            ]);
            $purchaseCode = $request->input('purchase_code');

            if ($validator->fails()) {
                Alert::warning('warning', $validator->messages()->all()[0]);
                return back();
            } else if ($payment_method == 'coupon_code') {
                $applyCouponCode = $this->couponService->redeemCoupon($user, $purchaseCode, $lesson);
                if (! $applyCouponCode['status']) {
                    Alert::error('error', $applyCouponCode['message']);
                    return back();
                }
            }

            $enrollLesson = match ($payment_method) {
                'coupon_code' => app(EnrollmentAction::class)->usingCoupon($lesson,$purchaseCode),
                'wallet' => app(EnrollmentAction::class)->usingWallet($lesson),
                'free' => app(EnrollmentAction::class)->free($lesson),
            };

            return back();
        } catch (\Exception $e) {
            report($e);
            Alert::error('error', "Error When Unlocking Lesson.");
            return back();
        }
    }
}
