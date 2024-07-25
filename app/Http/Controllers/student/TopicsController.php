<?php

namespace App\Http\Controllers\student;

use App\Models\File;
use App\Models\Lesson;
use App\Models\LessonTopic;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Students;
use App\Models\SubjectTeacher;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TopicsController extends Controller
{
    public function index()
    {
        dd('test');
    }

    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        abort_unless(
            Enrollment::where('user_id', Auth::user()->id)->where('lesson_id', $id)->exists(),
            Response::HTTP_UNAUTHORIZED
        );

        $topics = LessonTopic::where('lesson_id', $id)->get();

        $lesson_name = $lesson->name;

        return view('student_dashboard.topics.index', compact('topics', 'lesson_name'));
    }

    public function topic_files($topic_id)
    {
        $videos = File::where('modal_type', Lesson::class)->where('modal_id', $topic_id)->get();
        $topic_videos = File::where('modal_type', LessonTopic::class)->get();
        return view('student_dashboard.files.index', compact('videos', 'topic_videos'));
    }


}
