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
            'topic' => function ($q) {
                $q->with(['file' => function($q)
                {
                    $q->with(['exam' => function($query){$query->with(['question_choice' => function ($query) { $query->with('questions'); }, 'student_attempt']);},
                    'assignment' => function($q) {
                        $q->with('submission');
                    }
                ]);
                }
                ]);
            },
            'teacher' => function ($q) {
                $q->with('user')->withCount([
                    'lessons' => fn ($q) => $q->active(),
                    'questions',
                    'students'
                ]);
            }
        ]);
        $ids = [];
        $ids = $lesson->topic->pluck('id')->toArray();
        $arr = [];
        foreach($lesson->topic->pluck('file') as $row)
        {
            $row = $row[0];
            $obtainedMarks = 0;
            if(!empty($row->assignment_id))
            {
                if(empty($row->assignment->submission) || !empty($row->assignment->submission) && $row->assignment->submission->status != 1)
                {
                    $arr[] = $row->modal_id;
                }
            }
            if(!empty($row->online_exam_id))
            {
                $exam = $row->exam;
                $totalMarks = $exam->question_choice->sum('marks');
                $row->exam->total_marks = $totalMarks;
                foreach($exam->question_choice as $question)
                {
                    $correctAnswers = $question->questions->answers->pluck('answer');
                    $studentAnswer = !empty($question->questions->student_answer) ? $question->questions->student_answer->pluck('option_id') : [];
                    foreach($correctAnswers as $key => $value)
                    {
                        if(isset($studentAnswer[$key]) && $studentAnswer[$key] == $value)
                        {
                            $obtainedMarks += $question->marks;
                        }
                    }
                }
                $row->exam->obtained_marks = $obtainedMarks;
                if(($row->exam->obtained_marks/$row->exam->total_marks*100) < $row->exam->pass_mark)
                {
                    $row->exam->status = 'failed';
                }
                else
                {
                    $row->exam->status = 'succeed';
                }
                if($row->exam->status != 'succeed')
                {
                    $arr[] = $row->modal_id;
                }
            }
        }
        $result = [];
        if(!empty($arr))
        {
            $first = $arr[0];
            $result = array_filter($ids, function($item) use ($first) {
                return $item > $first;
            });
        }
        return view('student_dashboard.lessons.show', compact('lesson','result'));
    }
}