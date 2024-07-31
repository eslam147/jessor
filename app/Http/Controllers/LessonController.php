<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Students;
use App\Rules\YouTubeUrl;
use App\Models\ClassSchool;
use App\Models\LessonTopic;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\Lesson\LessonStatus;
use App\Rules\uniqueLessonInClass;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Nullable;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Auth::user()->can('lesson-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $class_section = ClassSection::SubjectTeacher()->with('class.medium', 'section', 'class.streams')->get();
        $subjects = Subject::SubjectTeacher()->orderBy('id')->get();
        $lessons = Lesson::relatedToTeacher()->withCount('enrollments')->with('file')->get();

        return response(view('lessons.index', compact('class_section', 'subjects', 'lessons')));
    }


    public function store(Request $request)
    {
        if (! Auth::user()->can('lesson-create')) {
            $response = [
                'error' => true,
                'message' => trans('no_permission_message')
            ];
            return response()->json($response);
        }
        $teacher = auth()->user()->load('teacher')->teacher;

        $validator = Validator::make($request->all(), [
            'name' => ['required', new uniqueLessonInClass($request->class_section_id, $request->subject_id)],
            'description' => 'required',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'status' => ['required', Rule::in(LessonStatus::values())],
            'payment_status' => 'required|in:0,1',

            'file' => 'nullable|array',
            'file.*.type' => ['nullable', Rule::in(['file_upload', 'youtube_link', 'video_upload', 'video_corner_link', 'video_corner_download_link', 'other_link'])],
            'file.*.name' => 'required_with:file.*.type',
            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_corner_link,video_corner_download_link,video_upload,other_link|max:2048',
            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
            // 'file.*.link' => 'required_if:file.*.type,youtube_link,other_link',
            //Regex for Youtube Link
            'file.*.link' => ['required_if:file.*.type,youtube_link', new YouTubeUrl, 'nullable'],
            'file.*.video_corner_url' => ['required_if:file.*.type,video_corner_url', 'nullable'],
            //Regex for Other Link
            // 'file.*.link'=>'required_if:file.*.type,other_link|url'
        ], [
            'name.unique' => trans('lesson_alredy_exists')
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ]);
        }
        try {

            $lesson = Lesson::create([
                'name' => $request->name,
                'description' => $request->description,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $teacher->id,
                'status' => $request->status,
                'is_paid' => ($request->payment_status == 1),
            ]);

            foreach ($request->file as $key => $file) {
                if ($file['type']) {
                    $lesson_file = new File();
                    $lesson_file->file_name = $file['name'];
                    $lesson_file->modal()->associate($lesson);

                    if ($file['type'] == "file_upload") {
                        $lesson_file->type = 1;
                        $lesson_file->file_url = $file['file']->store('lessons', 'public');
                    } elseif ($file['type'] == "youtube_link") {
                        $lesson_file->type = 2;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;
                        $lesson_file->file_url = $file['link'];
                    } elseif ($file['type'] == "video_upload") {
                        $lesson_file->type = 3;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;

                        $lesson_file->file_url = $file['file']->store('lessons', 'public');
                    } elseif ($file['type'] == "video_corner_link") {
                        $lesson_file->type = 5;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;
                        $lesson_file->file_url = $file['video_corner_url'];

                    } elseif ($file['type'] == "video_corner_download_link") {
                        $lesson_file->type = 6;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;
                        $lesson_file->file_url = $file['video_corner_url'];
                        $lesson_file->download_link = $file['video_corner_download_link'];

                } elseif ($file['type'] == "other_link") {
                        $lesson_file->type = 4;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;
                        $lesson_file->file_url = $file['link'];
                    }
                    $lesson_file->save();
                }
            }

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'exception' => $e
            ];
        }
        return response()->json($response);
    }

    public function show()
    {
        if (! Auth::user()->can('lesson-list')) {
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

        $sql = Lesson::lessonteachers()->relatedToTeacher()->with('subject', 'class_section', 'topic');
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                    ->orwhere('name', 'LIKE', "%$search%")
                    ->orwhere('description', 'LIKE', "%$search%")
                    ->orwhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                    ->orwhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                    ->orWhereHas('class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })->orWhereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        if (filled(request('subject_id'))) {
            $sql = $sql->where('subject_id', request('subject_id'));
        }
        if (request('class_id')) {
            $sql = $sql->where('class_section_id', request('class_id'));
        }
        if (request('lesson_id')) {
            $sql = $sql->where(
                'id',
                request('lesson_id')
            );
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $sql->withCount('enrollments');

        $res = $sql->get();
        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {

            $row = (object) $row;
            $operate = '<a href=' . route('lesson.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('lesson.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';
            $tempRow['payment_status'] = view('lessons.datatable.is_paid', ['row' => $row])->render();
            $tempRow['is_paid'] = $row->is_paid;

            $tempRow['status_name'] = view('lessons.datatable.status', ['status' => $row->status])->render();
            $tempRow['status'] = $row->status;

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['description'] = $row->description;
            $tempRow['purchased_count'] = $row->enrollments_count;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['class_section_name'] = $row->class_section->class->name . ' ' . $row->class_section->section->name . ' - ' . $row->class_section->class->medium->name;
            $tempRow['subject_id'] = $row->subject_id;
            $tempRow['subject_name'] = $row->subject->name . ' - ' . $row->subject->type;
            $tempRow['topic'] = $row->topic;
            $tempRow['file'] = $row->file;
            $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
            $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SubjectLesson $subjectLesson
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($request->edit_id);
        $teacher = auth()->user()->load('teacher')->teacher;

        if (! Auth::user()->can('lesson-edit') || $lesson->teacher_id != $teacher->id) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'edit_id' => 'required|numeric',
                'name' => ['required', new uniqueLessonInClass($request->class_section_id, $request->subject_id, $request->edit_id)],
                'description' => 'required',
                'class_section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',
                'status' => ['required', Rule::in(LessonStatus::values())],

                'payment_status' => 'required|in:0,1',

                'edit_file' => 'nullable|array',
                'edit_file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                'edit_file.*.name' => 'nullable|required_with:edit_file.*.type',
                // 'edit_file.*.link' => 'nullable|required_if:edit_file.*.type,youtube_link,other_link',

                // for Youtube Link
                'edit_file.*.link' => ['nullable|required_if:edit_file.*.type,youtube_link', new YouTubeUrl, 'nullable'],

                'file' => 'nullable|array',
                'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                'file.*.name' => 'nullable|required_with:file.*.type',
                'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link|max:2048',
                'file.*.file' => 'nullable|required_if:file.*.type,file_upload,video_upload',
                // 'file.*.link' => 'nullable|required_if:file.*.type,youtube_link,other_link',

                //Regex for Youtube Link
                'file.*.link' => ['nullable|required_if:file.*.type,youtube_link', new YouTubeUrl],
                //Regex for Other Link
                // 'file.*.link'=>'required_if:file.*.type,other_link|url'
            ],
            [
                'name.unique' => trans('lesson_alredy_exists')
            ]
        );
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
            );
            return response()->json($response);
        }
        try {
            $lesson->name = $request->name;
            $lesson->description = $request->description;
            $lesson->class_section_id = $request->class_section_id;
            $lesson->subject_id = $request->subject_id;
            $lesson->status = $request->status;
            $lesson->is_paid = $request->payment_status;
            $lesson->save();

            // Update the Old Files
            foreach ($request->edit_file as $file) {
                if ($file['type']) {
                    $lesson_file = File::find($file['id']);
                    $lesson_file->file_name = $file['name'];

                    if ($file['type'] == "file_upload") {
                        $lesson_file->type = 1;
                        if (! empty($file['file'])) {
                            if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                            }
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                        }
                    } elseif ($file['type'] == "youtube_link") {
                        $lesson_file->type = 2;
                        if (! empty($file['thumbnail'])) {
                            if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_thumbnail'))) {
                                Storage::disk('public')->delete($lesson_file->getRawOriginal('file_thumbnail'));
                            }

                            $image = $file['thumbnail'];
                            // made file name with combination of current time
                            $file_name = time() . '-' . $image->getClientOriginalName();
                            //made file path to store in database
                            $file_path = 'lessons/' . $file_name;
                            //resized image
                            resizeImage($image);
                            //stored image to storage/public/lessons folder
                            $destinationPath = storage_path('app/public/lessons');
                            $image->move($destinationPath, $file_name);

                            $lesson_file->file_thumbnail = $file_path;
                        }

                        $lesson_file->file_url = $file['link'];
                    } elseif ($file['type'] == "video_upload") {
                        $lesson_file->type = 3;
                        if (! empty($file['file'])) {
                            if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                            }
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                        }

                        if (! empty($file['thumbnail'])) {
                            if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_thumbnail'))) {
                                Storage::disk('public')->delete($lesson_file->getRawOriginal('file_thumbnail'));
                            }

                            $image = $file['thumbnail'];
                            // made file name with combination of current time
                            $file_name = time() . '-' . $image->getClientOriginalName();
                            //made file path to store in database
                            $file_path = 'lessons/' . $file_name;
                            //resized image
                            resizeImage($image);
                            //stored image to storage/public/lessons folder
                            $destinationPath = storage_path('app/public/lessons');
                            $image->move($destinationPath, $file_name);

                            $lesson_file->file_thumbnail = $file_path;
                        }
                    } elseif ($file['type'] == "video_corner_link") {
                        $lesson_file->type = 5;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;
                        $lesson_file->file_url = $file['video_corner_url'];

                    } elseif ($file['type'] == "video_corner_download_link") {
                        $lesson_file->type = 6;

                        $image = $file['thumbnail'];
                        // made file name with combination of current time
                        $file_name = time() . '-' . $image->getClientOriginalName();
                        //made file path to store in database
                        $file_path = 'lessons/' . $file_name;
                        //resized image
                        resizeImage($image);
                        //stored image to storage/public/lessons folder
                        $destinationPath = storage_path('app/public/lessons');
                        $image->move($destinationPath, $file_name);

                        $lesson_file->file_thumbnail = $file_path;
                        $lesson_file->file_url = $file['video_corner_url'];
                        $lesson_file->download_link = $file['video_corner_download_link'];

                    } elseif ($file['type'] == "other_link") {
                        $lesson_file->type = 4;
                        if (! empty($file['thumbnail'])) {
                            if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_thumbnail'))) {
                                Storage::disk('public')->delete($lesson_file->getRawOriginal('file_thumbnail'));
                            }

                            $image = $file['thumbnail'];
                            // made file name with combination of current time
                            $file_name = time() . '-' . $image->getClientOriginalName();
                            //made file path to store in database
                            $file_path = 'lessons/' . $file_name;
                            //resized image
                            resizeImage($image);
                            //stored image to storage/public/lessons folder
                            $destinationPath = storage_path('app/public/lessons');
                            $image->move($destinationPath, $file_name);

                            $lesson_file->file_thumbnail = $file_path;
                        }
                        $lesson_file->file_url = $file['link'];
                    }

                    $lesson_file->save();
                }
            }

            //Add the new Files
            if ($request->file) {
                foreach ($request->file as $key => $file) {
                    if ($file['type']) {
                        $lesson_file = new File();
                        $lesson_file->file_name = $file['name'];
                        $lesson_file->modal()->associate($lesson);

                        if ($file['type'] == "file_upload") {
                            $lesson_file->type = 1;
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                        } elseif ($file['type'] == "youtube_link") {
                            $lesson_file->type = 2;

                            $image = $file['thumbnail'];
                            // made file name with combination of current time
                            $file_name = time() . '-' . $image->getClientOriginalName();
                            //made file path to store in database
                            $file_path = 'lessons/' . $file_name;
                            //resized image
                            resizeImage($image);
                            //stored image to storage/public/lessons folder
                            $destinationPath = storage_path('app/public/lessons');
                            $image->move($destinationPath, $file_name);

                            $lesson_file->file_thumbnail = $file_path;
                            $lesson_file->file_url = $file['link'];
                        } elseif ($file['type'] == "video_upload") {
                            $lesson_file->type = 3;
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');

                            $image = $file['thumbnail'];
                            // made file name with combination of current time
                            $file_name = time() . '-' . $image->getClientOriginalName();
                            //made file path to store in database
                            $file_path = 'lessons/' . $file_name;
                            //resized image
                            resizeImage($image);
                            //stored image to storage/public/lessons folder
                            $destinationPath = storage_path('app/public/lessons');
                            $image->move($destinationPath, $file_name);

                            $lesson_file->file_thumbnail = $file_path;
                        } elseif ($file['type'] == "other_link") {
                            $lesson_file->type = 4;

                            $image = $file['thumbnail'];
                            // made file name with combination of current time
                            $file_name = time() . '-' . $image->getClientOriginalName();
                            //made file path to store in database
                            $file_path = 'lessons/' . $file_name;
                            //resized image
                            resizeImage($image);
                            //stored image to storage/public/lessons folder
                            $destinationPath = storage_path('app/public/lessons');
                            $image->move($destinationPath, $file_name);

                            $lesson_file->file_thumbnail = $file_path;
                            $lesson_file->file_url = $file['link'];
                        }
                        $lesson_file->save();
                    }
                }
            }
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'exception' => $e
            ];
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        $lesson = Lesson::relatedToTeacher()->find($id);

        if (! Auth::user()->can('lesson-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {

            $lesson_topics = LessonTopic::where('lesson_id', $id)->relatedToTeacher()->count();
            if ($lesson_topics) {
                $response = [
                    'error' => true,
                    'message' => trans('cannot_delete_beacuse_data_is_associated_with_other_data')
                ];
            } else {

                $lesson = Lesson::relatedToTeacher()->findOrFail($id);
                if ($lesson->file) {
                    foreach ($lesson->file as $file) {
                        if (Storage::disk('public')->exists($file->file_url)) {
                            Storage::disk('public')->delete($file->file_url);
                        }
                    }
                }
                $lesson->file()->delete();
                $lesson->delete();

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


    public function search(Request $request)
    {
        $lesson = new Lesson;
        if (isset($request->subject_id)) {
            $lesson = $lesson->where('subject_id', $request->subject_id);
        }

        if (isset($request->class_section_id)) {
            $lesson = $lesson->where('class_section_id', $request->class_section_id);
        }
        $lesson = $lesson->relatedToTeacher()->get();
        $response = array(
            'error' => false,
            'data' => $lesson,
            'message' => 'Lesson fetched successfully'
        );
        return response()->json($response);
    }

    public function deleteFile($id)
    {
        try {
            $file = File::findOrFail($id);
            if (Storage::disk('public')->exists($file->file_url)) {
                Storage::disk('public')->delete($file->file_url);
            }
            $file->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}