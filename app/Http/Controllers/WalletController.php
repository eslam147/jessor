<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Bavix\Wallet\Models\Wallet;

class WalletController extends Controller
{
    public function index()
    {
        $students = User::whereHas('student')->select('first_name', 'last_name', 'id')->get();
        return view('wallet.index', compact('students'));
    }
    public function list()
    {
        // if (! Auth::user()->can('subject-teacher-list')) {
        //     $response = array(
        //         'error' => true,
        //         'message' => trans('no_permission_message')
        //     );
        //     return response()->json($response);
        // }
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        if (filled(request('offset')))
            $offset = request('offset');
        if (filled(request('limit')))
            $limit = request('limit');
        if (filled(request('sort')))
            $sort = request('sort');
        if (filled(request('order')))
            $order = request('order');
        $search = request('search');

        $sql = User::with('wallet')->has('student');
        $sql->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('dob', 'LIKE', "%$search%");
            });
            //class filter data
        });
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);

        $res = $sql->get();

        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {
            $operate = '';
            $operate .= "<a class='btn btn-xs btn-danger btn-rounded wallet_withdraw' data-id='{$row->id}' title='Withdraw Balance'><i class='fa fa-minus'></i></a>&nbsp;&nbsp;";
            $operate .= "<a class='btn btn-xs btn-success btn-rounded wallet_deposit' data-id='{$row->id}' title='Add Balance'><i class='fa fa-plus'></i></a>&nbsp;&nbsp;";

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;

            $tempRow['balance'] = $row->balanceInt;
            $tempRow['name'] = $row->full_name;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
    public function updateBalance(Request $request, string $type)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:50000',
            'user_id' => 'required|exists:users,id',
        ]);
        $user = User::find($request->user_id);
        $msg = '';
        switch ($type) {
            case 'deposit':
                $user->deposit($request->amount);
                $msg = trans('wallet_balance_added_successfully');

                break;
            case 'withdraw':
                $msg = trans('wallet_balance_withdraw_successfully');
                $user->forceWithdraw($request->amount);
                break;

            default:
                abort(404);
                break;
        }
        return response()->json([
            'error' => false,
            'message' => $msg,
        ]);

    }
}
