<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Http\Controllers\Controller;
class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $lesson->load([
            'studentActiveEnrollment' ,
            'topic',
            'teacher' => function ($q) {
                $q->with('user')->withCount([
                    'lessons' => fn ($q) => $q->active(),
                    'questions',
                    'students'
                ]);
            }
        ]);

        return view('student_dashboard.lessons.show', compact('lesson'));
    }
}