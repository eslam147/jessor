<?php
namespace App\Actions\Lesson;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Coupon\CouponService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\Purchase\PurchaseService;

class EnrollmentAction
{

    public function __construct(
        private readonly PurchaseService $purchaseService,
        private readonly CouponService $couponService
    ) {
    }
    public function usingCoupon($lesson, $purchaseCode)
    {
        $user = Auth::user();
        DB::transaction(function () use ($user, $lesson, $purchaseCode) {
            $this->couponService->redeemCoupon($user, $purchaseCode, $lesson);
            $this->purchaseService->enrollLesson($lesson, $user->id);
        });

        Alert::success('Success', 'Lesson has been unlocked successfully.');
    }
    public function usingWallet($lesson)
    {
        $user = Auth::user();
        // ----------------------------------------------- #
        if (! empty($lesson->price)) {
            if ($user->balance < $lesson->price) {
                Alert::html('error', view('student_dashboard.wallet.balance_is_not_e', compact('user'))->render(), icon: 'error');
                return redirect()->back();
            }
            DB::transaction(function () use ($lesson, $user) {
                // ----------------------------------------------- #
                $user->withdraw($lesson->price, [
                    'description' => "Enroll Lesson {$lesson->name}",
                ]);
                // ----------------------------------------------- #
                $this->purchaseService->enrollLesson($lesson, $user->id);
            });
            // ----------------------------------------------- #
            Alert::success('Success', 'Lesson has been unlocked successfully.');
        }
    }
    public function free($lesson)
    {
        $user = Auth::user();
        if (empty($lesson->price) || $lesson->is_free == 1) {
            $this->purchaseService->enrollLesson($lesson, $user->id);
            Alert::success('Success', 'Lesson has been unlocked successfully.');
        }
    }
    // public function 
}