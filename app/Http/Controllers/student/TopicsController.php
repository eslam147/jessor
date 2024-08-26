<?php

namespace App\Http\Controllers\student;

use App\Models\{
    Lesson,
    Enrollment,
    LessonTopic,
    File,
};
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class TopicsController extends Controller
{
    public function index()
    {
        dd('test');
    }

    public function show($id)
    {
        $lesson = Lesson::with('file')->active()->findOrFail($id);
        if ($lesson->is_paid) {
            abort_unless(
                Enrollment::where('user_id', Auth::user()->id)->where('lesson_id', $id)->exists(),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $topics = LessonTopic::active()->where('lesson_id', $lesson->id)->get();
        return view('student_dashboard.topics.index', compact('topics', 'lesson'));
    }

    public function topic_files($topic_id)
    {
        $topic = LessonTopic::with('file')->active()->findOrFail($topic_id);
        abort_unless(
            Enrollment::where('user_id', Auth::user()->id)->where('lesson_id', $topic->lesson_id)->exists(),
            Response::HTTP_UNAUTHORIZED
        );

        $videos = $topic->file->whereIn('type', [
            File::VIDEO_CORNER_TYPE,
            File::DOWNLOAD_LINK_TYPE,
            File::YOUTUBE_TYPE,
            File::VIDEO_UPLOAD_TYPE
        ]);

        $files = $topic->file->whereIn('type', [File::FILE_UPLOAD_TYPE, File::EXTERNAL_LINK]);
        return view('student_dashboard.files.index', compact('files', 'videos','topic'));
    }

    public function get_file($id)
    {
        $file = File::find($id);
        if(!$file) return '';

        return view('student_dashboard.files.file', compact('file'));
    }
    public function get_video($id)
    {
        $video = File::find($id);
        if(!$video) return '';
        return view('student_dashboard.files.video', compact('video'));
    }
}
