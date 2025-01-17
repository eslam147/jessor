<?php

namespace App\Services\Exam;

use App\Models\OnlineExam;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentOnlineExamStatus;
use App\Models\OnlineExamQuestionChoice;
use RealRashid\SweetAlert\Facades\Alert;

class ExamService
{
    public function getOnlineExamList()
    {
        $date = now()->setTimezone('UTC');
        $student = auth()->user()->student;

        $student_subject = $student->subjects();

        $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

        $elective_subjects = [];
        if (isset($student_subject["elective_subject"])) {
            $elective_subjects = collect($student_subject["elective_subject"])->pluck('subject_id')->toArray();
        }

        $subject_id = array_merge($core_subjects, $elective_subjects);

        $class_section_id = $student->class_section->id;
        $class_id = $student->class_section->class_id;
        $session_year_id = getSettings('session_year')['session_year'];

        //get current
        $time_data = $date->toArray();
        $current_date_time = $time_data['formatted'];

        // checks the subject id param is passed or not .
        // query meets the condition for both class section and class
        $examQuery = OnlineExam::query()
            ->where('session_year_id', $session_year_id)
            ->where(function ($q) use ($class_section_id, $class_id) {
                $q->where(function ($q) use ($class_section_id) {
                    $q->where('model_type', ClassSection::class)->where('model_id', $class_section_id);
                })->orWhere(function ($q) use ($class_id) {
                    $q->where('model_type', ClassSchool::class)->where('model_id', $class_id);
                });
            })
            ->where('end_date', '>=', $current_date_time)
            ->has('question_choice')
            ->with([
                'subject',
                'student_attempt' => function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                }
            ])
            // ->whereDoesntHave('student_attempt', function ($q) use ($student) {
            //     $q->where('student_id', $student->id);
            // })
            ->when(
                filled(request('subject_id')),
                fn($q) => $q->where('subject_id', request('subject_id')),
                fn($q) => $q->whereIn('subject_id', $subject_id)
            );


        $exam_data_db = $examQuery->orderBy('start_date')->paginate(15);
        $choicesMarks = OnlineExamQuestionChoice::whereIn('online_exam_id', $exam_data_db->pluck('id'))->select([
            'online_exam_id',
            DB::raw("sum(online_exam_question_choices.marks) as total_marks"),
        ])->groupBy('online_exam_id')->pluck('total_marks', 'online_exam_id');


        $subjectsWithExams = $exam_data_db->groupBy('subject.id')->map(function ($items) use ($exam_data_db) {

            return [
                'name' => $items[0]['subject']['name'],
                'subject_id' => $items[0]['subject']['id'],
                'image' => $items[0]['subject']['image'],
                'exams' => $exam_data_db->where('subject_id', $items[0]['subject']['id'])->all(),
            ];
        });

