<?php

namespace App\Http\Controllers;

use App\Traits\TenantImageTrait;
use Throwable;
use App\Models\User;
use App\Models\Teacher;
use App\Models\FormField;

use App\Models\ClassTeacher;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use Illuminate\Support\Facades\{
    DB,
    Auth,
    Hash,
    Mail,
    Storage,
    Validator
};
class TeacherController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('teacher-list')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $teacherFields = FormField::where('for', 3)->orderBy('rank', 'ASC')->get();
        return view('teacher.index', compact('teacherFields'));
    }

    public function teacherListIndex()
    {
        if (! Auth::user()->can('teacher-list')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $teacherFields = FormField::where('for', 3)->orderBy('rank', 'ASC')->get();
        return view('teacher.details', compact('teacherFields'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('teacher-create') || ! Auth::user()->can('teacher-edit')) {
            return response()->json([
                'message' => trans('no_permission_message')
            ]);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'nullable|min:8',
                'mobile' => 'required|numeric|regex:/^[0-9]{7,16}$/',
            ],
            [
                'mobile.regex' => 'The mobile number must be a length of 7 to 15 digits.'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            DB::beginTransaction();
            // check if email exists in deleted_at records
            $check_user = User::where('email', $request->email)->onlyTrashed();
            if ($check_user->count()) {
                $user_exists = $check_user->first();
                DB::table('users')->where('id', $user_exists->id)->update(['deleted_at' => null]);
                $user = User::findOrFail($user_exists->id);
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    // made file name with combination of current time
                    $file_name = time() . '-' . $image->hashName();
                    //made file path to store in database
                    $file_path = 'teachers/' . $file_name;
                    //resized image
                    resizeImage($image);
                    //stored image to storage/public/teachers folder
                    $destinationPath = storage_path('app/public/teachers');
                    $image->move($destinationPath, $file_name);

                    $user->image = $file_path;
                } else {
                    $user->image = "";
                }
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->gender = $request->gender;
                $user->current_address = $request->current_address;
                $user->permanent_address = $request->permanent_address;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->dob = date('Y-m-d', strtotime($request->dob));
                $user->update();

                $check_teacher = DB::table('teachers')->where('user_id', $user->id)->whereNotNull('deleted_at');
                if ($check_teacher->count()) {
                    $teacher_exists = $check_teacher->first();
                    DB::table('teachers')->where('id', $teacher_exists->id)->update(['deleted_at' => null]);

                    $teacher = Teacher::findOrFail($teacher_exists->id);

                    $formFields = FormField::where('for', 3)->orderBy('rank', 'ASC')->get();
                    $data = [];
                    $status = 0;
                    $dynamic_data = json_decode($teacher->dynamic_field_values, true);
                    foreach ($formFields as $form_field) {
                        // INPUT TYPE CHECKBOX
                        if ($form_field->type == 'checkbox') {
                            if ($status == 0) {
                                $data[] = $request->input('checkbox', []);
                                $status = 1;
                            }
                        } else if ($form_field->type == 'file') {
                            // INPUT TYPE FILE
                            $get_file = '';
                            $field = str_replace(" ", "_", $form_field->name);
                            if ($dynamic_data && count($dynamic_data) > 0) {
                                foreach ($dynamic_data as $field_data) {
                                    if (isset($field_data[$field])) { // GET OLD FILE IF EXISTS
                                        $get_file = $field_data[$field];
                                    }
                                }
                            }
                            $hidden_file_name = 'file-' . $field;

                            if ($request->hasFile($field)) {
                                if ($get_file) {
                                    Storage::disk('public')->delete($get_file); // DELETE OLD FILE IF NEW FILE IS SELECT
                                }
                                $data[] = [str_replace(" ", "_", $form_field->name) => $request->file($field)->store('student', 'public')];
                            } else {
                                if ($request->$hidden_file_name) {
                                    $data[] = [str_replace(" ", "_", $form_field->name) => $request->$hidden_file_name];
                                }
                            }
                        } else {
                            $field = str_replace(" ", "_", $form_field->name);
                            $data[] = [str_replace(" ", "_", $form_field->name) => $request->$field];
                        }
                    }
                    $teacher->user_id = $user->id;
                    $teacher->qualification = $request->qualification;
                    $teacher->dynamic_fields = json_encode($data);
                    $teacher->update();
                }
                if ($request->grant_permission) {
                    $user->givePermissionTo([
                        'student-list',
                        'parents-create',
                        'parents-list',
                    ]);
                } else {
                    $user->revokePermissionTo([
                        'student-create',
                        'student-list',
                        'student-edit',
                        'student-delete',
                        'parents-create',
                        'parents-list',
                        'parents-edit'
                    ]);
                }
                $user->assignRole([2]);
                $school_name = getSettings('school_name');
                $data = [
                    'subject' => 'Welcome to ' . $school_name['school_name'],
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'school_name' => $school_name['school_name']
                ];

                // Mail::send('teacher.email', $data, function ($message) use ($data) {
                //     $message->to($data['email'])->subject($data['subject']);
                // });
            } else {
                $user = new User();
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    // made file name with combination of current time
                    $file_name = time() . '-' . $image->hashName();
                    //made file path to store in database
                    $file_path = 'teachers/' . $file_name;
                    //resized image
                    resizeImage($image);
                    //stored image to storage/public/teachers folder
                    $destinationPath = storage_path('app/public/teachers');
                    $image->move($destinationPath, $file_name);

                    $user->image = $file_path;
                } else {
                    $user->image = "";
                }
                //$teacher_plain_text_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
                $user->password = Hash::make($request->password);

                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->gender = $request->gender;
                $user->current_address = $request->current_address;
                $user->permanent_address = $request->permanent_address;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->dob = date('Y-m-d', strtotime($request->dob));
                $user->save();


                $teacher = new Teacher();

                $formFields = FormField::where('for', 3)->orderBy('rank', 'ASC')->get();
                $data = [];
                $status = 0;
                $dynamic_data = json_decode($teacher->dynamic_field_values, true);
                foreach ($formFields as $form_field) {
                    // INPUT TYPE CHECKBOX
                    if ($form_field->type == 'checkbox') {
                        if ($status == 0) {
                            $data[] = $request->input('checkbox', []);
                            $status = 1;
                        }
                    } else if ($form_field->type == 'file') {
                        // INPUT TYPE FILE
                        $get_file = '';
                        $field = str_replace(" ", "_", $form_field->name);
                        if ($dynamic_data && count($dynamic_data) > 0) {
                            foreach ($dynamic_data as $field_data) {
                                if (isset($field_data[$field])) { // GET OLD FILE IF EXISTS
                                    $get_file = $field_data[$field];
                                }
                            }
                        }
                        $hidden_file_name = 'file-' . $field;

                        if ($request->hasFile($field)) {
                            if ($get_file) {
                                Storage::disk('public')->delete($get_file); // DELETE OLD FILE IF NEW FILE IS SELECT
                            }
                            $data[] = [str_replace(" ", "_", $form_field->name) => $request->file($field)->store('teachers', 'public')];
                        } else {
                            if ($request->$hidden_file_name) {
                                $data[] = [str_replace(" ", "_", $form_field->name) => $request->$hidden_file_name];
                            }
                        }
                    } else {
                        $field = str_replace(" ", "_", $form_field->name);
                        $data[] = [str_replace(" ", "_", $form_field->name) => $request->$field];
                    }
                }
                $teacher->user_id = $user->id;
                $teacher->qualification = $request->qualification;
                $teacher->dynamic_fields = json_encode($data);
                $teacher->save();
                if ($request->grant_permission) {
                    $user->givePermissionTo([
                        'student-create',
                        'student-list',
                        'student-edit',
                        'student-delete',
                        'parents-create',
                        'parents-list',
                        'parents-edit'
                    ]);
                } else {
                    $user->revokePermissionTo([
                        'student-create',
                        'student-list',
                        'student-edit',
                        'student-delete',
                        'parents-create',
                        'parents-list',
                        'parents-edit'
                    ]);
                }
                $user->assignRole([2]);
                $school_name = getSettings('school_name');
                $data = [
                    'subject' => 'Welcome to ' . $school_name['school_name'],
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'school_name' => $school_name['school_name']
                ];
                if ($request->password) {
                    $data['password'] = Hash::make($request->password);
                }
                // Mail::send('teacher.email', $data, function ($message) use ($data) {
                //     $message->to($data['email'])->subject($data['subject']);
                // });
            }
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
            DB::commit();
        } catch (Throwable $e) {
            report($e);
            DB::rollBack();

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
        if (! Auth::user()->can('teacher-list')) {
            return response()->json([
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

        $sql = Teacher::with('user');
        if (! empty(request('search'))) {
            $search = request('search');
            $sql->where('id', 'LIKE', "%{$search}%")
                ->orwhere('user_id', 'LIKE', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orwhere('last_name', 'LIKE', "%{$search}%")
                        ->orwhere('gender', 'LIKE', "%{$search}%")
                        ->orwhere('email', 'LIKE', "%{$search}%")
                        ->orwhere('dob', 'LIKE', "%" . date('Y-m-d', strtotime($search)) . "%")
                        ->orwhere('qualification', 'LIKE', "%{$search}%")
                        ->orwhere('current_address', 'LIKE', "%{$search}%")
                        ->orwhere('permanent_address', 'LIKE', "%{$search}%");
                });
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
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' data-url=' . url('teachers') . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-user_id=' . $row->user_id . ' data-url=' . url('teachers', $row->user_id) . ' title="Delete"><i class="fa fa-trash"></i></a>';
            if (Auth::user()->can('teacher-delete')) {
                if ($row->user->isNotBanned()) {
                    $operate .= "<a data-url=" . route('users.ban', $row->user->id) . " class='btn btn-xs btn-danger btn-rounded user_ban' data-id='{$row->user->id}' title='Ban {$row->user->full_name}'><i class='fa fa-lock'></i>Block</a>&nbsp;&nbsp;";
                } else {
                    $operate .= "<a data-url=" . route('users.unban', $row->user->id) . " class='btn btn-xs btn-success btn-rounded user_unban' data-id='{$row->user->id}' title='unBan {$row->user->full_name}'><i class='fa fa-unlock'></i>UnBlock</a>&nbsp;&nbsp;";
                }
            }
            $data = getSettings('date_formate');

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['last_name'] = $row->user->last_name;
            $tempRow['gender'] = $row->user->gender;
            $tempRow['current_address'] = $row->user->current_address;
            $tempRow['permanent_address'] = $row->user->permanent_address;
            $tempRow['email'] = $row->user->email;
            $tempRow['dob'] = date($data['date_formate'], strtotime($row->user->dob));
            $tempRow['mobile'] = $row->user->mobile;
            $tempRow['image'] = $row->user->image;
            $tempRow['qualification'] = $row->qualification;
            $tempRow['dynamic_data_field'] = json_decode($row->dynamic_fields);

            if ($row->user->can('student-create', 'student-list', 'student-edit', 'parents-create', 'parents-list', 'parents-edit')) {
                $tempRow['has_student_permissions'] = 1;
            } else {
                $tempRow['has_student_permissions'] = 0;
            }

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
        $teacher = Teacher::findOrFail($id);
        return response($teacher);
    }


    public function update(Request $request)
    {
        if (! Auth::user()->can('teacher-edit')) {
            $response = [
                'message' => trans('no_permission_message')
            ];
            return response()->json($response);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'password' => 'required',
                'mobile' => 'required|numeric|regex:/^[0-9]{7,16}$/',
                'dob' => 'required|date',
                'qualification' => 'required',
                'current_address' => 'required',
                'permanent_address' => 'required',
            ],
            [
                'mobile.regex' => 'The mobile number must be a length of 7 to 15 digits.'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $user = User::find($request->user_id);
            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
                $image = $request->file('image');
                // made file name with combination of current time
                $file_name = time() . '-' . $image->hashName();
                //made file path to store in database
                $file_path = 'teachers/' . $file_name;
                //resized image
                resizeImage($image);
                //stored image to storage/public/teachers folder
                $destinationPath = storage_path('app/public/teachers');
                $image->move($destinationPath, $file_name);

                $user->image = $file_path;
            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->gender = $request->gender;
            $user->current_address = $request->current_address;
            $user->permanent_address = $request->permanent_address;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = Hash::make($request->password);
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->save();

            $teacher = Teacher::find($request->id);

            // Teacher dynamic fields
            $formFields = FormField::where('for', 3)->orderBy('rank', 'ASC')->get();
            $data = [];
            $status = 0;
            $i = 0;
            $dynamic_data = json_decode($teacher->dynamic_fields, true);
            foreach ($formFields as $form_field) {
                // INPUT TYPE CHECKBOX
                if ($form_field->type == 'checkbox') {
                    if ($status == 0) {
                        $data[] = $request->input('checkbox', []);
                        $status = 1;
                    }
                } else if ($form_field->type == 'file') {
                    // INPUT TYPE FILE
                    $get_file = '';
                    $field = str_replace(" ", "_", $form_field->name);
                    if (! is_null($dynamic_data)) {
                        foreach ($dynamic_data as $field_data) {
                            if (isset($field_data[$field])) { // GET OLD FILE IF EXISTS
                                $get_file = $field_data[$field];
                            }
                        }
                    }
                    $hidden_file_name = $field;

                    if ($request->hasFile($field)) {
                        if ($get_file) {
                            Storage::disk('public')->delete($get_file); // DELETE OLD FILE IF NEW FILE IS SELECT
                        }
                        $data[] = [
                            str_replace(" ", "_", $form_field->name) => $request->file($field)->store('teachers', 'public')
                        ];
                    } else {
                        if ($request->$hidden_file_name) {
                            $data[] = [
                                str_replace(" ", "_", $form_field->name) => $request->$hidden_file_name
                            ];
                        }
                    }
                } else {
                    $field = str_replace(" ", "_", $form_field->name);
                    $data[] = [
                        str_replace(" ", "_", $form_field->name) => $request->$field
                    ];
                }
            }
            $status = 0;
            // End teacher dynamic field
            $teacher->user_id = $user->id;
            $teacher->qualification = $request->qualification;
            $teacher->dynamic_fields = json_encode($data);
            $teacher->save();

            if ($request->edit_grant_permission) {
                $user->givePermissionTo([
                    'student-create',
                    'student-list',
                    'student-edit',
                    'student-delete',
                    'parents-create',
                    'parents-list',
                    'parents-edit'
                ]);
            } else {
                $user->revokePermissionTo([
                    'student-create',
                    'student-list',
                    'student-edit',
                    'student-delete',
                    'parents-create',
                    'parents-list',
                    'parents-edit'
                ]);
            }

            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Auth::user()->can('teacher-delete')) {
            return response()->json([
                'message' => trans('no_permission_message')
            ]);
        }
        try {
            $teacher = Teacher::where('user_id', $id)->with('user')->first();
            // Check whether the teacher exists in other tables
            $subject_teacherCount = SubjectTeacher::where('teacher_id', $teacher->id)->count();
            $class_teacherCount = ClassTeacher::where('class_teacher_id', $teacher->id)->count();

            if ($subject_teacherCount > 0 || $class_teacherCount > 0) {
                $response = [
                    'error' => true,
                    'message' => trans('cannot_delete_beacuse_data_is_associated_with_other_data')
                ];
            } else {
                // Delete related records and user
                $classTeachers = ClassTeacher::where('class_teacher_id', $teacher->id)->pluck('id');

                foreach ($classTeachers as $classTeacher) {

                    $classTeacher->delete();
                }
                $teacher->user->revokePermissionTo('class-teacher');
                $user = User::find($id);
                if (Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }
                $user->delete();

                Teacher::where('user_id', $id)->delete();
                $response = [
                    'error' => false,
                    'message' => trans('data_delete_successfully')
                ];
            }
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred')
            ];
        }
        return response()->json($response);
    }
}
