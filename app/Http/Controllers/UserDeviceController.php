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
        $order = request('order', 'ASC');

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
            $bulkData['rows'][] = [
                'id' => $row->id,
                'no' => $no++,
                'user_id' => $row->user_id,
                'first_name' => $row->user->first_name,
                'last_name' => $row->user->last_name,
                'email' => $row->user->email,
                'gender' => $row->user->gender,
                'mobile' => $row->user->mobile,
                // ----------------------------------------------- \\
                'device_name' => $row->device_name,
                'device_ip' => $row->device_ip,
                'device_agent' => $row->device_agent,
                // ----------------------------------------------- \\
                'operate' => view('user_devices.datatables.actions', compact('row'))->render(),
            ];
        }
        // ----------------------------------------------- \\


        return response()->json($bulkData);
    }
    public function delete(Request $request, UserDevice $userDevice)
    {
        if (! Auth::user()->can('user-devices-delete')) {
            return response()->json([
                'message' => trans('no_permission_message')
            ]);
        }
        $userDevice->delete();
        return response()->json([
            'error' => true,
            'message' => trans('error_occurred')
        ]);
    }

}
