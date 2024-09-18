<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Students;
use App\Rules\YouTubeUrl;
use App\Models\LessonTopic;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\uniqueTopicInLesson;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LessonTopicController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('topic-list')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $class_section = ClassSection::SubjectTeacher()->with('class', 'section')->withOutTrashedRelations('class', 'section')->get();
        $subjects = Subject::SubjectTeacher()->orderBy('id', 'ASC')->get();
        
        $lessons = Lesson::relatedToTeacher()->get();
        return response(view('lessons.topic', compact('class_section', 'subjects', 'lessons')));
    }


    public function store(Request $request)
    {
        if (! Auth::user()->can('topic-create')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $teacher = Auth::user()->load('teacher')->teacher;
        $validator = Validator::make(
            $request->all(),
            [
                'class_section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',
                'lesson_id' => ['required', 'numeric', Rule::exists('lessons', 'id')->where('teacher_id', $teacher->id)],
                'name' => ['required', new uniqueTopicInLesson($request->lesson_id)],
                'description' => 'required',
                // ------------------------------------------ //
                'edit_file' => 'nullable|array',
                'edit_file.*.type' => ['nullable', Rule::in(File::$types)],
                'edit_file.*.name' => 'required_if:edit_file.*.type,!=,online_exam,assignment',
                // ------------------------------------------ //
                // 'file.*.name' => 'required_if:file.*.type,!=,online_exam,assignment',
                'edit_file.*.thumbnail' => 'required_if:edit_file.*.type,youtube_link,video_upload,other_link',
                //Regex for Youtube Link
                'edit_file.*.link' => ['nullable', 'required_if:edit_file.*.type,youtube_link', new YouTubeUrl],
                'edit_file.*.online_exam' => ['required_if:edit_file.*.type,online_exam', 'nullable'],
                'edit_file.*.assignments' => ['required_if:edit_file.*.type,assignment', 'nullable'],
                'file' => 'nullable|array',
                'file.*.type' => ['nullable', Rule::in(File::$types)],
                'file.*.video_corner_url' => ['required_if:file.*.type,video_corner_link', 'nullable'],
                // ------------------------------------------ //
                'file.*.assignments' => ['required_if:file.*.type,assignment', 'nullable', Rule::exists('assignments', 'id')],
                'file.*.online_exam' => ['required_if:file.*.type,online_exam', 'nullable', Rule::exists('online_exams', 'id')],
                'file.*.download_link' => ['required_if:file.*.type,video_corner_link', 'url', 'nullable'],
                // -----------------------------------------------
                'file.*.file' => 'required_if:file.*.type,file_upload,video_upload|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3,webm|max:5125',
                //Regex for Youtube Link
                // -----------------------------------------------
                'file.*.link' => ['nullable', 'required_if:file.*.type,youtube_link', new YouTubeUrl],
                // -----------------------------------------------
                'file.*.external_link' => ['nullable', 'required_if:file.*.type,external_link', 'url'],
                // -----------------------------------------------
                'thumbnail' => 'required|image|mimes:jpg,jpeg,png',
            ],
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ]);
        } else {
            DB::beginTransaction();
            $topic = LessonTopic::create([
                'name' => $request->name,
                'description' => $request->description,
                'lesson_id' => $request->lesson_id,
            ]);
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $file_name = time() . '-' . $image->hashName();
                $file_path = "topics/{$file_name}";

                resizeImage($image);

                $destinationPath = storage_path('app/public/topics');
                $image->move($destinationPath, $file_name);

                $topic->thumbnail = $file_path;
            }
            foreach ($request->file as $data) {
                if ($data['type']) {
                    $file = new File();
                    $file->file_name = isset($data['name']) ? $data['name'] : null;
                    $file->modal()->associate($topic);
                    switch ($data['type']) {
                        case "file_upload":
                            $file->type = File::FILE_UPLOAD_TYPE;
                            $file->file_url = $data['file']->store('lessons', 'public');
                            break;
                        case "external_link":
                            $file->type = File::EXTERNAL_LINK;
                            $file->file_url = $data['external_link'];
                            break;
                        case "youtube_link":
                            $file->type = File::YOUTUBE_TYPE;
                            $file->file_url = $data['link'];
                            break;
                        case "video_corner_link":
                            $file->type = File::VIDEO_CORNER_TYPE;
                            $file->file_url = $data['video_corner_url'];
                            $file->download_link = $data['download_link'];
                            break;
                        case "video_upload":
                            $file->type = File::VIDEO_UPLOAD_TYPE;
                            $file->file_url = $data['file']->store('lessons', 'public');
                            break;
                        case "online_exam":
                            $file->type = File::ONLINE_EXAM_TYPE;
                            $file->online_exam_id = $data['online_exam'];
                            break;
                        case "assignment":
                            $file->type = File::ASSIGNMENT_TYPE;
                            $file->assignment_id = $data['assignments'];
                            break;
                        case "other_link":
                            $file->type = 4;
                            $file->file_url = $data['link'];
                            break;
                    }
                    $file->save();
                }
            }
            DB::commit();
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\LessonTopic $lessonTopic
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        if (! Auth::user()->can('topic-list')) {
            return response()->json([
                'error' => true,
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

        $sql = LessonTopic::relatedToTeacher()->with('lesson.class_section', 'lesson.subject', 'file');

        if (! empty(request('search'))) {
            $search = request('search');
            $sql->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%{$search}%")
                    ->orwhere('name', 'LIKE', "%{$search}%")
                    ->orwhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('lesson.class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })->orWhereHas('lesson.class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })->orWhereHas('lesson.subject', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })->orWhereHas('lesson', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        if (request('subject_id')) {
            $sql = $sql->whereHas('lesson', fn($q) => $q->where('subject_id', request('subject_id')));
        }
        if (request('class_id')) {
            $sql = $sql->whereHas('lesson', fn($q) => $q->where('class_section_id', request('class_id')));
        }
        if (request('lesson_id')) {
            $sql = $sql->where('lesson_id', request('lesson_id'));
        }
        $total = $sql->count();

        $res = $sql->orderBy($sort, $order)->skip($offset)->take($limit)->get();
        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {

            $row = (object) $row;
            $operate = '<a href=' . route('lesson-topic.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('lesson-topic.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['thumbnail'] = view('lessons.datatable.thumbnail', compact('row'))->render();
            $tempRow['description'] = $row->description;
            $tempRow['lesson_id'] = $row->lesson_id;
            $tempRow['lesson_name'] = $row->lesson->name;
            $tempRow['class_section_id'] = $row->lesson->class_section->id;
            $tempRow['class_section_name'] = $row->lesson->class_section?->class?->name . ' ' . $row->lesson->class_section?->section?->name . ' - ' . $row->lesson->class_section?->class?->medium?->name;
            $tempRow['subject_id'] = $row->lesson->subject->id;
            $tempRow['subject_name'] = $row->lesson->subject->name . ' - ' . $row->lesson->subject->type;
            $tempRow['file'] = $row->file;
            $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
            $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update(Request $request, LessonTopic $lessonTopic)
    {
        if (! Auth::user()->can('topic-edit')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        $validator = Validator::make($request->all(), [
            'edit_id' => 'required|numeric',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'name' => ['required', new uniqueTopicInLesson($request->lesson_id, $request->edit_id)],
            'description' => 'required',
            // ------------------------------------- \\
            'edit_file' => 'nullable|array',
            'edit_file.*.type' => ['nullable', Rule::in(File::$types)],
            'edit_file.*.name' => 'required_if:edit_file.*.type,!=,online_exam,assignment',
            'edit_file.*.video_corner_url' => ['required_if:edit_file.*.type,video_corner_link', 'nullable'],
            'edit_file.*.online_exam' => ['required_if:edit_file.*.type,online_exam', 'nullable'],
            'edit_file.*.assignments' => ['required_if:edit_file.*.type,assignment', 'nullable'],
            'edit_file.*.download_link' => ['required_if:edit_file.*.type,video_corner_link', 'url', 'nullable'],
            'edit_file.*.external_link' => ['nullable', 'required_if:edit_file.*.type,external_link', 'url'],
            //Regex for Youtube Link
            'edit_file.*.link' => ['nullable', 'required_if:edit_file.*.type,youtube_link', new YouTubeUrl],
            // ------------------------------------- \\

            // ------------------------------------- \\
            // ------------------------------------- \\
            'file' => 'nullable|array',
            'file.*.name' => 'required_if:edit_file.*.type,!=,online_exam,assignment',
            'file.*.type' => ['nullable', Rule::in(File::$types)],
            'file.*.video_corner_url' => ['required_if:file.*.type,video_corner_link', 'nullable'],
            'file.*.download_link' => ['required_if:file.*.type,video_corner_link', 'url', 'nullable'],
            'file.*.file' => 'nullable|required_if:file.*.type,file_upload,video_upload|mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3,webm|max:5120',
            //Regex for Youtube Link
            'file.*.link' => ['nullable', 'required_if:file.*.type,youtube_link', new YouTubeUrl],
            'file.*.external_link' => ['nullable', 'required_if:file.*.type,external_link', 'url'],
            // ------------------------------------- \\
        ], [
            'name.unique' => trans('topic_alredy_exists')
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ]);
        }
        try {
            $topic = LessonTopic::relatedToTeacher()->findOrFail($request->edit_id);
            $topic->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            // Update the Old Files
            foreach ($request->edit_file as $key => $file) {
                // $file['type'] = $file['type'] ?? null;
                if (! empty($file['type'])) {
                    $topic_file = File::find($file['id']);
                    $topic_file->file_name = $file['name'];
                    switch ($file['type']) {
                        case "file_upload":
                            $topic_file->type = File::FILE_UPLOAD_TYPE;
                            if (! empty($file['file'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_url = $file['file']->store('lessons', 'public');
                            }
                            break;
                        case "youtube_link":
                            $topic_file->type = File::YOUTUBE_TYPE;
                            $topic_file->file_url = $file['link'];
                            break;
                        case "video_upload":
                            $topic_file->type = File::VIDEO_UPLOAD_TYPE;
                            if (! empty($file['file'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_url = $file['file']->store('lessons', 'public');
                            }
                            break;
                        case "video_corner_link":
                            $topic_file->type = File::VIDEO_CORNER_TYPE;
                            $topic_file->file_url = $file['video_corner_url'];
                            $topic_file->download_link = $file['download_link'];
                            break;
                        case "external_link":
                            $topic_file->type = File::EXTERNAL_LINK;
                            $topic_file->file_url = $file['external_link'];
                            break;
                        case "online_exam":
                            $topic_file->type = File::ONLINE_EXAM_TYPE;
                            $topic_file->online_exam_id = $file['online_exam'];
                            $topic_file->file_url = null;
                            $topic_file->download_link = null;
                            $topic_file->assignment_id = null;
                            break;
                        case "assignment":
                            $topic_file->type = File::ASSIGNMENT_TYPE;
                            $topic_file->file_url = null;
                            $topic_file->download_link = null;
                            $topic_file->online_exam_id = null;
                            $topic_file->assignment_id = $file['assignments'];
                            break;
                        case "online_exam":
                            $topic_file->type = File::ONLINE_EXAM_TYPE;
                            $topic_file->online_exam_id = $file['online_exam'];
                            $topic_file->file_url = null;
                            $topic_file->download_link = null;
                            $topic_file->assignment_id = null;
                            break;
                        case "assignment":
                            $topic_file->type = File::ASSIGNMENT_TYPE;
                            $topic_file->file_url = null;
                            $topic_file->download_link = null;
                            $topic_file->online_exam_id = null;
                            $topic_file->assignment_id = $file['assignments'];
                            break;        
                        case "other_link":
                            $topic_file->type = 4;
                            $topic_file->file_url = $file['link'];
                            break;
                    }
                    $topic_file->save();
                }
            }

            //Add the new Files
            if ($request->file) {
                foreach ($request->file as $key => $file) {
                    $topic_file = new File();
                    $topic_file->file_name = $file['name'];
                    $topic_file->modal()->associate($topic);

                    switch ($file['type']) {
                        case "file_upload":
                            $topic_file->type = File::FILE_UPLOAD_TYPE;
                            $topic_file->file_url = $file['file']->store('lessons', 'public');
                            break;
                        case "youtube_link":
                            $topic_file->type = File::YOUTUBE_TYPE;
                            $topic_file->file_url = $file['link'];
                            break;
                        case "video_upload":
                            $topic_file->type = File::VIDEO_UPLOAD_TYPE;
                            $topic_file->file_url = $file['file']->store('lessons', 'public');
                            break;
                        case "external_link":
                            $topic_file->type = File::EXTERNAL_LINK;
                            $topic_file->file_url = $file['external_link'];
                            break;
                        case "video_corner_link":
                            $topic_file->type = File::VIDEO_CORNER_TYPE;
                            $topic_file->file_url = $file['video_corner_url'];
                            $topic_file->download_link = $file['download_link'];
                            break;
                        case "online_exam":
                            $topic_file->type = File::ONLINE_EXAM_TYPE;
                            $topic_file->online_exam_id = $file['online_exam'];
                            break;
                        case "assignment":
                            $topic_file->type = File::ASSIGNMENT_TYPE;
                            $topic_file->assignment_id = $file['assignments'];
                            break;
                        case "other_link":
                            $topic_file->type = 4;
                            $topic_file->file_url = $file['link'];
                            break;
                    }
                    $topic_file->save();
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

    public function destroy($id)
    {
        if (! Auth::user()->can('topic-delete')) {
            return to_route('home')->withErrors([
                'message' => trans('no_permission_message')
            ]);
        }
        try {
            $topic = LessonTopic::relatedToTeacher()->find($id);
            if ($topic->file) {
                foreach ($topic->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }
            }
            $topic->file()->delete();
            $topic->delete();
            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred')
            ];
        }
        return response()->json($response);
    }
}
