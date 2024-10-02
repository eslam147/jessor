<?php

namespace App\Http\Controllers;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDeviceController extends Controller
{
    public function index()
    {
        if (! auth()->user()->can('user-devices-list')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }

        return view('user_devices.index');
    }

    public function list()
    {
        if (! auth()->user()->can('user-devices-list')) {
            return response()->json([
                'message' => trans('no_permission_message')
            ]);
        }

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = UserDevice::with('user');
        if (! empty(request('search'))) {
            $search = request('search');
            $sql->where('id', 'LIKE', "%{$search}%")
                ->orwhere('user_id', 'LIKE', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orwhere('last_name', 'LIKE', "%{$search}%")
                        ->orwhere('gender', 'LIKE', "%{$search}%")
                        ->orwhere('email', 'LIKE', "%{$search}%")
                        ->orwhere('dob', 'LIKE', "%" . date('Y-m-d', strtotime($search)) . "%");
                });
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit);
        $res = $sql->get();

        $bulkData = [];
        $bulkData['total'] = $total;

        $no = 1;
        // ----------------------------------------------- \\
        foreach ($res as $row) {
            $user = optional($row?->user);
            $bulkData['rows'][] = [
                'id' => $row->id,
                'no' => $no++,
                'user_id' => $row->user_id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ??' ',
                'email' => $user->email ?? '',
                'gender' => $user->gender ??' ',
                'mobile' => $user->mobile ?? '',
                // ----------------------------------------------- \\
                'device_name' => $row->device_name,
                'device_ip' => $row->ip,
                'os' => $row->os,
                'browser' => $row->browser,
                'city' => $row->city,
                // ----------------------------------------------- \\
                'session_start_at' => $row->session_start_at,
                'session_end_at' => $row->session_end_at,
                'device_agent' => $row->device_agent,
                // ----------------------------------------------- \\
                'student' => view('user_devices.datatables.student', compact('user'))->render(),
                'operate' => view('user_devices.datatables.actions', compact('row'))->render(),
            ];
        }
        // ----------------------------------------------- \\


        return response()->json($bulkData);
    }
    public function destroy(UserDevice $userDevice)
    {
        if (! Auth::user()->can('user-devices-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $userDevice->delete();
        return response()->json([
            'error' => false,
            'message' => trans('data_delete_successfully')
        ]);
    }

}
