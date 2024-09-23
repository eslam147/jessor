<?php

namespace App\Http\Controllers\student;

use Exception;
use App\Models\OnlineExam;
use Illuminate\Http\Request;
use App\Services\Exam\ExamService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OnlineExamStudentAnswer;
use App\Models\StudentOnlineExamStatus;
use App\Models\OnlineExamQuestionAnswer;
use App\Models\OnlineExamQuestionChoice;
use App\Models\OnlineExamQuestionOption;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL as FacadeURL;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamService $examService
    ) {}
    public function index()
    {
        // $examTerms = $this->examService->getExamTerms();
        $exams = $this->examService->getOnlineExamList();
        return view('student_dashboard.exams.index', compact('exams'));
    }
    public function result(Request $request, OnlineExam $exam)
    {
        $studentId = $request->user()->student->id;
        $examStatus = $this->examService->getOnlineExamStatus($studentId, $exam->id);
        
        if (! $examStatus) {
            abort(404);
        }
        // Get total questions count
        $examQuestions = OnlineExam::where('id',$exam->id)->with(['questions','answers','student_answer','student_attempt'])->withCount('questions')->first();
        $totalQuestions = $examQuestions->questions_count;        
        $totalMarks = $examQuestions->total_mark;
        $obtainedMarks = $examQuestions->grade;
        $correctAnswersCount = 0;
        $questions = $examQuestions->questions;
        $incorrectAnswersData = [];
        $studentAnswers = collect($examQuestions->student_answer); // الإجابات الخاصة بالطالب
        $correctAnswers = collect($examQuestions->answers);
        foreach ($questions as $question) {
            $studentSelectedOptions = $studentAnswers->where('question_choice_id', $question['id'])->pluck('option_id'); // البحث عن إجابة الطالب للسؤال الحالي
            $correctAnswersForQuestion = $correctAnswers->where('question_id', $question['id'])->pluck('answer');
            if ($studentSelectedOptions->isNotEmpty()) {
                if ($studentSelectedOptions->sort()->values()->all() === $correctAnswersForQuestion->sort()->values()->all()) 
                {
                    $correctAnswersCount += 1;
                    $question->is_correct = true;
                }
                else
                {
                    $question->is_correct = false;
                }
                $question->student_answers = $studentSelectedOptions->sort()->values()->all();
            } else {
                $question->is_correct = false;
                $question->student_answers = [];
            }
            $question->correct_answers = $correctAnswersForQuestion->sort()->values()->all();
        }
        $examQuestions->questions = $questions;
        // Get total marks
        $examResult = (object) [
            'total_questions' => $totalQuestions,
            'correct_answers_count' =>$correctAnswersCount,
            'in_correct_answers' => [
                'total_questions' => $totalQuestions - $correctAnswersCount,
                'question_data' => $incorrectAnswersData,
            ],
            'total_obtained_marks' => $examQuestions->grade,
            'total_marks' => $examQuestions->total_mark,
            'grade' => $examQuestions->highest_degree,
            // 'grade' => '$this->examService->getGrade($obtainedMarks, $totalMarks)',
            'examQuestions' => $examQuestions->questions,
            'exam' => $examQuestions,
        ];
        return view('student_dashboard.exams.result', compact('examResult'));
    }
    public function submit(Request $request, OnlineExam $exam)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'answers_data' => 'required|array',
            'answers_data.*.question_id' => 'numeric',
            'answers_data.*.option_id' => 'array',
            'answers_data.*.option_id.*' => 'numeric',
        ]);

        if ($validator->fails()) {
            Alert::error('Validation Error', $validator->errors()->first());
            return redirect()->back();
        }
        DB::beginTransaction();

        try {
            $student = $request->user()->student;

            // Update the student exam status
            $studentExamStatus = StudentOnlineExamStatus::where([
                'student_id' => $student->id,
                'online_exam_id' => $exam->id,
            ])->firstOrFail();

            if ($studentExamStatus->status == 2) {
                Alert::error('Error', 'Exam already submitted');
                DB::rollBack();
                return redirect()->back();
            }

            $answers_exists = OnlineExamStudentAnswer::where([
                'student_id' => $student->id,
                'online_exam_id' => $exam->id
            ])->exists();

            if ($answers_exists) {
                Alert::error('Error', 'Answers already submitted');
                return redirect()->back();
            }

            foreach ($request->answers_data as $key => $answer_data) {
                $check_question_exists = OnlineExamQuestionChoice::where([
                    'question_id' => $answer_data['question_id'],
                ])->exists();

                if (! $check_question_exists) {
                    continue;
                }
                if (! empty($answer_data['question_id'])) {

                    $question = OnlineExamQuestionChoice::where([
                        'question_id' => $answer_data['question_id'],
                        'online_exam_id' => $exam->id
                    ])->first();

                    // Check if the option exists
                    if (! empty($answer_data['option_id'])) {

                        $check_option_exists = OnlineExamQuestionOption::where([
                            'id' => $answer_data['option_id'],
                            'question_id' => $question->question_id
                        ])->exists();

                        if ($check_option_exists) {

                            foreach ($answer_data['option_id'] as $option) {
                                // Store the answer
                                OnlineExamStudentAnswer::create([
                                    'student_id' => $student->id,
                                    'online_exam_id' => $exam->id,
                                    'question_id' => $question->id,
                                    'option_id' => $option,
                                    'submitted_date' => now()->toDateString(),
                                ]);
                            }

                            if ($studentExamStatus) {
                                $studentExamStatus->update([
                                    'status' => 2
                                ]);
                            }
                        } else {
                            continue;
                        }
                    }
                }
                // Get the question ID from the question choice
            }

            DB::commit();
            // Success message
            Alert::success('Success', 'Exam Submitted successfully');
            return to_route('student_dashboard.exams.online.result', $exam->id);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            // throw $e;
            Alert::error('Error', 'An error occurred');
            return redirect()->back();
        }
    }
    public function start(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:online_exams,id',
            'exam_key' => 'nullable|string',
        ]);

        $exam = OnlineExam::whereId($request->exam_id)->firstOrFail();

        if (! empty($exam->exam_key) && $exam->exam_key != $request->exam_key) {
            Alert::error(trans('invalid_exam_key'), trans('invalid_exam_key'));
            return redirect()->back();
        }

        $student = $request->user()->student;

        // checks student exam status
        $studentExamStatus = $this->examService->getOnlineExamStatus(
            $student->id,
            $exam->id
        );

        // -------------------------------------------------------------- \\
        if ($studentExamStatus && $studentExamStatus->status == StudentOnlineExamStatus::COMPLETED) {
            Alert::error(trans('student_already_attempted_exam'), trans('student_already_attempted_exam'));
            return redirect()->back();
        }

        // -------------------------------------------------------------- \\
        if ($exam->start_date->isFuture()) {
            Alert::error(trans('exam_not_started_yet'), trans('exam_not_started_yet'));
            return redirect()->back();
        }

        // add the exam status
        // -------------------------------------------------------------- \\
        $examStatus = $this->examService->createOnlineExamStatus($student->id, $exam->id);
        // -------------------------------------------------------------- \\
        $duration = $this->examService->calculateRemainingMinutes($examStatus, $exam);
        // -------------------------------------------------------------- \\
        $generateTempUrl = FacadeURL::temporarySignedRoute(
            name: 'student_dashboard.exams.online.show',
            expiration: now()->addMinutes($duration),
            parameters: [
                'exam' => $exam->id,
            ],
            absolute: false
        );
        // -------------------------------------------------------------- \\
        return redirect($generateTempUrl);
    }
    public function show(Request $request, $id)
    {
        $exam = OnlineExam::whereId($request->exam)->firstOrFail();
        $student = $request->user()->student;

        $examStatus = $this->examService->getOnlineExamStatus(
            $student->id,
            $exam->id
        );

        if (! $examStatus) {
            Alert::error(__('unauthorized_access'));
            return to_route('student_dashboard.exams.online.index');
        }
        if ($examStatus->status == StudentOnlineExamStatus::COMPLETED) {
            Alert::error(__('student_already_attempted_exam'));
            return redirect()->back();
        }

        if ($exam->start_date->isFuture()) {
            Alert::error(trans('exam_not_started_yet'));
            return redirect()->back();
        }

        $duration = $this->examService->calculateRemainingMinutes(
            $examStatus,
            $exam
        );
        $examEndTime = now()->addMinutes($duration);
        if ($duration <= 0) {
            Alert::error('error', __('exam_expired'));
            return redirect()->route('student_dashboard.exams.online.index');
        }
        $questions_data = $this->examService->getOnlineExamQuestions($exam);
        return view('student_dashboard.exams.show', compact('duration', 'examEndTime', 'questions_data'));
    }
}
