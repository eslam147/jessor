<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Services\Coupon\CouponService;
use App\Http\Requests\Dashboard\Coupon\CouponRequest;
use App\Models\ClassSubject;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {
        $this->middleware('permission:coupons-list', ['only' => ['index']]);
        $this->middleware('permission:coupons-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:coupons-edit', ['only' => ['edit', 'update', 'changeStatus']]);
        $this->middleware('permission:coupons-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('coupons.index');
    }
    public function list()
    {
        $coupons = Coupon::query();
        $mappedOrderKeys = [
            'id' => 'id',
            'code' => 'code',
            'name' => 'name',

        ];
        $offset = request('offset', 0);
        $sort = request('sort', 'id');
        $limit = request('limit', 10);
        $order = request('order', 'DESC');
        $purchased = request('purchased');

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
        $coupons->when($purchased, fn($q) => $q->has('usages'));

        $total = $coupons->count();

        $findOrderKey = in_array($sort, array_keys($mappedOrderKeys));

        $coupons->when($purchased, fn($q) => $q->has('usages'));
        $coupons
            ->withCount('usages')
            ->with('classSection.class', 'classSection.section', 'classSection.class.medium', 'classSection.class.streams', 'subject:id,name')
            ->when($findOrderKey, fn($q) => $q->orderBy($mappedOrderKeys[$sort], $order))
            ->skip($offset)
            ->take($limit);


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
            $tempRow['used_count'] = $row->usages_count;
            $tempRow['class_name'] = optional($row->classSection)->class?->name . "-" . optional($row->classSection)->section?->name . " - " . optional($row->classSection)->class?->medium?->name . " " . optional($row->classSection)->class?->streams?->name;
            $tempRow['subject_name'] = optional($row->subject)->name;
            $tempRow['expiry_date'] = $row->expiry_date->toDateString();
            $tempRow['price'] = number_format($row->price, 2);
            $tempRow['maximum_usage'] = $row->maximum_usage;
            $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
            $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
            $tempRow['status'] = view('coupons.datatable.status', ['row' => $row])->render();
            $tempRow['operate'] = view('coupons.datatable.actions', ['row' => $row])->render();
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function create()
    {
        $subjects = ClassSubject::with('subject')->orderByDesc('id')->get();

        $classes = ClassSection::with('class', 'section', 'streams')->get();
        $teachers = Teacher::with('user', 'subjects')->get();

        $lessons = Lesson::select('name', 'teacher_id', 'class_section_id', 'id')->get();

        return view('coupons.create', compact('teachers', 'lessons', 'subjects', 'classes'));
    }

    public function store(CouponRequest $request)
    {
        $couponIds = $this->couponService->savePurchaseCoupons($request);

        if ($request->post('action') == 'save_and_print') {
            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully'),
                'data' => [
                    'file_url' => $this->couponService->exportCouponCode($couponIds),
                    'file_name' => "couponcode".time().".xlsx"
                ]
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => trans('data_store_successfully')
        ]);
    }

    public function show(Coupon $coupon)
    {
        $coupon->load('onlyAppliedTo', 'usages', 'teacher.user');
        return response()->json([
            'error' => false,
            'data' => [
                'code' => $coupon->code,
                'price' => $coupon->price,
                'maximum_usage' => $coupon->maximum_usage,
                'expiry_date' => $coupon->expiry_date->toDateString(),
                'only_applied_to' => $coupon->only_applied_to_type instanceof Lesson ? $coupon->onlyAppliedTo->name : '',
                'is_disabled' => $coupon->is_disabled,
                'used_count' => $coupon->usages()->count(),
                'teacher' => $coupon->teacher->user->name,
                'created_at' => convertDateFormat($coupon->created_at, 'd-m-Y H:i:s'),
                'type' => $coupon->type->translatedName()
            ],
            'message' => trans('coupon_fetch_successfully'),
        ]);
    }

    public function changeStatus(Request $request, Coupon $coupon)
    {
        $status = $request->json()->all()['status'] ?? 1;
        $coupon->update([
            'is_disabled' => $status
        ]);

        return response()->json([
            'error' => false,
            'message' => trans('data_update_successfully'),
        ]);
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

        $lessons = Lesson::select('id', 'teacher_id', 'name')->get();

        return view('coupons.edit', compact('coupon', 'teachers', 'lessons'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $this->couponService->updateCoupon($request, $coupon);

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
