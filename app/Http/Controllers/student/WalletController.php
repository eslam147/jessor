<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Coupon\CouponService;
use RealRashid\SweetAlert\Facades\Alert;

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

        $coupon = $this->couponService->applyCouponToWallet($user, $request->coupon_code);

        if ($coupon['status']) {
            Alert::success("Coupon Success", __('coupon_success'));
            return to_route('student_dashboard.wallet.index');
        }

        Alert::error("Coupon Error", $coupon['message']);
        return redirect()->back();
    }
}
