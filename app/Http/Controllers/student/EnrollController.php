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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_code' => 'required|string',
            'lesson_id' => 'required|exists:lessons,id'
        ]);

        if ($validator->fails()) {
            Alert::warning('warning', $validator->messages()->all()[0]);
            return redirect()->back();
        } else {
            // ----------------------------------------------- #
            $purchaseCode = $request->input('purchase_code');
            $lesson = Lesson::find($request->lesson_id);
            $user = Auth::user();
            // ----------------------------------------------- #
            try {
                DB::beginTransaction();
                $applyCouponCode = $this->couponService->redeemCoupon($user, $purchaseCode, $lesson);
                if ($applyCouponCode['status'] == false) {
                    Alert::error('error', $applyCouponCode['message']);
                    return redirect()->back();
                }

                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id
                ]);
                // ----------------------------------------------- #
                DB::commit();

                Alert::success('Success', 'Lesson has been unlocked successfully.');
                return redirect()->back();
            } catch (\Exception $e) {
                DB::rollBack();

                if ($applyCouponCode['status'] == false) {
                    Alert::error('error', $applyCouponCode['message']);
                    return redirect()->back();
                }
            }

        }
    }


}
