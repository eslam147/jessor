<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Models\LessonTopic;
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
        $lesson = Lesson::with('file')->findOrFail($id);

        abort_unless(
            Enrollment::where('user_id', Auth::user()->id)->where('lesson_id', $id)->exists(),
            Response::HTTP_UNAUTHORIZED
        );

        $topics = LessonTopic::where('lesson_id', $id)->get();

        $lesson_name = $lesson->name;

        return view('student_dashboard.topics.index', compact('topics','lesson', 'lesson_name'));
    }

    public function topic_files($topic_id)
    {
        $topic = LessonTopic::with('file')->findOrFail($topic_id);
        return view('student_dashboard.files.index', compact('topic'));
    }


}