<?php

namespace App\Http\Controllers;

use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Matcher\Subset;
use Throwable;

class SubjectTeacherController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('subject-teacher-list')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $subjects = Subject::orderByDesc('id')->get();

        $class_section = ClassSection::with('class', 'section')
            ->withOutTrashedRelations('class', 'section')
            ->get();
        $teachers = Teacher::with('user')->get();

        return view('subject.teacher', compact('class_section', 'teachers', 'subjects'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (! Auth::user()->canAny(['subject-teacher-create', 'subject-teacher-edit'])) {

            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $request->validate([
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'teacher_id' => 'required',
        ]);

        try {

            foreach ($request->teacher_id as $teacher_id) {
                if (! empty($request->id)) {
                    $subject_teacher = SubjectTeacher::find($request->id);
                } else {
                    $subject_teacher = new SubjectTeacher();
                }
                $subject_teacher->class_section_id = $request->class_section_id;
                $subject_teacher->subject_id = $request->subject_id;
                $subject_teacher->teacher_id = $teacher_id;
                $subject_teacher->save();
            }
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function update(Request $request)
    {

        if (! Auth::user()->can('subject-teacher-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $request->validate([
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'teacher_id' => 'required',
        ]);

        try {
            $subject_teacher = SubjectTeacher::find($request->id);
            $subject_teacher->update([
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
            ]);
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        if (! Auth::user()->can('subject-teacher-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
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

        $sql = SubjectTeacher::SubjectTeacher()->with('class_section.class', 'subject', 'teacher');
        if (! empty(request('search'))) {
            $search = request('search');
            $sql->where('id', 'LIKE', "%$search%")
                ->orWhereHas('class_section.class', function ($q) use ($search) {

                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('class_section.section', function ($q) use ($search) {

                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('teacher.user', function ($q) use ($search) {
                    $q->whereRaw("concat(users.first_name,' ',users.last_name) LIKE '%" . $search . "%'")->orwhere('users.first_name', 'LIKE', "%$search%")->orwhere('users.last_name', 'LIKE', "%$search%");
                });
        }

        if (request('class_id')) {
            $sql = $sql->where('class_section_id', request('class_id'));
        }
        if (request('teacher_id')) {
            $sql = $sql->where('teacher_id', request('teacher_id'));
        }
        if (request('subject_id')) {
            $sql = $sql->where('subject_id', request('subject_id'));
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

            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' data-url=' . url('subject-teachers') . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-url=' . url('subject-teachers', $row->id) . ' title="Delete"><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['class_section_name'] = $row->class_section?->class->name . ' - ' . $row->class_section?->section?->name . ' ' . $row->class_section?->class?->medium->name;
            $tempRow['stream_id'] = $row->class_section->class->streams->id ?? '-';
            $tempRow['stream_name'] = $row->class_section->class->streams->name ?? '-';
            $tempRow['subject_id'] = $row->subject_id;
            $tempRow['subject_name'] = $row->subject->name . " ( {$row->subject->type} ) ";
            $tempRow['teacher_id'] = $row->teacher_id;
            $tempRow['teacher_name'] = ($row->teacher) ? ($row->teacher->user->first_name . ' ' . $row->teacher->user->last_name) : '';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subject_teacher = SubjectTeacher::find($id);
        return response($subject_teacher);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Auth::user()->can('subject-teacher-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            $timetables = Timetable::where('subject_teacher_id', $id)->count();
            if ($timetables) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_beacuse_data_is_associated_with_other_data')
                );
            } else {
                SubjectTeacher::find($id)->delete();
                $response = [
                    'error' => false,
                    'message' => trans('data_delete_successfully')
                ];
            }
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}
