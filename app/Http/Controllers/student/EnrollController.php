<?php

namespace App\Http\Controllers\student;


use App\Models\Coupon;
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
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'purchase_code' => 'required|string',
            'lesson_id' => 'required|exists:lessons,id'
        ]);

        if($validator->fails()){
            Alert::warning('warning', $validator->messages()->all()[0]);
            return redirect()->back();
        }else{
                    // Debug the input
        $purchaseCode = $request->input('purchase_code');

        // Debug query to check coupon retrieval
        $coupon = Coupon::where('code', $purchaseCode)->first();

        if (!$coupon) {
            Alert::warning('Invalid Purchase Code', 'This Purchase is invalid.');
            return redirect()->back();
        }

        if ($coupon->is_disabled == 1) {
            Alert::warning('disabled', 'This purchase code is disabled.');
            return redirect()->back();
        }

        if ($coupon->expiry_date < now()) {
            Alert::warning('expired', 'This purchase code has expired.');
            return redirect()->back();
        }

        $usageCount = CouponUsage::where('coupon_id', $coupon->id)->count();
        if ($usageCount >= $coupon->maximum_usage) {
            Alert::warning('Used Code', 'This purchase code has reached its maximum usage limit.');
            return redirect()->back();
        }

        $enrollment = new Enrollment();
        $enrollment->user_id = Auth::id();
        $enrollment->lesson_id = $request->lesson_id;
        $enrollment->save();

        $couponUsage = new CouponUsage();
        $couponUsage->coupon_id = $coupon->id;
        $couponUsage->used_by_user_id = Auth::id();
        $couponUsage->amount = $coupon->price;
        $couponUsage->save();

        Alert::success('Success', 'Lesson has been unlocked successfully.');
        return redirect()->back();
        
        }
    }
}
