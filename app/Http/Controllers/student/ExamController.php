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
use Illuminate\Support\Facades\URL as FacadesURL;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamService $examService
    ) {

    }
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
        $examQuestions = OnlineExamQuestionChoice::where('online_exam_id', $exam->id)->with('questions')->get();
        $totalQuestions = $examQuestions->count();

        // Get student's answers
        $studentAnswers = OnlineExamStudentAnswer::where([
            'student_id' => $studentId,
            'online_exam_id' => $exam->id,
        ])->with('question')->get();

        // Get correct answers and marks
        $correctAnswersCount = 0;
        $incorrectAnswersData = [];
        $obtainedMarks = 0;
        $totalMarks = $examQuestions->sum('marks');

        $correctAnswers = OnlineExamQuestionAnswer::whereIn('question_id', $examQuestions->pluck('questions.id'))->get();
        $examQuestions = collect($examQuestions);
        foreach ($examQuestions as $question) {
            $studentAnswer = $studentAnswers->where('question_id', $question->id)->first();

            $questionChoiceAnswers = $question->questions->answers->pluck('options.id')->toArray();

            $isAnswerCorrect = null;

            if ($studentAnswer) {
                // $isAnswerCorrect =  ;
                if (in_array($studentAnswer->option_id, $correctAnswers->where('question_id', $question->question_id)->pluck('answer')->toArray())) {
                    $obtainedMarks += $question->marks;
                    $correctAnswersCount += 1; 
                    $question->is_correct = true;
                    $question->student_answer = $studentAnswer->option_id;
                    $question->correct_answers = $questionChoiceAnswers;
                    continue;
                }
            }

            $question->is_correct = false;
            $question->correct_answers = $questionChoiceAnswers;
            $question->student_answer = $studentAnswer->option_id ?? null;
        }

        // Get total marks
        $examResult = (object) [
            'total_questions' => $totalQuestions,
            'correct_answers_count' =>$correctAnswersCount,
            'in_correct_answers' => [
                'total_questions' => count($incorrectAnswersData),
                'question_data' => $incorrectAnswersData,
            ],
            'total_obtained_marks' => $obtainedMarks,
            'total_marks' => $totalMarks,
            'grade' => '',
            // 'grade' => '$this->examService->getGrade($obtainedMarks, $totalMarks)',
            'examQuestions' => $examQuestions,
            'exam' => $exam,
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

        // dd('Check asd');
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
                                // Store the answers

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
            Alert::success('Success', 'Data stored successfully');
            return to_route('student_dashboard.exams.online.index');

        } catch (\Exception $e) {
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
            'exam_key' => 'required'
        ]);

        $exam = OnlineExam::where([
            'id' => $request->exam_id,
            'exam_key' => $request->exam_key
        ])->first();
        $student = $request->user()->student;
        $time_data = now()->toArray()['formatted'];
        if (! $exam) {
            Alert::error(trans('invalid_exam_key'), trans('invalid_exam_key'));
            return redirect()->back();
        }
        // checks student exam status
        $check_student_status = $this->examService->getOnlineExamStatus(
            $student->id,
            $exam->id
        );

        if ($check_student_status && $check_student_status->status == 2) {
            Alert::error(trans('student_already_attempted_exam'), trans('student_already_attempted_exam'));
            return redirect()->back();
        }

        if ($exam->where('start_date', '>', $time_data)) {
            Alert::error(trans('exam_not_started_yet'), trans('exam_not_started_yet'));
            return redirect()->back();
        }

        // add the exam status
        $examStatus = $this->examService->createOnlineExamStatus($student->id, $request->exam_id);
        // -------------------------------------------------------------- \\
        $duration = $this->examService->getRemainingMinutes($examStatus, $exam);
        // -------------------------------------------------------------- \\
        $generateTempUrl = FacadesURL::temporarySignedRoute('student_dashboard.exams.online.show', $examStatus->created_at->addMinutes($duration), [
            'exam' => $request->exam_id,
        ]);
        // -------------------------------------------------------------- \\
        return redirect($generateTempUrl);
    }
    public function show(Request $request)
    {
        $exam = OnlineExam::where('id', $request->exam)->firstOrFail();
        $student = $request->user()->student;

        // checks student exam status
        $check_student_status = $this->examService->getOnlineExamStatus(
            $student->id,
            $exam->id
        );

        if (! $check_student_status) {
            Alert::error(__('unauthorized_access'));
            return to_route('student_dashboard.exams.online.index');
        }
        if ($check_student_status->status == 2) {
            Alert::error(__('student_already_attempted_exam'));
            return redirect()->back();
        }

        // //checks the exam started or not
        $time_data = now()->toArray();
        $current_date_time = $time_data['formatted'];

        $check_start_date = OnlineExam::whereId($request->exam)
            ->where('start_date', '>', $current_date_time)
            ->first();
        if ($check_start_date) {
            // dd('asdasd2345',$check_start_date,$current_date_time);
            Alert::error(trans('exam_not_started_yet'));
            return redirect()->back();
        }


        $duration = $this->examService->getRemainingMinutes(
            $check_student_status,
            $exam
        );
        $examEndTime = now()->addMinutes($duration);
        if ($duration <= 0) {
            Alert::error('error', __('exam_expired'));
            return redirect()->route('student_dashboard.exams.online.index');
        }
        $questions_data = $this->examService->getOnlineExamQuestions($request);

        return view('student_dashboard.exams.show', compact('duration', 'examEndTime', 'questions_data'));
    }


}

