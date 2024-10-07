<?php

namespace App\Http\Controllers\student;

use App\Models\User;
use App\Models\LiveLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\Lesson\LiveLessonStatus;
use App\Services\Coupon\CouponService;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Services\Purchase\PurchaseService;
use App\Actions\LiveLesson\EnrollmentAction;

class LiveLessonController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchaseService,
        private readonly EnrollmentAction $enrollmentAction,
        private readonly CouponService $couponService
    ) {
    }
    public function index()
    {
        $classSectionId = Auth::user()->student->class_section_id;
        $liveSessions = LiveLesson::where('class_section_id', $classSectionId)
            ->orderByDesc('id')
            ->where('status', '!=', LiveLessonStatus::FINISHED)
            ->with('subject', 'teacher.user')
            ->withCount([
                'participants' => fn($q) => $q
                    ->where('participant_type', User::class)
                    ->where('participant_id', Auth::user()->id),
            ])->get()
            ->groupBy(fn($item) => $item->session_start_at->format('Y-m-d'));
        // meeting->participants->where('users.id', auth()->user()->id)->first()

        return view('student_dashboard.live_lessons.index', compact('liveSessions'));
    }
    public function enroll(Request $request, LiveLesson $liveLesson)
    {
        $paymentMethod = $request->input('payment_method');
        $user = Auth::user();
        $liveLesson->load([
            'meeting'
        ]);

        try {
            if ($liveLesson->meeting->participants()->where('users.id', $user->id)->exists()) {
                Alert::warning('warning', 'You have already enrolled this lesson.');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'lesson_id' => 'required|exists:live_lessons,id',
                'payment_method' => 'required|string',
                'purchase_code' => 'required_if:payment_method,coupon_code|string',
            ]);

            if ($validator->fails()) {
                Alert::warning('warning', $validator->messages()->all()[0]);
                return redirect()->back();
            } else if ($paymentMethod == 'coupon_code') {
                $purchaseCode = $request->input('purchase_code');

                $applyCouponCode = $this->couponService->redeemCoupon($user, $purchaseCode, $liveLesson);
                if (! $applyCouponCode['status']) {
                    Alert::error('error', $applyCouponCode['message']);
                }
            }

            $enrollLesson = match ($paymentMethod) {
                'wallet' => app(EnrollmentAction::class)->usingWallet($liveLesson),
            // 'coupon_code' => app(EnrollmentAction::class)->usingCoupon($lesson),
            // 'free' => app(EnrollmentAction::class)->free($lesson),
            };

            return back();

        } catch (\Exception $e) {
            // throw $e;
            report($e);
            
            DB::rollBack();
            Alert::error('error', "Error When Unlocking Lesson.");
            return redirect()->back();
        }
    }
}