        $exam_data = [];
        $exam_list = [];
        if (! empty($exam_data_db)) {
            // making the array of exam data
            foreach ($exam_data_db as $data) {

                // total marks of exams
                // $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                $total_marks = $choicesMarks[$data['id']];

                if ($data['model_type'] instanceof ClassSection) {
                    $class_section_data = ClassSection::where('id', $data['model_id'])->with('class.medium', 'section')->first();
                    $class_name = $class_section_data->class->name . ' - ' . $class_section_data->section->name . ' ' . $class_section_data->class->medium->name;
                } else {
                    $class_data = ClassSchool::where('id', $data['model_id'])->with('medium')->first();
                    $class_name = $class_data?->name . ' ' . $class_data?->medium->name;
                }

                if (! is_null($total_marks)) {
                    $exam_list[] = (object) [
                        'exam_id' => $data['id'],
                        'class' => [
                            'id' => $data['model_id'],
                            'name' => $class_name
                        ],
                        'subject' => [
                            'id' => $data['subject_id'],
                            'name' => $data['subject']['name'] . ' - ' . $data['subject']['type']
                        ],
                        'title' => $data['title'],
                        'exam_key' => $data['exam_key'],
                        'pass_mark' => $data['pass_mark'],
                        'duration' => $data['duration'],
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'status' => $data['status'],
                        'student_attempt' => $data['student_attempt'] ?? null,
                        'total_marks' => $total_marks,
                    ];
                }
            }

            //adding the exam data with pagination data
            $exam_data = [
                'data' => $exam_list,
                'by_subject' => $subjectsWithExams,
                'current_date' => $date,
            ];
        }
        return $exam_data;
    }
    public function getExamTerms()
    {

    }
    public function calculateRemainingMinutes(StudentOnlineExamStatus $studentStatus, OnlineExam $exam): float|int
    {
        $elapsedTime = now()->diffInMinutes($studentStatus->created_at);

        $remainingMinutes = max(($exam->duration - $elapsedTime), 0);

        return $remainingMinutes;
    }
    public function getOnlineExamQuestions(OnlineExam $onlineExam)
    {
        // --------------------------------------------- \\
        $onlineExam->load('question_choice.questions')->loadCount('question_choice');
        // --------------------------------------------- \\
        $get_exam_questions_db = $onlineExam->question_choice;
        // --------------------------------------------- \\
        $questions_data = [];
        $total_marks = 0;
        // --------------------------------------------- \\
        foreach ($get_exam_questions_db as $exam_questions) {
            $total_marks += $exam_questions->marks;
            // make question array
            $questions_data[] = $this->formatQuestion(
                $exam_questions->questions,
                $this->foramtExamOptions($exam_questions->questions->options),
                $this->formatAnswers($exam_questions->questions->answers)
            );
        }

        return [
            'data' => collect($questions_data)->shuffle() ?? [],
            'exam' => $onlineExam,
            'total_questions' => $onlineExam->question_choice_count,
            'total_marks' => $total_marks,
        ];
    }
    public function foramtExamOptions($options): array
    {
        $formateOptions = [];
        foreach ($options as $option) {
            $formateOptions[] = [
                'id' => $option->id,
                'option' => htmlspecialchars_decode($option->option)
            ];
        }
        return $formateOptions;
    }
    public function formatAnswers($answers)
    {
        $formatedData = [];
        foreach ($answers as $answer) {
            $formatedData[] = [
                'id' => $answer->id,
                'option_id' => $answer->answer,
                'answer' => $answer->options->option
            ];
        }
        return $formatedData;
    }
    function formatQuestion($question, $options, $answers)
    {
        return [
            'id' => $question->id,
            'question' => htmlspecialchars_decode($question->question),
            'question_type' => $question->question_type,
            'options' => $options,
            'answers' => $answers,
            'marks' => $question->marks,
            'image' => $question->image_url,
            'note' => $question->note,
        ];
    }
    public function getOnlineExamStatus($studentId, $examId): ?StudentOnlineExamStatus
    {
        return StudentOnlineExamStatus::where([
            'online_exam_id' => $examId,
            'student_id' => $studentId
        ])->first();
    }
    public function createOnlineExamStatus($studentId, $examId): StudentOnlineExamStatus
    {
        return StudentOnlineExamStatus::create([
            'online_exam_id' => $examId,
            'student_id' => $studentId,
            'status' => StudentOnlineExamStatus::IN_PROGRESS,
        ]);
    }

    /****** */
    // public function studentLeftTimeToStopExam($examId, $studentId)
    // {
    //     $studentExam = $this->examStudentModel->where('student_id',$studentId)->where('exam_id',$examId)->first();
    //     $exam = $this->findExam($examId);

    //     $formatTime = explode(":", $exam->time);

    //     $examStartedAt = Carbon::parse($studentExam->started_at);

    //     $examEndTime = $examStartedAt->copy()->addHours($formatTime[0])->addMinutes($formatTime[1])->addSeconds($formatTime[2]);

    //     $remainingTime = $examEndTime->diff(now());

    //     return [
    //         $remainingTime->h,
    //         $remainingTime->i,
    //         $remainingTime->s
    //     ];
    // }
    // public function studentCantAccessExam($examId, $studentId):bool
    // {
    //     $studentExam = $this->examStudentModel->where('exam_id', $examId)->whereNull('submitted_at')->where('student_id', $studentId)->firstOrFail();
    //     if($studentExam->exists()){
    //         $formatTime = explode(":", $this->exam->time);

    //         $examStartedAt = Carbon::parse($studentExam->started_at);

    //         $examEndTime = $examStartedAt->copy()->addHours($formatTime[0])->addMinutes($formatTime[1])->addSeconds($formatTime[2]);
    //         return ! is_null($studentExam->submitted_at) || $examEndTime->isPast();
    //     }

    //     return false;
    // }

    // public function studentStartExam()
    // {
    //     return $this->examStudentModel->firstOrCreate([
    //         'exam_id' => $this->exam->id,
    //         'student_id' => $this->student->id,
    //     ],[
    //         'exam_time' => $this->exam->time,
    //         'started_at' => now()->toDateTimeString(),
    //     ]);
    // }
    /****** */
}