/*
  try {
        $student = $request->user()->student;
        $class_section_id = $student->class_section_id;
        $class_id = $student->class_section->class_id;

        $session_year_id = getSettings('session_year')['session_year'];
        $current_date_time = Carbon::now();

        $exam_query = OnlineExam::where([
                ['model_type', '=', 'App\Models\ClassSection'],
                ['model_id', '=', $class_section_id],
                ['subject_id', '=', $request->subject_id],
                ['session_year_id', '=', $session_year_id],
                ['start_date', '<=', $current_date_time]
            ])->orWhere(function ($query) use ($class_id, $session_year_id, $request, $current_date_time) {
                $query->where([
                    ['model_type', '=', 'App\Models\ClassSchool'],
                    ['model_id', '=', $class_id],
                    ['subject_id', '=', $request->subject_id],
                    ['session_year_id', '=', $session_year_id],
                    ['start_date', '<=', $current_date_time]
                ]);
            });

        $exam_exists = $exam_query->exists();
        $exam_query_without_session_year = OnlineExam::where([
                ['model_type', '=', 'App\Models\ClassSection'],
                ['model_id', '=', $class_section_id],
                ['subject_id', '=', $request->subject_id],
                ['start_date', '<=', $current_date_time]
            ])->orWhere(function ($query) use ($class_id, $request, $current_date_time) {
                $query->where([
                    ['model_type', '=', 'App\Models\ClassSchool'],
                    ['model_id', '=', $class_id],
                    ['subject_id', '=', $request->subject_id],
                    ['start_date', '<=', $current_date_time]
                ]);
            });

        if ($exam_exists) {
            $total_exam_ids = $exam_query->pluck('id');
            $attempted_online_exam_ids = StudentOnlineExamStatus::where('student_id', $student->id)
                ->whereIn('online_exam_id', $total_exam_ids)
                ->pluck('online_exam_id');

            $online_exams_attempted_answers = OnlineExamStudentAnswer::where('student_id', $student->id)
                ->whereIn('online_exam_id', $total_exam_ids)
                ->pluck('option_id');

            $online_exams_submitted_question_ids = OnlineExamStudentAnswer::where('student_id', $student->id)
                ->whereIn('online_exam_id', $total_exam_ids)
                ->pluck('question_id');

            $get_question_ids = OnlineExamQuestionChoice::whereIn('id', $online_exams_submitted_question_ids)
                ->pluck('question_id');

            foreach ($get_question_ids as $question_id) {
                $wrong_answers_exist = OnlineExamQuestionAnswer::where('question_id', $question_id)
                    ->whereNotIn('answer', $online_exams_attempted_answers)
                    ->exists();

                if ($wrong_answers_exist) {
                    $get_question_ids = $get_question_ids->reject(fn($id) => $id == $question_id);
                }
            }

            $correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $get_question_ids)
                ->whereIn('answer', $online_exams_attempted_answers)
                ->pluck('question_id');

            $total_exams = $exam_query_without_session_year->count();
            $total_attempted_exams = StudentOnlineExamStatus::where('student_id', $student->id)
                ->whereIn('online_exam_id', $total_exam_ids)
                ->count();

            $total_missed_exams = $exam_query_without_session_year->whereNotIn('id', $attempted_online_exam_ids)
                ->count();

            $total_obtained_marks = OnlineExamQuestionChoice::whereIn('online_exam_id', $total_exam_ids)
                ->whereIn('question_id', $correct_answers_question_id)
                ->sum('marks');

            $total_marks = OnlineExamQuestionChoice::whereIn('online_exam_id', $total_exam_ids)
                ->sum('marks');

            $percentage = $total_marks ? number_format(($total_obtained_marks * 100) / $total_marks, 2) : 0;

            $online_exams_db = $exam_query->with(['student_attempt' => function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                }])
                ->has('question_choice')
                ->paginate(10);

            $exam_list = $online_exams_db->map(function ($data) use ($student) {
                $exam_submitted_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data->id])
                    ->pluck('question_id');

                $get_exam_question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_submitted_question_ids)
                    ->pluck('question_id');

                $exam_attempted_answers = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data->id])
                    ->pluck('option_id');

                foreach ($get_exam_question_ids as $question_id) {
                    $wrong_answers_exist = OnlineExamQuestionAnswer::where('question_id', $question_id)
                        ->whereNotIn('answer', $exam_attempted_answers)
                        ->exists();

                    if ($wrong_answers_exist) {
                        $get_exam_question_ids = $get_exam_question_ids->reject(fn($id) => $id == $question_id);
                    }
                }

                $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $get_exam_question_ids)
                    ->whereIn('answer', $exam_attempted_answers)
                    ->pluck('question_id');

                $total_obtained_marks_exam = OnlineExamQuestionChoice::where('online_exam_id', $data->id)
                    ->whereIn('question_id', $exam_correct_answers_question_id)
                    ->sum('marks');

                $total_marks_exam = OnlineExamQuestionChoice::where('online_exam_id', $data->id)
                    ->sum('marks');

                return [
                    'online_exam_id' => $data->id,
                    'title' => $data->title,
                    'obtained_marks' => $total_obtained_marks_exam ?? "0",
                    'total_marks' => $total_marks_exam ?? "0",
                ];
            })->toArray();

            $online_exam_report_data = [
                'total_exams' => $total_exams,
                'attempted' => $total_attempted_exams,
                'missed_exams' => $total_missed_exams,
                'total_marks' => $total_marks ?? "0",
                'total_obtained_marks' => $total_obtained_marks ?? "0",
                'percentage' => $percentage ?? "0",
                'exam_list' => $exam_list,
                'pagination' => $online_exams_db->toArray()
            ];
        }

        return view('exam_report', compact('online_exam_report_data'));
    } catch (Exception $e) {
        report($e);
        alert()->error('Error', trans('error_occurred'));
        return redirect()->back();
    }

    public function getExamList(Request $request)
    {
        try {

            $student_id = Auth::user()->student->id;
            $student = Students::with('class_section')->where('id', $student_id)->first();
            $student_subject = $student->subjects();
            $class_id = $student->class_section->class_id;

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id') ?? [];
            // dd($core_subjects);
            $elective_subjects = $student_subject["elective_subject"] ?? [];
            // dd($elective_subjects);
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }

            $subject_id = array_merge($core_subjects, $elective_subjects);

            $exam_data_db = ExamClass::with('exam.session_year:id,name', 'exam.timetable')
                ->where('class_id', $class_id)
                ->whereHas('exam.timetable', function ($query) use ($subject_id) {
                    $query->whereIn('subject_id', $subject_id);
                })->get();

            foreach ($exam_data_db as $data) {
                // date status
                $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where(['exam_id' => $data->exam->id, 'class_id' => $class_id])->first();
                $starting_date = $starting_date_db['min(date)'];
                $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where(['exam_id' => $data->exam->id, 'class_id' => $class_id])->first();
                $ending_date = $ending_date_db['max(date)'];
                $currentTime = Carbon::now();
                $current_date = date($currentTime->toDateString());
                if ($current_date >= $starting_date && $current_date <= $ending_date) {
                    $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } elseif ($current_date < $starting_date) {
                    $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } else {
                    $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
                }

                if (isset($request->status)) {
                    if ($request->status == 0) {
                        $exam_data[] = array(
                            'id' => $data->exam->id,
                            'name' => $data->exam->name,
                            'description' => $data->exam->description,
                            'publish' => $data->exam->publish,
                            'session_year' => $data->exam->session_year->name,
                            'exam_starting_date' => $starting_date,
                            'exam_ending_date' => $ending_date,
                            'exam_status' => $exam_status,
                        );
                    } else if ($request->status == 1) {
                        if ($exam_status == 0) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->exam->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                            );
                        }
                    } else if ($request->status == 2) {
                        if ($exam_status == 1) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->exam->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                            );
                        }
                    } else {
                        if ($exam_status == 2) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->exam->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                            );
                        }
                    }
                } else {
                    $exam_data[] = array(
                        'id' => $data->exam->id,
                        'name' => $data->exam->name,
                        'description' => $data->exam->description,
                        'publish' => $data->exam->publish,
                        'session_year' => $data->exam->session_year->name,
                        'exam_starting_date' => $starting_date,
                        'exam_ending_date' => $ending_date,
                        'exam_status' => $exam_status,
                    );
                }
            }

            $response = array(
                'error' => false,
                'data' => isset($exam_data) ? $exam_data : [],
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getExamDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|nullable',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student_id = Auth::user()->student->id;
            $student = Students::with('class_section')->where('id', $student_id)->first();
            $student_subject = $student->subjects();
            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] == null ? [] : $student_subject["elective_subject"]->pluck('subject_id')->toArray();

            $subject_id = array_merge($core_subjects, $elective_subjects);

            $class_id = $student->class_section->class_id;
            $exam_data_db = Exam::with([
                'timetable' => function ($q) use ($request, $class_id, $subject_id) {
                    $q->where(['exam_id' => $request->exam_id, 'class_id' => $class_id])->whereIn('subject_id', $subject_id)->with(['subject'])->orderby('date');
                }
            ])->where('id', $request->exam_id)->first();


            if (! $exam_data_db) {
                $response = array(
                    'error' => false,
                    'data' => [],
                    'code' => 200,
                );
                return response()->json($response);
            }


            foreach ($exam_data_db->timetable as $data) {
                $exam_data[] = array(
                    'id' => $data->id,
                    'total_marks' => $data->total_marks,
                    'passing_marks' => $data->passing_marks,
                    'date' => $data->date,
                    'starting_time' => $data->start_time,
                    'ending_time' => $data->end_time,
                    'subject' => array(
                        'id' => $data->subject->id,
                        'name' => $data->subject->name,
                        'bg_color' => $data->subject->bg_color,
                        'image' => $data->subject->image,
                        'type' => $data->subject->type,
                    )
                );
            }
            $response = array(
                'error' => false,
                'data' => isset($exam_data) ? $exam_data : [],
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getExamMarks(Request $request)
    {
        try {
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $student = $request->user()->student;
            $class_data = Students::where('id', $student->id)->with('class_section.class.medium', 'class_section.section')->get()->first();

            $exam_result_db = ExamResult::with([
                'student' => function ($q) {
                    $q->select('id', 'user_id', 'roll_number')->with('user:id,first_name,last_name');
                }
            ])->with('exam', 'session_year')->with([
                        'exam.marks' => function ($q) use ($student) {
                            $q->where('student_id', $student->id);
                        }
                    ])->where('student_id', $student->id)->get();



            if (sizeof($exam_result_db)) {
                foreach ($exam_result_db as $exam_result_data) {
                    $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where(['exam_id' => $exam_result_data->exam_id, 'class_id' => $class_data->class_section->class_id, 'session_year_id' => $session_year_id])->first();
                    $starting_date = $starting_date_db['min(date)'];

                    $exam_result = array(
                        'result_id' => $exam_result_data->id,
                        'exam_id' => $exam_result_data->exam_id,
                        'exam_name' => $exam_result_data->exam->name,
                        'class_name' => $class_data->class_section->class->name . '-' . $class_data->class_section->section->name . ' ' . $class_data->class_section->class->medium->name,
                        'student_name' => $exam_result_data->student->user->first_name . ' ' . $exam_result_data->student->user->last_name,
                        'exam_date' => $starting_date,
                        'total_marks' => $exam_result_data->total_marks,
                        'obtained_marks' => $exam_result_data->obtained_marks,
                        'percentage' => $exam_result_data->percentage,
                        'grade' => $exam_result_data->grade,
                        'session_year' => $exam_result_data->session_year->name,
                    );
                    $exam_marks = array();
                    foreach ($exam_result_data->exam->marks as $marks) {
                        $exam_marks[] = array(
                            'marks_id' => $marks->id,
                            'subject_name' => $marks->subject->name,
                            'subject_type' => $marks->subject->type,
                            'total_marks' => $marks->timetable->total_marks,
                            'passing_marks' => $marks->timetable->passing_marks,
                            'obtained_marks' => $marks->obtained_marks,
                            'teacher_review' => $marks->teacher_review,
                            'grade' => $marks->grade,
                        );
                    }
                    $data[] = array(
                        'result' => $exam_result,
                        'exam_marks' => $exam_marks,
                    );
                }

                $response = array(
                    'error' => false,
                    'message' => "Exam Result Fetched Successfully",
                    'data' => $data,
                    'code' => 200,
                );
            } else {
                $response = array(
                    'error' => false,
                    'message' => "Exam Result Fetched Successfully",
                    'data' => [],
                    'code' => 200,
                );
            }
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $date = Carbon::now()->setTimezone('UTC');
            $student = $request->user()->student;

            $student_subject = $student->subjects();
            $class_subject = $student->classSubjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }

            $subject_id = array_merge($core_subjects, $elective_subjects);

            $class_section_id = $student->class_section->id;
            $class_id = $student->class_section->class_id;
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            //get current
            $time_data = Carbon::now()->toArray();
            $current_date_time = $time_data['formatted'];

            // checks the subject id param is passed or not .
            // query meets the condition for both class section and class
            if (isset($request->subject_id) && ! empty($request->subject_id)) {
                $exam_data_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->has('question_choice')->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $session_year_id, $current_date_time, $student, $request) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->orderby('start_date')->paginate(15)->toArray();
            } else {
                $exam_data_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->whereIn('subject_id', $subject_id)->has('question_choice')->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $subject_id, $session_year_id, $current_date_time, $student) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->whereIn('subject_id', $subject_id)->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->orderby('start_date')->paginate(15)->toArray();
            }

            if (isset($exam_data_db) && ! empty($exam_data_db)) {

                $exam_data = array();
                $exam_list = array();
                // making the array of exam data
                foreach ($exam_data_db['data'] as $data) {

                    // total marks of exams
                    $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                    $total_marks = $total_marks['sum(marks)'];

                    if ($data['model_type'] == 'App\Models\ClassSection') {
                        $class_section_data = ClassSection::where('id', $data['model_id'])->with('class.medium', 'section')->first();
                        $class_name = $class_section_data->class->name . ' - ' . $class_section_data->section->name . ' ' . $class_section_data->class->medium->name;
                    } else {
                        $class_data = ClassSchool::where('id', $data['model_id'])->with('medium')->first();
                        $class_name = $class_data->name . ' ' . $class_data->medium->name;
                    }

                    if ($total_marks == null) {
                        $exam_list = [];
                    } else {
                        $exam_list[] = array(
                            'exam_id' => $data['id'],
                            'class' => array(
                                'id' => $data['model_id'],
                                'name' => $class_name
                            ),
                            'subject' => array(
                                'id' => $data['subject_id'],
                                'name' => $data['subject']['name'] . ' - ' . $data['subject']['type']
                            ),
                            'title' => $data['title'],
                            'exam_key' => $data['exam_key'],
                            'duration' => $data['duration'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'total_marks' => $total_marks,
                        );
                    }

                }

                //adding the exam data with pagination data
                $exam_data = array(
                    'current_page' => $exam_data_db['current_page'],
                    'data' => $exam_list,
                    'current_date' => $date,
                    'from' => $exam_data_db['from'],
                    'last_page' => $exam_data_db['last_page'],
                    'per_page' => $exam_data_db['per_page'],
                    'to' => $exam_data_db['to'],
                    'total' => $exam_data_db['total'],
                );
            } else {
                //if no data found
                $exam_data = null;
            }

            $response = array(
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $exam_data,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamQuestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required',
            'exam_key' => 'required',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;

            // checks Exam key
            $check_key = OnlineExam::where(['id' => $request->exam_id, 'exam_key' => $request->exam_key])->count();
            if ($check_key == 0) {
                $response = array(
                    'error' => true,
                    'message' => trans('invalid_exam_key'),
                    'code' => 103
                );
                return response()->json($response);
            }

            // checks student exam status
            $check_student_status = StudentOnlineExamStatus::where(['online_exam_id' => $request->exam_id, 'student_id' => $student->id])->count();
            if ($check_student_status != 0) {
                $response = array(
                    'error' => true,
                    'message' => trans('student_already_attempted_exam'),
                    'code' => 105
                );
                return response()->json($response);
            }

            //checks the exam started or not
            $time_data = Carbon::now()->toArray();
            $current_date_time = $time_data['formatted'];
            $check_start_date = OnlineExam::where('id', $request->exam_id)->where('start_date', '>', $current_date_time)->count();
            if ($check_start_date != 0) {
                $response = array(
                    'error' => true,
                    'message' => trans('exam_not_started_yet'),
                    'code' => 106,
                );
                return response()->json($response);
            }

            // add the exam status
            $student_exam_status = new StudentOnlineExamStatus();
            $student_exam_status->online_exam_id = $request->exam_id;
            $student_exam_status->student_id = $student->id;
            $student_exam_status->status = 1;
            $student_exam_status->save();

            // get total questions
            $total_questions = OnlineExamQuestionChoice::where('online_exam_id', $request->exam_id)->count();

            // get the questions data
            $get_exam_questions_db = OnlineExamQuestionChoice::where('online_exam_id', $request->exam_id)->with('questions')->get();
            $questions_data = array();
            $total_marks = 0;
            foreach ($get_exam_questions_db as $exam_questions) {
                $total_marks += $exam_questions->marks;

                // make options array
                $options_data = array();
                foreach ($exam_questions->questions->options as $question_options) {
                    $options_data[] = array(
                        'id' => $question_options->id,
                        'option' => htmlspecialchars_decode($question_options->option)
                    );
                }

                // make answers array
                $answers_data = array();
                foreach ($exam_questions->questions->answers as $question_answers) {
                    $answers_data[] = array(
                        'id' => $question_answers->id,
                        'option_id' => $question_answers->answer,
                        'answer' => htmlspecialchars_decode($question_answers->options->option)
                    );
                }

                // make question array
                $questions_data[] = array(
                    'id' => $exam_questions->id,
                    'question' => htmlspecialchars_decode($exam_questions->questions->question),
                    'question_type' => $exam_questions->questions->question_type,
                    'options' => $options_data,
                    'answers' => $answers_data,
                    'marks' => $exam_questions->marks,
                    'image' => $exam_questions->questions->image_url,
                    'note' => $exam_questions->questions->note,
                );
            }
            $response = array(
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $questions_data ?? null,
                'total_questions' => $total_questions,
                'total_marks' => $total_marks,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function submitOnlineExamAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'online_exam_id' => 'required|numeric',
            'answers_data' => 'required|array',
            'answers_data.*.question_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;

            // checks the online exam exists
            $check_online_exam_id = OnlineExam::where('id', $request->online_exam_id)->count();
            if ($check_online_exam_id) {

                $answers_exists = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->count();
                if ($answers_exists) {
                    $response = array(
                        'error' => true,
                        'message' => 'Answers already submitted',
                        'code' => 103,
                    );
                    return response()->json($response);
                }

                foreach ($request->answers_data as $answer_data) {

                    // checks the question exists with provided exam id
                    $check_question_exists = OnlineExamQuestionChoice::where(['id' => $answer_data['question_id']])->count();
                    if ($check_question_exists) {

                        // get the question id from question choiced
                        $question_id = OnlineExamQuestionChoice::where(['id' => $answer_data['question_id'], 'online_exam_id' => $request->online_exam_id])->pluck('question_id')->first();

                        // checks the option exists with provided question
                        $check_option_exists = OnlineExamQuestionOption::where(['id' => $answer_data['option_id'], 'question_id' => $question_id])->count();

                        //get the current date
                        $currentTime = Carbon::now();
                        $current_date = date($currentTime->toDateString());

                        if ($check_option_exists) {
                            foreach ($answer_data['option_id'] as $options) {
                                // add the data of answers
                                $store_answers = new OnlineExamStudentAnswer();
                                $store_answers->student_id = $student->id;
                                $store_answers->online_exam_id = $request->online_exam_id;
                                $store_answers->question_id = $answer_data['question_id'];
                                $store_answers->option_id = $options;
                                $store_answers->submitted_date = $current_date;
                                $store_answers->save();
                            }

                            $student_exam_status_id = StudentOnlineExamStatus::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->pluck('id')->first();
                            if (isset($student_exam_status_id) && ! empty($student_exam_status_id)) {
                                $update_status = StudentOnlineExamStatus::find($student_exam_status_id);
                                $update_status->status = 2;
                                $update_status->save();
                            }
                        }
                    } else {
                        $response = array(
                            'error' => true,
                            'message' => trans('invalid_question_id'),
                            'code' => 103
                        );
                        return response()->json($response);
                    }
                }
                $response = array(
                    'error' => false,
                    'message' => trans('data_store_successfully'),
                    'code' => 200,
                );
                return response()->json($response);
            } else {
                $response = array(
                    'error' => true,
                    'message' => trans('invalid_online_exam_id'),
                    'code' => 103
                );
                return response()->json($response);
            }
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;
            $class_section_id = $student->class_section_id;
            $class_id = $student->class_section->class_id;


            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            //get current
            $time_data = Carbon::now()->toArray();
            $current_date_time = $time_data['formatted'];

            $exam_query = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['start_date', '<=', $current_date_time]])->orWhere(function ($query) use ($class_id, $session_year_id, $request, $current_date_time) {
                $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['start_date', '<=', $current_date_time]]);
            });
            $exam_exists = $exam_query->count();
            $exam_query_without_session_year = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, ['start_date', '<=', $current_date_time]])->orWhere(function ($query) use ($class_id, $session_year_id, $request, $current_date_time) {
                $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, ['start_date', '<=', $current_date_time]]);
            });

            // checks the exams exists
            if (isset($exam_exists) && ! empty($exam_exists)) {
                //total online exams id and counts
                $total_exam_ids = $exam_query->pluck('id');
                //online exam ids attempted
                $attempted_online_exam_ids = StudentOnlineExamStatus::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->pluck('online_exam_id');

                //get the submitted answers (i.e. option id)
                $online_exams_attempted_answers = OnlineExamStudentAnswer::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->pluck('option_id');

                //get the submitted choiced question id
                $online_exams_submitted_question_ids = OnlineExamStudentAnswer::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->pluck('question_id');

                //get the questions id
                $get_question_ids = OnlineExamQuestionChoice::whereIn('id', $online_exams_submitted_question_ids)->pluck('question_id');

                //removes the question id of the question if one of the answer of particular question is wrong
                foreach ($get_question_ids as $question_id) {
                    $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $online_exams_attempted_answers)->count();
                    if ($check_questions_answers_exists) {
                        unset($get_question_ids[array_search($question_id, $get_question_ids->toArray())]);
                    }
                }
                //get the correct answers question id
                $correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $get_question_ids)->whereIn('answer', $online_exams_attempted_answers)->pluck('question_id');


                //total exams
                $total_exams = $exam_query_without_session_year->count();

                //total exam attempted
                $total_attempted_exams = StudentOnlineExamStatus::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->count();

                // total missed exams
                $total_missed_exams = $exam_query_without_session_year->whereNotIn('id', $attempted_online_exam_ids)->count();

                // get the correct choiced question id and marks
                $total_obtained_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->whereIn('online_exam_id', $total_exam_ids)->whereIn('question_id', $correct_answers_question_id)->first();
                $total_obtained_marks = $total_obtained_marks['sum(marks)'];

                //overall total marks
                $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->whereIn('online_exam_id', $total_exam_ids)->first();
                $total_marks = $total_marks['sum(marks)'];

                if ($total_obtained_marks) {
                    $percentage = number_format(($total_obtained_marks * 100) / $total_marks, 2);
                }


                // particular online exam data
                $online_exams_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['start_date', '<=', $current_date_time]])->orWhere(function ($query) use ($class_id, $session_year_id, $request, $current_date_time) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['start_date', '<=', $current_date_time]]);
                })->with([
                            'student_attempt' => function ($q) use ($student) {
                                $q->where('student_id', $student->id);
                            }
                        ])->has('question_choice')->paginate(10)->toArray();


                $exam_list = array();
                $total_obtained_marks_exam = '';
                foreach ($online_exams_db['data'] as $data) {
                    $exam_submitted_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('question_id');
                    $get_exam_question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_submitted_question_ids)->pluck('question_id');


                    $exam_attempted_answers = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('option_id');


                    //removes the question id of the question if one of the answer of particular question is wrong
                    foreach ($get_exam_question_ids as $question_id) {
                        $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $exam_attempted_answers)->count();
                        if ($check_questions_answers_exists) {
                            unset($get_exam_question_ids[array_search($question_id, $get_exam_question_ids->toArray())]);
                        }
                    }

                    $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $get_exam_question_ids)->whereIn('answer', $exam_attempted_answers)->pluck('question_id');

                    $total_obtained_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->whereIn('question_id', $exam_correct_answers_question_id)->first();
                    $total_obtained_marks_exam = $total_obtained_marks_exam['sum(marks)'];
                    $total_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                    $total_marks_exam = $total_marks_exam['sum(marks)'];

                    $exam_list[] = array(
                        'online_exam_id' => $data['id'],
                        'title' => $data['title'],
                        'obtained_marks' => $total_obtained_marks_exam ?? "0",
                        'total_marks' => $total_marks_exam ?? "0",
                    );

                }


                // array of final data
                $online_exam_report_data = array(
                    'total_exams' => $total_exams,
                    'attempted' => $total_attempted_exams,
                    'missed_exams' => $total_missed_exams,
                    'total_marks' => $total_marks ?? "0",
                    'total_obtained_marks' => $total_obtained_marks ?? "0",
                    'percentage' => $percentage ?? "0",
                    'exam_list' => array(
                        'current_page' => $online_exams_db['current_page'],
                        'data' => $exam_list,
                        'from' => $online_exams_db['from'],
                        'last_page' => $online_exams_db['last_page'],
                        'per_page' => $online_exams_db['per_page'],
                        'to' => $online_exams_db['to'],
                        'total' => $online_exams_db['total'],
                    )
                );
            }
            $response = array(
                'error' => false,
                'data' => $online_exam_report_data ?? [],
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getAssignmentReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;

            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // get the assignments ids
            $assingment_ids = Assignment::where(['class_section_id' => $student->class_section_id, 'session_year_id' => $session_year_id, 'subject_id' => $request->subject_id])->pluck('id');

            //total assignments of class
            $total_assignments = Assignment::where(['class_section_id' => $student->class_section_id, 'session_year_id' => $session_year_id, 'subject_id' => $request->subject_id])->count();

            //total assignment submiited
            $total_submitted_assignments = AssignmentSubmission::where('student_id', $student->id)->whereIn('assignment_id', $assingment_ids)->count();

            // submitted assingment id
            $submitted_assignment_ids = AssignmentSubmission::where('student_id', $student->id)->whereIn('assignment_id', $assingment_ids)->pluck('assignment_id');

            //total assignment unsubmitted
            $total_assingment_unsubmitted = Assignment::where(['class_section_id' => $student->class_section_id, 'subject_id' => $request->subject_id])->whereNotIn('id', $submitted_assignment_ids)->count();

            //total points of assignment submitted
            $total_assignment_submitted_points = Assignment::select(DB::raw("sum(points)"))->where('class_section_id', $student->class_section_id)->whereIn('id', $submitted_assignment_ids)->whereNot('points', null)->first();
            $total_assignment_submitted_points = $total_assignment_submitted_points['sum(points)'];

            // total obtained assignment points
            $assingment_id_with_points = Assignment::where(['class_section_id' => $student->class_section_id, 'subject_id' => $request->subject_id])->whereIn('id', $submitted_assignment_ids)->whereNot('points', null)->pluck('id');
            $total_points_obtained = AssignmentSubmission::select(DB::raw("sum(points)"))->whereIn('assignment_id', $assingment_id_with_points)->where('student_id', $student->id)->first();
            $total_points_obtained = $total_points_obtained['sum(points)'];

            if ($total_points_obtained) {
                //percentage
                $percentage = number_format(($total_points_obtained * 100) / $total_assignment_submitted_points, 2);
            }

            $submitted_assignment_data_db = Assignment::with('submission')->where(['class_section_id' => $student->class_section_id, 'subject_id' => $request->subject_id])->whereIn('id', $submitted_assignment_ids)->whereNot('points', null);
            $submitted_assignment_data_with_points = $submitted_assignment_data_db->paginate(10)->toArray();

            $submitted_assingment_data = array();
            foreach ($submitted_assignment_data_with_points['data'] as $submitted_data) {
                $submitted_assingment_data[] = array(
                    'assignment_id' => $submitted_data['id'],
                    'assignment_name' => $submitted_data['name'],
                    'obtained_points' => $submitted_data['submission']['points'],
                    'total_points' => $submitted_data['points']
                );
            }
            $assingment_report = array(
                'assignments' => $total_assignments,
                'submitted_assignments' => $total_submitted_assignments,
                'unsubmitted_assignments' => $total_assingment_unsubmitted,
                'total_points' => $total_assignment_submitted_points ?? "0",
                'total_obtained_points' => $total_points_obtained ?? "0",
                'percentage' => $percentage ?? "0",
                'submitted_assignment_with_points_data' => array(
                    'current_page' => $submitted_assignment_data_with_points['current_page'],
                    'data' => $submitted_assingment_data,
                    'from' => $submitted_assignment_data_with_points['from'],
                    'last_page' => $submitted_assignment_data_with_points['last_page'],
                    'per_page' => $submitted_assignment_data_with_points['per_page'],
                    'to' => $submitted_assignment_data_with_points['to'],
                    'total' => $submitted_assignment_data_with_points['total'],
                )
            );
            $response = array(
                'error' => false,
                'data' => $assingment_report,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response, 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function getOnlineExamResultList(Request $request)
    {
        try {
            $student = $request->user()->student;
            $class_section_id = $student->class_section_id;
            $class_id = $student->class_section->class_id;

            // current session year id
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // get the class subject id on the basis of subject id passed
            // query meets the condition for both class section and class
            if (isset($request->subject_id) && ! empty($request->subject_id)) {
                $online_exam_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id])->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $session_year_id, $request, $student) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'session_year_id' => $session_year_id, 'subject_id' => $request->subject_id])->with('subject')->whereHas('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->with('subject')->paginate(10)->toArray();
            } else {
                $online_exam_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'session_year_id' => $session_year_id])->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $session_year_id, $student) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'session_year_id' => $session_year_id])->with('subject')->whereHas('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->with('subject')->paginate(10)->toArray();
            }
            $exam_list_data = array();
            foreach ($online_exam_db['data'] as $data) {
                //get the choice question id
                $exam_submitted_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('question_id');
                $exam_submitted_date = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('submitted_date')->first();

                $question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_submitted_question_ids)->pluck('question_id');


                $exam_attempted_answers = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('option_id');

                //removes the question id of the question if one of the answer of particular question is wrong
                foreach ($question_ids as $question_id) {
                    $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $exam_attempted_answers)->count();
                    if ($check_questions_answers_exists) {
                        unset($question_ids[array_search($question_id, $question_ids->toArray())]);
                    }
                }

                $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->pluck('question_id');

                // get the data of only attempted data
                $total_obtained_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->whereIn('question_id', $exam_correct_answers_question_id)->first();
                $total_obtained_marks_exam = $total_obtained_marks_exam['sum(marks)'];
                $total_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                $total_marks_exam = $total_marks_exam['sum(marks)'];

                $exam_list_data[] = array(
                    'online_exam_id' => $data['id'],
                    'subject' => array(
                        'id' => $data['subject_id'],
                        'name' => $data['subject']['name'] . ' - ' . $data['subject']['type'],
                    ),
                    'title' => $data['title'],
                    'obtained_marks' => $total_obtained_marks_exam ?? "0",
                    'total_marks' => $total_marks_exam ?? "0",
                    'exam_submitted_date' => $exam_submitted_date ?? date('Y-m-d', strtotime($data['end_date']))
                );
            }
            $exam_list = array(
                'current_page' => $online_exam_db['current_page'],
                'data' => $exam_list_data ?? '',
                'from' => $online_exam_db['from'],
                'last_page' => $online_exam_db['last_page'],
                'per_page' => $online_exam_db['per_page'],
                'to' => $online_exam_db['to'],
                'total' => $online_exam_db['total'],
            );
            $response = array(
                'error' => false,
                'data' => $exam_list ?? '',
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'online_exam_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;

            //get the total questions count
            $total_questions = OnlineExamQuestionChoice::where('online_exam_id', $request->online_exam_id)->count();

            //get the exam's choiced question id
            $exam_choiced_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->pluck('question_id');

            //get the questions id
            $question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_choiced_question_ids)->pluck('question_id');

            //get the options submitted by student
            $exam_attempted_answers = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->pluck('option_id');

            //removes the question id of the question if one of the answer of particular question is wrong
            foreach ($question_ids as $question_id) {
                $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $exam_attempted_answers)->count();
                if ($check_questions_answers_exists) {
                    unset($question_ids[array_search($question_id, $question_ids->toArray())]);
                }
            }

            // get the correct answers counter
            $exam_correct_answers = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->groupby('question_id')->pluck('question_id')->count();

            // question id of correct answers
            $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->pluck('question_id');

            //data of correct answers
            $exam_correct_answers_data = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->groupby('question_id')->get();

            // array of correct answer with choiced exam id and marks
            $correct_answers_data = array();
            foreach ($exam_correct_answers_data as $correct_data) {
                $choice_questions = OnlineExamQuestionChoice::where(['online_exam_id' => $request->online_exam_id, 'question_id' => $correct_data->question_id])->first();
                $correct_answers_data[] = array(
                    'question_id' => $choice_questions->id,
                    'marks' => $choice_questions->marks
                );

            }

            // get questions ids
            $all_questions_ids = OnlineExamQuestionChoice::whereNotIn('question_id', $question_ids)->where('online_exam_id', $request->online_exam_id)->pluck('question_id');

            // get the incorrect answers && unattempted counter
            $exam_in_correct_answers = OnlineExamQuestionAnswer::whereIn('question_id', $all_questions_ids)->whereNotIn('answer', $exam_attempted_answers)->groupby('question_id')->pluck('question_id')->count();

            // data of in correct && unattempted answers
            $exam_in_correct_answers_data = OnlineExamQuestionAnswer::whereIn('question_id', $all_questions_ids)->whereNotIn('answer', $exam_attempted_answers)->groupby('question_id')->get();

            // array of in correct answer && unattempted with choiced exam id and marks
            $in_correct_answers_data = array();
            foreach ($exam_in_correct_answers_data as $in_correct_data) {
                $choice_questions = OnlineExamQuestionChoice::where(['online_exam_id' => $request->online_exam_id, 'question_id' => $in_correct_data->question_id])->first();
                if (isset($choice_questions) && ! empty($choice_questions)) {
                    $in_correct_answers_data[] = array(
                        'question_id' => $choice_questions->id,
                        'marks' => $choice_questions->marks
                    );
                }
            }

            // total obtained and total marks
            $total_obtained_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $request->online_exam_id)->whereIn('question_id', $exam_correct_answers_question_id)->first();
            $total_obtained_marks = $total_obtained_marks['sum(marks)'];
            $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $request->online_exam_id)->first();
            $total_marks = $total_marks['sum(marks)'];

            // final array data
            $exam_result = array(
                'total_questions' => $total_questions,
                'correct_answers' => array(
                    'total_questions' => $exam_correct_answers,
                    'question_data' => $correct_answers_data ?? ''
                ),
                'in_correct_answers' => array(
                    'total_questions' => $exam_in_correct_answers,
                    'question_data' => $in_correct_answers_data ?? ''
                ),
                'total_obtained_marks' => $total_obtained_marks ?? '0',
                'total_marks' => $total_marks
            );
            $response = array(
                'error' => false,
                'data' => $exam_result ?? '',
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }


*/