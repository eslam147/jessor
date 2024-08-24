<?php

namespace App\Http\Controllers\student;

use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OfflineExamController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $student_subject = $student->subjects();

        $core_subjects = array_column($student_subject["core_subject"], 'subject_id') ?? [];

        $elective_subjects = $student_subject["elective_subject"] ?? [];

        if ($elective_subjects) {
            $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
        }

        $subject_id = array_merge($core_subjects, $elective_subjects);
        $classId = $student->class_section->class_id;

        $exams = Exam::with('session_year:id,name')
            ->where('publish', 1)
            ->whereHas('exam_classes', fn($q) => $q->where('class_id', $classId))
            ->whereHas('timetable', fn($q) => $q->whereIn('subject_id', $subject_id))
            ->leftJoin('exam_timetables', function ($join) use ($classId) {
                $join->on('exams.id', '=', 'exam_timetables.exam_id')
                    ->where('exam_timetables.class_id', '=', $classId);
            })
            ->groupBy('exams.id')
            ->addSelect([
                'exams.*',
                DB::raw('MIN(exam_timetables.date) as starting_date'),
                DB::raw('MAX(exam_timetables.date) as ending_date'),
            ])->get();

        return view('student_dashboard.offline_exams.index', compact('exams'));
        // return view('student_dashboard.offline_exams.index');
    }
    public function show(Exam $exam)
    {
        $exam->load([
            'timetable' => function ($q) {
                return $q->where('class_id', Auth::user()->student->class_section->class_id)->with('subject');
            }
        ]);

        return view('student_dashboard.offline_exams.show', compact('exam'));
    }
}
