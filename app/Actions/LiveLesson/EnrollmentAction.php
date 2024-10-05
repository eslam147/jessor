<?php
namespace App\Actions\LiveLesson;

use App\Models\LiveLesson;
use App\Models\MeetingParticipant;
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
    public function usingCoupon($lesson)
    {
        // // ----------------------------------------------- #
        // DB::beginTransaction();
        $user = Auth::user();
        // $this->purchaseService->enrollLesson($lesson, $user->id);
        // // ----------------------------------------------- #
        // DB::commit();
        Alert::success('Success', 'Lesson has been unlocked successfully.');
    }
    public function usingWallet(LiveLesson $lesson)
    {
        $user = Auth::user();
        // // ----------------------------------------------- #
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
                if ($lesson->meeting) {
                    $participant = new MeetingParticipant();
                    $participant->meeting()->associate($lesson->meeting);

                    $participant->participant()->associate($user);
                    $participant->purchaseable()->associate($user->wallet()->first());

                    $participant->save();
                }
                // ----------------------------------------------- #
                // $this->purchaseService->enrollLesson($lesson, $user->id);
            });
            // ----------------------------------------------- #
            Alert::success('Success', 'Lesson has been unlocked successfully.');
        }
    }
    public function free($liveLesson)
    {
        // $user = Auth::user();
        // if (empty($liveLesson->price) || $liveLesson->is_free == 1) {
        //     $this->purchaseService->enrollLesson($liveLesson, $user->id);
        //     Alert::success('Success', 'Lesson has been unlocked successfully.');
        // }
    }
    // public function 
}