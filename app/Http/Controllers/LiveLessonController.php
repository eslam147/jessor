<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Subject;

use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Traits\TenantImageTrait;
use App\Enums\Lesson\LiveLessonStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LiveLesson\LiveLessonService;
use App\Http\Requests\Dashboard\LiveLesson\LiveLessonRequest;
use App\Http\Requests\Dashboard\Meeting\MeetingRequest;
use App\Models\LiveLesson;
use App\Models\Settings;

class LiveLessonController extends Controller
{
    use TenantImageTrait;
    public function __construct(
        private readonly LiveLessonService $liveLessonService,

    ) {
        $this->middleware('can:lesson-list')->only(['index']);
        $this->middleware('can:lesson-create')->only(['store']);
    }
    public function index()
    {
        $class_section = ClassSection::SubjectTeacher()->with('class.medium', 'section', 'class.streams')->withOutTrashedRelations('class', 'section')->get();
        $subjects = Subject::SubjectTeacher()->orderBy('id')->get();
        $lessons = Lesson::relatedToTeacher()->withCount('enrollments')->with('file')->select('id', 'name', 'description', 'class_section_id')->get();
        $enabledServices = collect(config('meetings.providers'))->where('is_active', true)->pluck('name')->map(fn($service) => [
            'name' => $service,
            'concat_name' => "video_conference_{$service}"
        ]);

        $services = Settings::whereIn('type', $enabledServices->pluck('concat_name'))->get()->map(function ($item) use ($enabledServices) {
            $content = json_decode($item->message, true) ?? [];
            if (! empty($content['is_enabled']) && $content['is_enabled']) {
                return $enabledServices->firstWhere('concat_name', $item->type)['name'];
            }
        });


        return view('live_lessons.index', compact('class_section', 'services', 'subjects', 'lessons'));
    }


    public function store(LiveLessonRequest $request)
    {
        $this->liveLessonService->create($request);
        return response()->json([
            'error' => false,
            'message' => trans('data_store_successfully')
        ]);
    }

