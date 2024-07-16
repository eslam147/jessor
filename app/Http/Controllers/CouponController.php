<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Http\Requests\Dashboard\Coupon\CouponRequest;
use App\Models\LessonTopic;
use App\Models\Teacher;
use App\Services\Coupon\CouponService;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {
        $this->middleware('permission:coupons-list', ['only' => ['index']]);
        $this->middleware('permission:coupons-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:coupons-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:coupons-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('coupons.index');
    }
    public function list()
    {
        $coupons = Coupon::query();

        $offset = request('offset', 0);
        $sort = request('sort', 'id');
        $limit = request('limit', 10);
        $order = request('order', 'DESC');

        if ($limit <= 0) {
            $limit = 10;
        } elseif ($limit > 100) {
            $limit = 100;
        }

        $coupons->when(request()->has('search'), function ($q) {
            $search = request('search');
            return $q->where('id', 'LIKE', "%{$search}%")
                ->orwhere('code', 'LIKE', "%{$search}%");
        });

        $total = $coupons->count();

        $coupons->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $coupons->get();

        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['code'] = $row->code;
            $tempRow['used_count'] = $row->usages()->count();
            $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
            $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
            $tempRow['operate'] = view('coupons.datatable.actions')->render();
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function create()
    {
        $teachers = Teacher::select("id", "user_id")->with('user:id,first_name,last_name')->get();
        $teachers = $teachers->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => "{$teacher->user->first_name} {$teacher->user->last_name}"
            ];
        })->pluck('name', 'id');

        $topics = LessonTopic::pluck('name', 'id');

        return view('coupons.create', compact('teachers', 'topics'));
    }

    public function store(CouponRequest $request)
    {
        $saveCoupons = $this->couponService->savePurchaseCoupons($request);

        return response()->json([
            'error' => false,
            'message' => trans('data_store_successfully')
        ]);
    }

    public function show(Coupon $coupon)
    {
        //
    }


    public function edit(Coupon $coupon)
    {
        $coupon->load('onlyAppliedTo');
        $teachers = Teacher::select("id", "user_id")->with('user:id,first_name,last_name')->get();
        $teachers = $teachers->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => "{$teacher->user->first_name} {$teacher->user->last_name}"
            ];
        })->pluck('name', 'id');

        $topics = LessonTopic::select('id', 'name')->get();

        return view('coupons.edit', compact('coupon', 'teachers', 'topics'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon = $this->couponService->updateCoupon($request, $coupon);

        return response()->json([
            'error' => false,
            'message' => trans('data_update_successfully'),
        ]);

    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json([
            'error' => false,
            'message' => trans('data_delete_successfully')
        ]);
    }
}
