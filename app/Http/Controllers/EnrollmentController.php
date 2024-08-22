<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('teacher.user')->get();
        return view('enrollment.index', compact('lessons'));
    }
    public function list()
    {
        $enrollmentQuery = Enrollment::query();
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
    public function update(Request $request, Enrollment $enrollment)
    {
        if (! Auth::user()->can('role-edit')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $this->validate($request, [
            'expiration_at' => ['required','date_format:Y-m-d\TH:i','after:now'],
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
        $response = [
            'error' => false,
            'message' => trans('data_delete_successfully')
        ];

        return response()->json($response);
    }
}