    public function participants(LiveLesson $liveLesson)
    {
        if (! Auth::user()->can('lesson-list')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $particpants = $liveLesson->participants()->with('student')->get();
        // $sql = LiveLesson::relatedToTeacher()->with('subject', 'class_section', 'meetings');
        // if (request()->filled('search')) {
        //     $search = request('search');
        //     $sql->where(function ($query) use ($search) {
        //         $query->where('id', 'LIKE', "%$search%")
        //             ->orwhere('name', 'LIKE', "%$search%")
        //             ->orwhere('description', 'LIKE', "%$search%")
        //             ->orwhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
        //             ->orwhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
        //             ->orWhereHas('class_section.section', function ($q) use ($search) {
        //                 $q->where('name', 'LIKE', "%{$search}%");
        //             })
        //             ->orWhereHas('class_section.class', function ($q) use ($search) {
        //                 $q->where('name', 'LIKE', "%{$search}%");
        //             })->orWhereHas('subject', function ($q) use ($search) {
        //                 $q->where('name', 'LIKE', "%{$search}%");
        //             });
        //     });
        // }
        // if (filled(request('subject_id'))) {
        //     $sql = $sql->where('subject_id', request('subject_id'));
        // }
        // if (request('class_id')) {
        //     $sql = $sql->where('class_section_id', request('class_id'));
        // }

        // $total = $sql->count();

        // $sql->orderBy($sort, $order)->skip($offset)->take($limit);

        // $res = $sql->get();
        // $bulkData = [];
        // $bulkData['total'] = $total;
        // $rows = [];
        // $tempRow = [];
        // $no = 1;
        // foreach ($res as $row) {
        //     $row = (object) $row;

        //     $tempRow['status_name'] = view('live_lessons.datatable.status', ['status' => $row->status])->render();
        //     $tempRow['status'] = $row->status;

        //     $tempRow['id'] = $row->id;
        //     $tempRow['no'] = $no++;

        //     $tempRow['name'] = $row->name;
        //     $tempRow['duration'] = $row->duration;
        //     $tempRow['session_date'] = $row->session_date->format('Y-m-d h:i A');

        //     $tempRow['started_at'] = $row->meeting?->started_at?->format('Y-m-d h:i A') ?? 'Not Started Yet';

        //     $tempRow['duration_readable'] = readableDuration($row->duration);

        //     $tempRow['description'] = str($row->description)->limit(10);

        //     $tempRow['password'] = view('live_lessons.datatable.password', ['password' => $row->password])->render();
        //     $tempRow['class_section_id'] = $row->class_section_id;

        //     $classSection = $row->class_section;

        //     $tempRow['class_section_name'] = $classSection?->class->name . ' ' . $classSection?->section?->name . ' - ' . $classSection?->class->medium->name;
        //     $tempRow['subject_id'] = $row->subject_id;
        //     $tempRow['meeting_url'] = view('live_lessons.datatable.meet_url', ['row' => $row])->render();
        //     $tempRow['subject_name'] = $row->subject->name . ' - ' . $row->subject->type;

        //     $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
        //     $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
        //     $tempRow['operate'] = view('live_lessons.datatable.actions', compact('row'))->render();
        //     $rows[] = $tempRow;
        // }

        // $bulkData['rows'] = $rows;
        return response()->json([
            'error' => false,
            'data' => [
                'students' => $particpants
            ]
        ]);
    }

    public function list()
    {
        if (! Auth::user()->can('lesson-list')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = LiveLesson::relatedToTeacher()->with('subject', 'class_section', 'meetings');
        if (request()->filled('search')) {
            $search = request('search');
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

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);

        $res = $sql->get();
        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $no = 1;
        foreach ($res as $row) {
            $row = (object) $row;

            $tempRow['status_name'] = view('live_lessons.datatable.status', ['status' => $row->status])->render();
            $tempRow['status'] = $row->status;

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;

            $tempRow['name'] = $row->name;
            $tempRow['duration'] = $row->duration;
            $tempRow['session_date'] = $row->session_date->format('Y-m-d h:i A');

            $tempRow['started_at'] = $row->meeting?->started_at?->format('Y-m-d h:i A') ?? 'Not Started Yet';

            $tempRow['duration_readable'] = readableDuration($row->duration);

            $tempRow['description'] = str($row->description)->limit(10);

            $tempRow['password'] = view('live_lessons.datatable.password', ['password' => $row->password])->render();
            $tempRow['class_section_id'] = $row->class_section_id;

            $classSection = $row->class_section;

            $tempRow['class_section_name'] = $classSection?->class->name . ' ' . $classSection?->section?->name . ' - ' . $classSection?->class->medium->name;
            $tempRow['subject_id'] = $row->subject_id;
            $tempRow['meeting_url'] = view('live_lessons.datatable.meet_url', ['row' => $row])->render();
            $tempRow['subject_name'] = $row->subject->name . ' - ' . $row->subject->type;

            $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
            $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
            $tempRow['operate'] = view('live_lessons.datatable.actions', compact('row'))->render();
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update(LiveLessonRequest $request, $id)
    {
        $lesson = LiveLesson::relatedToTeacher()->findOrFail($id);
        $teacher = auth()->user()->load('teacher')->teacher;
        if (! Auth::user()->can('lesson-edit') || $lesson->teacher_id != $teacher->id) {
            return response()->json([
                'error' => true,
                'message' => trans(key: 'no_permission_message')
            ]);
        }
        $this->liveLessonService->update($request, $lesson);

        return response()->json([
            'error' => false,
            'message' => trans('data_store_successfully')
        ]);
    }

    public function destroy($id)
    {
        $liveLesson = LiveLesson::relatedToTeacher()->findOrFail($id);

        if (! Auth::user()->can('lesson-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $liveLesson->delete();
        return response()->json([
            'error' => false,
            'message' => trans('data_delete_successfully')
        ]);
    }

    public function start(LiveLesson $liveLesson)
    {
        // if (! Auth::user()->can('live-lesson-start')) {
        //     return response()->json([
        //         'error' => true,
        //         'message' => trans('no_permission_message')
        //     ]);
        // }
        if ($liveLesson->status->value == LiveLessonStatus::DEFAULT ) {
            // dd(
            //     $liveLesson
            // );
            $liveLesson->update([
                'status' => LiveLessonStatus::STARTED,
                'started_at' => now(),
            ]);
            $liveLesson->meeting()->started();
            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully'),
                'data' => [
                    'status' => $liveLesson->status->isStarted(),
                    'meeting_url' => $liveLesson->start_url
                ]
            ]);
        }
    }

    public function stop(Request $request, LiveLesson $liveLesson)
    {
        #TODO : Check if user can stop live lesson

        // if (! Auth::user()->can('live-lesson-start')) {
        //     // return response()->json([
        //     //     'error' => true,
        //     //     'message' => trans('no_permission_message')
        //     // ]);
        // }

        if ($liveLesson->status->value == LiveLessonStatus::STARTED->value) {
            $liveLesson->update([
                'status' => LiveLessonStatus::FINISHED,
                'notes' => $request->notes,
                'finished_at' => now()
            ]);
            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully'),
            ]);
        }
        return response()->json([
            'error' => true,
            'message' => trans('error_occurred'),
        ]);


    }
    public function scheduleMeeting(MeetingRequest $request, LiveLesson $liveLesson)
    {
        $this->liveLessonService->createMeeting($liveLesson, $request->service);
        return response()->json([
            'error' => false,
            'message' => trans('data_store_successfully'),
        ]);
    }
}