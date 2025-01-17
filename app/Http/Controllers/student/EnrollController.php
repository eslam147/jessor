<?php

namespace App\Http\Controllers\student;


use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Coupon\CouponService;
use App\Services\Purchase\PurchaseService;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

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
            if (! $this->purchaseService->isLessonAlreadyEnrolled($lesson, $user->id)) {
                if ($payment_method == 'coupon_code') {
                    $validator = Validator::make($request->all(), [
                        'purchase_code' => 'required|string',
                        'lesson_id' => 'required|exists:lessons,id'
                    ]);
                    $msg = "";
                    if ($validator->fails()) {
                        Alert::warning('warning', $validator->messages()->all()[0]);
                        return redirect()->back();
                    } else {
                        // ----------------------------------------------- #
                        $purchaseCode = $request->input('purchase_code');
                        // ----------------------------------------------- #
                        DB::beginTransaction();

                        $applyCouponCode = $this->couponService->redeemCoupon($user, $purchaseCode, $lesson);
                        if ($applyCouponCode['status']) {
                            // ----------------------------------------------- #
                            $this->purchaseService->enrollLesson($lesson, $user->id);
                            // ----------------------------------------------- #
                            DB::commit();
                            Alert::success('Success', 'Lesson has been unlocked successfully.');
                            return redirect()->back();
                        }
                        Alert::error('error', $applyCouponCode['message']);
                        return redirect()->back();
                    }
                } elseif ($payment_method == 'wallet') {
                    $validator = Validator::make($request->all(), [
                        'lesson_id' => 'required|exists:lessons,id'
                    ]);
                    $msg = "";
                    if ($validator->fails()) {
                        Alert::warning('warning', $validator->messages()->all()[0]);
                        return redirect()->back();
                    } else {
                        // ----------------------------------------------- #
                        DB::beginTransaction();
                        // ----------------------------------------------- #
                        if (! empty($lesson->price)) {
                            if ($user->balance < $lesson->price) {
                                Alert::html('error', view('student_dashboard.wallet.balance_is_not_e', compact('user'))->render(), icon: 'error');
                                return redirect()->back();
                            }
                            $user->withdraw($lesson->price, [
                                'description' => "Enroll Lesson {$lesson->name}",
                            ]);
                            // ----------------------------------------------- #
                            $this->purchaseService->enrollLesson($lesson, $user->id);
                            // ----------------------------------------------- #
                            DB::commit();
                            Alert::success('Success', 'Lesson has been unlocked successfully.');
                            return redirect()->back();
                        }
                        // ----------------------------------------------- #
                    }

                } elseif ($payment_method == 'free') {
                    if (empty($lesson->price) || $lesson->is_free == 1) {
                        $this->purchaseService->enrollLesson($lesson, $user->id);
                        Alert::success('Success', 'Lesson has been unlocked successfully.');
                        return redirect()->back();
                    }
                }
            } else {
                Alert::warning('warning', 'You have already enrolled this lesson.');
                return back();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Alert::error('error', "Error When Unlocking Lesson.");
            return redirect()->back();
        }
    }
}
