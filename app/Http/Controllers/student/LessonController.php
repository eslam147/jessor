<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Models\OnlineExamStudentAnswer;
use App\Models\OnlineExamQuestionAnswer;
use App\Services\Assigment\AssignmentService;

class LessonController extends Controller
{
    public function __construct(
        private readonly AssignmentService $assignmentService
    ) {
    }
    public function show(Lesson $lesson)
    {
        $student = auth()->user()->load('student')->student;
        $lesson->load([
            'studentActiveEnrollment',
            'topic' => function ($q) {
                $q->with([
                    'file' => function ($q) {
                        $q->with([
                            'exam.student_attempt',
                            'exam.question_choice.questions',
                            'assignment.submission'
                        ]);
                    }
                ]);
            },
            'teacher' => function ($q) {
                $q->with('user')->withCount([
                    'lessons' => fn($q) => $q->active(),
                    'questions',
                    'students'
                ]);
            }
        ]);

        $lockedTopics = [];
        if ($lesson->studentActiveEnrollment) {
            for ($i = 0; $i < $lesson->topic->count(); $i++) {
                $topic = $lesson->topic[$i];
                if ($i == 0) {
                    continue;
                }
                foreach ($topic->file as $file) {
                    $checkAssignmentAccessability = $this->checkAssignmentAccessability($student, $file);
                    $checkExamAccessability = $this->checkExamAccessability($file);
                    if (! $checkAssignmentAccessability || ! $checkExamAccessability) {
                        $lockedTopics[] = $topic->id;
                    }
                }
            }
        }

        return view('student_dashboard.lessons.show', compact('lesson', 'lockedTopics'));
    }
    private function checkAssignmentAccessability($student, $row): bool
    {
        if (! empty($row->assignment_id)) {
            $studentSubmission = $this->assignmentService->getStudentAssignmentSubmission($row->assignment, $student);
            if (empty($studentSubmission) || (! empty($studentSubmission) && ! $studentSubmission->isApproved())) {
                return false;
            }
        }
        return true;
    }
    private function checkExamAccessability($row): bool
    {
        if (! empty($row->online_exam_id)) {
            $exam = $row->exam;
            $examTotalMarks = $exam->question_choice->sum('marks');
            $studentAnswers = OnlineExamStudentAnswer::where([
                'student_id' => auth()->user()->student->id,
                'online_exam_id' => $exam->id,
            ])->with('question')->get();
            $obtainedMarks = 0;

            $correctAnswers = OnlineExamQuestionAnswer::whereIn('question_id', $exam->question_choice->pluck('questions.id'))->get();

            foreach ($exam->question_choice as $question) {
                $studentAnswer = $studentAnswers->where('question_id', $question->id)->first();
                if ($studentAnswer) {
                    if (in_array($studentAnswer->option_id, $correctAnswers->where('question_id', $question->question_id)->pluck('answer')->toArray())) {
                        $obtainedMarks += $question->marks;
                        continue;
                    }
                }
            }

            if ($row->exam->pass_mark > 0) {
                if ($obtainedMarks != 0) {
                    $examDoneMarkAsPercentage = ($obtainedMarks / $examTotalMarks) * 100;
                    if ($examDoneMarkAsPercentage > $row->exam->pass_mark) {
                        return true;
                    }
                }
                return false;
            }
        }
        return true;
    }
}