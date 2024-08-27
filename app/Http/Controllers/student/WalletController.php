<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Coupon\CouponService;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiter as RateLimiterCache;

class WalletController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService
    ) {
    }
    public function index()
    {
        $transactions = auth()->user()->walletTransactions()->orderByDesc('created_at')->paginate(10);
        return view('student_dashboard.wallet.index', compact('transactions'));
    }
    public function walletHistory()
    {
        return view('student_dashboard.wallet.wallet_history');
    }
    public function applyCouponToWallet(Request $request)
    {
        $user = auth()->user();
        $rateLimitKey = "apply-coupon-to-wallet: {$user->id}";

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            // $user->ban([
            //     'comment' => 'Too many attempts to apply coupon to wallet!',
            //     'expired_at' => '+1 month',
            // ]);
            $leftSeconds = RateLimiter::availableIn($rateLimitKey);

            Alert::error(__('unauthorized_access'), __('please_try_again_after', ['secs' => $leftSeconds]));
            return redirect()->route('student_dashboard.wallet.index');
        }
        $leftSeconds = RateLimiter::availableIn($rateLimitKey);
        // dd(
        //     $leftSeconds
        // );
        RateLimiter::hit($rateLimitKey);

        $coupon = $this->couponService->applyCouponToWallet($user, $request->coupon_code);

        if ($coupon['status']) {
            Alert::success("Coupon Success", __('coupon_successfully_applied'));
            return to_route('student_dashboard.wallet.index');
        }
        // toast($coupon['message'], 'error');
        Alert::error("Coupon Error", $coupon['message']);
        return redirect()->back();

    }
}
