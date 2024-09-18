<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\Response\HttpResponseCode;
use App\Models\Students;
use App\Services\Purchase\PurchaseService;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    public function __construct(
        public PurchaseService $enrollmentService
    ) {

    }
    public function index()
    {
        $classSections = ClassSection::with(
            'class.medium',
            'streams',
            'section'
        )->withOutTrashedRelations('section', 'class')->get();
        $classSectionsMapped = [];

        foreach ($classSections as $classSection) {
            $name = "{$classSection->class->name} - {$classSection->section->name} " .
                $classSection->class?->medium?->name . '' .
                optional($classSection->streams)->name ?? '';
            $classSectionsMapped[] = [
                'id' => $classSection->id,
                'name' => trim($name),
            ];
        }
        $lessons = Lesson::relatedToTeacher()->with('teacher.user')->get();
        return view('enrollment.index', compact('lessons', 'classSectionsMapped'));
    }
    public function list()
    {
        $enrollmentQuery = Enrollment::query()->teacherFilter();
        $mappedOrderKeys = [
            'id' => 'id',
        ];
        $offset = request('offset', 0);
        $sort = request('sort', 'id');
        $limit = request('limit', 10);
        $order = request('order', 'DESC');

        if ($limit <= 0) {
            $limit = 10;
        } elseif ($limit > 100) {
            $limit = 100;
        }


        $enrollmentQuery->when(request()->filled('search'), function ($q) {
            $search = request('search');
            return $q->whereHas('user', function ($q) use ($search) {
                return $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            })->orWhereHas('lesson', function ($q) use ($search) {
                return $q->where('lesson_id', 'LIKE', "%{$search}%")->orWhere('lesson_id', 'LIKE', "%{$search}%");
            });
        });
        $enrollmentQuery->when(request()->filled('teacher_id'), function ($q) {
            $q->whereHas('lesson', function ($q) {
                $q->where('teacher_id', request('teacher_id'));
            });
        });

        $enrollmentQuery->when(request()->filled('lesson_id'), function ($q) {
            $q->where('lesson_id', request('lesson_id'));
        });

        // $enrollmentQuery->when(request()->filled('teacher_id'),function ($q){});
        $total = $enrollmentQuery->count();

        $findOrderKey = in_array($sort, array_keys($mappedOrderKeys));

        $enrollmentQuery
            ->when($findOrderKey, fn($q) => $q->orderBy($mappedOrderKeys[$sort], $order))
            ->skip($offset)
            ->with('lesson.teacher.user', 'user.student')
            ->take($limit);

        $res = $enrollmentQuery->get();

        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $student = $row->user;
            $lesson = $row->lesson;
            $teacher = $row->lesson->teacher->user;
            $tempRow['student'] = view('enrollment.datatable.user', [
                'user' => $student
            ])->render();
            $tempRow['teacher'] = view('enrollment.datatable.user', [
                'user' => $teacher
            ])->render();
            $tempRow['lesson'] = view('enrollment.datatable.lesson', compact('lesson'))->render();

            $tempRow['purchase_date'] = $row->created_at?->toDateString();
            $tempRow['expiration_at'] = $row->expires_at?->format("Y-m-d h:i A");
            $tempRow['expiration_local_format'] = $row->expires_at?->format("Y-m-d\TH:i");

            $tempRow['operate'] = view('enrollment.datatable.actions', ['row' => $row])->render();

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('enrollments-create')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ], HttpResponseCode::UNAUTHORIZED);
        }

        $rules = [
            'expiration_at' => ['nullable', 'date_format:Y-m-d\TH:i', 'after:now'],
            'lesson_id' => ['required', Rule::exists('lessons', 'id')->where('teacher_id', Auth::user()->teacher->id)],
            'enroll_based_on' => ['required', 'in:0,1'],
            'student_id' => ['nullable', 'required_if:enroll_based_on,0', Rule::exists('users', 'id')],
            'class_section_id' => ['required_if:enroll_based_on,1', Rule::exists('class_sections', 'id')],
        ];

        $this->validate($request, $rules);

        $lesson = Lesson::findOrFail($request->input('lesson_id'));
        $expiryDate = Carbon::parse($request->input('expiration_at'));

        if ($request->input('enroll_based_on') == '0') {
            // Enroll a single student
            $enrollLesson = $this->enrollmentService->enrollLesson($lesson, $request->input('student_id'), $expiryDate);
            if (!$enrollLesson) {
                return response()->json([
                    'error' => true,
                    'message' => trans('lesson_already_enrolled')
                ]);
            }
        } else {

            // Enroll all students in the class section
            $students = Students::where('class_section_id', $request->input('class_section_id'))->with('user')
                ->whereDoesntHave('user.enrollmentLessons', function ($q) use ($lesson) {
                    $q->where('lesson_id', $lesson->id)->where('expires_at', '>', now());
                })->get();
            $newEnrollmets = [];
            foreach ($students as $student) {
                if (! empty($student->user->id)) {
                    $newEnrollmets[] = [
                        'user_id' => $student->user->id,
                        'lesson_id' => $lesson->id,
                        'expires_at' => $expiryDate->toDateTimeString(),
                    ];
                }
            }

            $isTransactionSuccess = DB::transaction(function () use ($newEnrollmets) {
                foreach (array_chunk($newEnrollmets, 100) as $chunk) {
                    Enrollment::insert($chunk);
                }
                return true;
            });
            if (! $isTransactionSuccess) {
                return response()->json([
                    'error' => true,
                    'message' => trans('error_occurred'),
                ]);
            }
        }

        return response()->json([
            'error' => false,
            'message' => trans('successfully_lesson_enrolled'),
        ]);

    }
    public function update(Request $request, Enrollment $enrollment)
    {
        if (! Auth::user()->can('enrollments-edit')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $this->validate($request, [
            'expiration_at' => ['required', 'date_format:Y-m-d\TH:i', 'after:now'],
        ]);

        try {
            $enrollment->update([
                'expires_at' => $request->input('expiration_at'),
            ]);
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully'),
            ];
        } catch (\Throwable $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }
    public function destroy(Enrollment $enrollment)
    {
        if (! Auth::user()->can('enrollments-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $enrollment->delete();

        return response()->json([
            'error' => false,
            'message' => trans('data_delete_successfully')
        ]);
    }
}