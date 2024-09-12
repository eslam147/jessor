<?php

namespace App\Http\Controllers;

use Spatie\Tags\Tag;
use App\Models\Coupon;
use App\Models\Lesson;
use App\Models\Mediums;
use App\Models\Teacher;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use App\Exports\CouponExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Coupon\CouponService;
use App\Http\Requests\Dashboard\Coupon\CouponRequest;

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
        $classes = ClassSchool::get();
        $tags = Tag::get();
        $classSubjects = ClassSubject::with('subject')->get();
        $mediums = Mediums::with('class')->has('class')->get();
        $teachers = Teacher::has('lessons')->with('lessons', 'subjects')->get();
        $lessons = Lesson::get();
        return view('coupons.index', compact('classes', 'teachers', 'lessons', 'classSubjects', 'mediums', 'tags'));
    }
    public function export(Request $request)
    {
        $couponIds = $this->filter(Coupon::query())->pluck('id')->toArray();
        return Excel::download(new CouponExport($couponIds), "coupons_list.xlsx");
    }
    public function list()
    {
        $coupons = Coupon::query()->with('tags');
        $mappedOrderKeys = [
            'id' => 'id',
            'code' => 'code',
            'name' => 'name',

        ];
        $offset = request('offset', 0);
        $sort = request('sort', 'id');
        $limit = request('limit', 10);
        $order = request('order', 'DESC');
        $purchased = request()->filled('purchased');

        if ($limit <= 0) {
            $limit = 10;
        } elseif ($limit > 100) {
            $limit = 100;
        }
        $coupons->when(request()->filled('tags'), function ($q) {
            $tags = explode(',', request('tags'));
            return $q->whereHas('tags', function ($query) use ($tags) {
                array_map('trim', $tags);
                $query->where(function ($q) use ($tags) {
                    foreach ($tags as $tag) {
                        $q->orWhere("name->en","LIKE", "%{$tag}%");
                    }
                });
            });
        });
        $coupons = $this->filter($coupons);
        $total = $coupons->count();

        $findOrderKey = in_array($sort, array_keys($mappedOrderKeys));

        $coupons
            ->withCount('usages')
            ->with('classModel', 'classModel.medium', 'classModel.streams', 'subject:id,name')
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
            $tempRow['tags_imploded'] = $row->tags->pluck('name')->implode(', ');
            $tempRow['type'] = $row->type->translatedName();
            $tempRow['class_name'] = optional($row->classModel)?->name ?? 'N/A';
            $tempRow['subject_name'] = optional($row->subject)?->name ?? 'N/A';
            $tempRow['expiry_date'] = $row->expiry_date->toDateString();
            $tempRow['price'] = !is_null($row->price) ? number_format($row->price, 2) : 'N/A';
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
        $mediums = Mediums::withWhereHas('class', fn($q) => $q->has('allSubjects'))
            ->orderByDesc('id')->has('class')->get();

        $mediums = $mediums->map(function ($medium) {
            return (object) [
                'id' => $medium->id,
                'name' => $medium->name,
                'classes' => $medium->class->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => "{$class->name}" . optional($class->section, function ($section) {
                            return " - {$section->name}";
                        }) . " -{$class->streams?->name}"
                    ];
                })
            ];

        });

        $teachers = Teacher::with('user', 'subjects')->get();
        $lessons = Lesson::select('name', 'teacher_id', 'subject_id', 'class_section_id', 'id')->addSelect([
            'class_id' => ClassSection::select('class_id')->whereColumn('id', 'lessons.class_section_id'),
        ])->get();
        return view('coupons.create', compact('teachers', 'lessons', 'mediums', 'subjects'));
    }

    public function store(CouponRequest $request)
    {
        if($request->post('coupon_type') == 'wallet'){
            $couponIds = $this->couponService->saveWalletCoupons($request);
        }else{
            $couponIds = $this->couponService->savePurchaseCoupons($request);
        }

        if ($request->post('action') == 'save_and_print') {
            $exportCoupons = $this->couponService->exportCouponCode($couponIds);
            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully'),
                'data' => [
                    'file_url' => $exportCoupons['url'],
                    'file_name' => $exportCoupons['name']
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
                'teacher' => optional($coupon->teacher)->user->name ?? 'N/A',
                'created_at' => convertDateFormat($coupon->created_at, 'd-m-Y H:i:s'),
                'type' => $coupon->type->translatedName()
            ],
            'message' => trans('coupon_fetch_successfully'),
        ]);
    }

    public function changeStatus(Request $request, Coupon $coupon)
    {
        $status = $request->json()->getBoolean('status');

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
        $subjects = ClassSubject::with('subject')->orderByDesc('id')->get();
        $mediums = Mediums::withWhereHas('class')->orderByDesc('id')->has('class')->get();

        $mediums = $mediums->map(function ($medium) {
            return (object) [
                'id' => $medium->id,
                'name' => $medium->name,
                'classes' => $medium->class->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => "{$class->name}" . optional($class->section, function ($section) {
                            return " - {$section->name}";
                        }) . " -{$class->streams?->name}"
                    ];
                })
            ];

        });


        $teachers = Teacher::with('user', 'subjects')->get();

        $lessons = Lesson::select('name', 'teacher_id', 'class_section_id', 'id')->get();


        return view('coupons.edit', compact('coupon', 'teachers', 'subjects', 'mediums', 'lessons'));
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
    
    private function filter($couponQuery){
        $couponQuery->when(request()->filled('search'), function ($q) {
            $search = request('search');
            return $q->where('id', 'LIKE', "%{$search}%")->orWhere('code', 'LIKE', "%{$search}%");
        });
        $couponQuery->when(request()->filled('filter_by_medium'), function ($q) {
            return $q->whereHas('classModel', function ($q) {
                $q->where('medium_id', request('filter_by_medium'));
            });
        });

        $couponQuery->when(request('class_id'), function ($q, $val) {
            return $q->where('class_id', $val);
        });

        $couponQuery->when(request('teacher_id'), function ($q, $val) {
            return $q->where('teacher_id', $val);
        });

        $couponQuery->when(request('subject_id'), function ($q, $val) {
            return $q->where('subject_id', $val);
        });

        $couponQuery->when(request('lesson_id'), function ($q, $val) {
            return $q->where('lesson_id', $val);
        });

        $couponQuery->when(request()->filled('filter_start_date'), function ($q) {
            return $q->whereDate('created_at', '>=', request('filter_start_date'));
        });

        $couponQuery->when(request()->filled('filter_end_date'), function ($q) {
            return $q->whereDate('created_at', '<=', request('filter_end_date'));
        });

        $couponQuery->when(request()->filled('filter_status'), function ($q) {
            $selectedValue = request()->boolean('filter_status');
            if ($selectedValue) {
                return $q->where('is_disabled', 1);
            }
            return $q->where('is_disabled', 0);
        });


        $couponQuery->when(request()->filled('purchased'), fn($q) => $q->has('usages'));
        return $couponQuery;
    }
}
