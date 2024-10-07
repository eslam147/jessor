<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('language-create')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        return view('settings.language_setting');
    }

    public function language_sample()
    {
        if (! Auth::user()->can('language-create')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $filePath = base_path("resources/lang/en.json");
        $headers = ['Content-Type: application/json'];
        $fileName = 'language.json';
        if (File::exists(base_path("resources/lang/en.json"))) {
            return response()->download($filePath, $fileName, $headers);
        } else {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred')
            ]);
        }
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('language-create')) {
            return redirect(route('home'))->withErrors([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'code' => 'required|in:ar,en|unique:languages,code',
        ]);

        try {
            Language::create([
                'name' => $request->name,
                'code' => $request->code,
                'status' =>  $request->boolean('active'),
                'is_rtl' => $request->rtl ? 1 : 0,
            ]);

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully'),
            ];
        } catch (Throwable $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ];
        }
        return response()->json($response);
    }

    public function show()
    {
        if (! Auth::user()->can('language-list')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        $sql = Language::where('id', '!=', 0);
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orwhere('name', 'LIKE', "%$search%")
                ->orwhere('code', 'LIKE', "%$search%")
                ->orwhere('status', 'LIKE', "%$search%");
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . '" data-url="' . url('language', $row->id) . '" title="Delete"><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['code'] = $row->code;
            $tempRow['rtl'] = $row->is_rtl;
            $tempRow['status'] = $row->status ? 'Active' : 'Inactive';
            $tempRow['is_active'] = $row->status;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update(Request $request)
    {
        if (! Auth::user()->can('language-edit')) {
            return redirect(route('home'))->withErrors([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $language = Language::findOrFail($request->id);
        $request->validate([
            'name' => 'required',
            'code' => ['required', Rule::in('ar', 'en'), 'unique:languages,code,' . $request->id],
        ]);

        try {
            $language->update([
                'name' => $request->name,
                'code' => $request->code,
                'is_rtl' => $request->rtl ? 1 : 0,
                'status' => $request->boolean('status') ? 1 : 0,
            ]);
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully'),
            ];
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ];
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        if (! Auth::user()->can('language-delete')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        try {
            Language::find($id)->delete();
            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred')
            ];
        }
        return response()->json($response);
    }

    public function set_language(Request $request)
    {
        Session::put('locale', $request->lang);
        $language = Language::where('code', $request->lang)->first();
        Session::save();
        Session::put('language', $language);
        app()->setLocale(Session::get('locale'));
        return redirect()->back();
    }
}
