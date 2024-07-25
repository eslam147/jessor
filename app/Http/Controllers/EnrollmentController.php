<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        return view('enrollment.index');
    }
    public function list()
    {
        $coupons = Enrollment::query();
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


        $coupons->when(request()->has('search'), function ($q) {
            $search = request('search');
            // return $q->where('id', 'LIKE', "%{$search}%")
            //     ->orwhere('code', 'LIKE', "%{$search}%");
        });

        $total = $coupons->count();

        $findOrderKey = in_array($sort, array_keys($mappedOrderKeys));

        $coupons
            ->when($findOrderKey, fn($q) => $q->orderBy($mappedOrderKeys[$sort], $order))
            ->skip($offset)
            ->with('lesson', 'user.student')
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
            $user = $row->user;
            $lesson = $row->lesson;
            $tempRow['student'] = view('enrollment.datatable.student', compact('user'))->render();
            $tempRow['lesson'] = view('enrollment.datatable.lesson', compact('lesson'))->render();

            $tempRow['purchase_date'] = $row->created_at?->toDateString();

            $tempRow['operate'] = view('enrollment.datatable.actions', ['row' => $row])->render();

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
    public function destroy(Enrollment $enrollment)
    {
        if (! Auth::user()->can('enrollments-delete')) {
            $response = [
                'error' => true,
                'message' => trans('no_permission_message')
            ];

            return response()->json($response);

        }
        try {
            $enrollment->delete();
            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }

        return response()->json($response);
    }
}