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
                Enrollment::activeEnrollments()->exists(),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $topics = LessonTopic::active()->where('lesson_id', $lesson->id)->get();
        return view('student_dashboard.topics.index', compact('topics', 'lesson'));
    }

    public function topic_files($topic_id)
    {
        $topic = LessonTopic::with([
            'file' => function ($query) {
                $query->with([
                    'exam' => function ($query) {
                        $query->with([
                            'question_choice' => function ($query) {
                                $query->with('questions');
                            },
                            'student_attempt'
                        ]);
                    },
                    'assignment'
                ]);
            }
        ])->active()->findOrFail($topic_id);
        foreach ($topic->file as $row) {
            $obtainedMarks = 0;
            if (! empty($row->online_exam_id)) {
                $exam = $row->exam;
                $totalMarks = $exam->question_choice->sum('marks');
                $row->exam->total_marks = $totalMarks;
                foreach ($exam->question_choice as $question) {
                    $correctAnswers = $question->questions->answers->pluck('answer');
                    $studentAnswer = ! empty($question->questions->student_answer) ? $question->questions->student_answer->pluck('option_id') : [];
                    foreach ($correctAnswers as $key => $value) {
                        if (isset($studentAnswer[$key]) && $studentAnswer[$key] == $value) {
                            $obtainedMarks += $question->marks;
                        }
                    }
                }
                $row->exam->obtained_marks = $obtainedMarks;
            }
        }
        abort_unless(
            Enrollment::activeEnrollments()->where('lesson_id', $topic->lesson_id)->exists(),
            Response::HTTP_UNAUTHORIZED
        );

        $videos = $topic->file->whereIn('type', [
            File::VIDEO_CORNER_TYPE,
            File::DOWNLOAD_LINK_TYPE,
            File::YOUTUBE_TYPE,
            File::VIDEO_UPLOAD_TYPE,
        ]);
        $exams = $topic->file->where('type', File::ONLINE_EXAM_TYPE);
        $assignments = $topic->file->where('type', File::ASSIGNMENT_TYPE);
        $files = $topic->file->whereIn('type', [File::FILE_UPLOAD_TYPE, File::EXTERNAL_LINK, File::ONLINE_EXAM_TYPE, File::ASSIGNMENT_TYPE]);
        return view('student_dashboard.files.index', compact('files', 'videos', 'topic'));
    }

    public function get_file($id)
    {
        $file = File::with([
            'exam' => function ($query) {
                $query->with([
                    'question_choice' => function ($query) {
                        $query->with('questions');
                    }
                ]);
            },
            'assignment'
        ])->find($id);
        if (! $file)
            return '';
        else {
            return view('student_dashboard.files.file', compact('file'));
        }
    }
    public function get_video($id)
    {
        $video = File::find($id);
        if (! $video)
            return '';
        return view('student_dashboard.files.video', compact('video'));
    }
}
