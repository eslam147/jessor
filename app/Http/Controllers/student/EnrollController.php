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
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class EnrollController extends Controller
{
    public CouponService $couponService;
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function store(Request $request, string $payment_method)
    {
        try {
            $user = Auth::user();

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
                    $lesson = Lesson::find($request->lesson_id);
                    // ----------------------------------------------- #
                    DB::beginTransaction();

                    $applyCouponCode = $this->couponService->redeemCoupon($user, $purchaseCode, $lesson);
                    if ($applyCouponCode['status'] == true) {
                        $enrollment = Enrollment::create([
                            'user_id' => $user->id,
                            'lesson_id' => $lesson->id
                        ]);
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
                    $lesson = Lesson::find($request->lesson_id);
                    $user = Auth::user();
                    // ----------------------------------------------- #
                    DB::beginTransaction();
                    // $user 
                    if (! empty($lesson->price)) {
                        if ($user->balance < $lesson->price) {
                            Alert::warning('error', "You don't have enough balance.");
                            return redirect()->back();
                        }
                        $user->withdraw($lesson->price, [
                            'description' => "Enroll Lesson {$lesson->name}",
                        ]);

                        $enrollment = Enrollment::create([
                            'user_id' => $user->id,
                            'lesson_id' => $lesson->id,
                            // 'payed_using' => 'wallet'
                        ]);
                        DB::commit();

                        Alert::success('Success', 'Lesson has been unlocked successfully.');
                        return redirect()->back();
                    }
                    // ----------------------------------------------- #
                }

            }

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Alert::error('error', "Error When Unlocking Lesson.");
            return redirect()->back();
        }
    }
}
