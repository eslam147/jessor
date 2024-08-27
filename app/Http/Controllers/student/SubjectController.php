<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Models\Settings;
use App\Models\Students;
use App\Models\ClassSection;
use App\Models\SubjectTeacher;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $class_section_id = Students::where('user_id', Auth::user()->id)->value('class_section_id');

        $class_id = ClassSection::whereId($class_section_id)->valueOrFail('class_id');

        $subjects = Subject::whereHas('classSubjects', fn($q) => $q->where('class_id', $class_id))
            ->latest()
            ->with([
                'teachersViaSubject' => fn($q) => $q->where('class_section_id', $class_section_id)->with('user'),
            ])->withCount([
                    'lessons' => fn($q) => $q->where('class_section_id', $class_section_id),
                    'onlineExams' => fn($q) => $q->whereIn('model_type', [ClassSection::class])->where('model_id', $class_section_id),
                ])->get();

        return view('student_dashboard.subject.index', compact('subjects'));
    }

    public function show(Subject $subject)
    {

        $class_section_id = Students::where('user_id', Auth::user()->id)->value('class_section_id');
        $show_teachers = Settings::where('type', 'show_teachers')->value('message');

        if ($show_teachers == 'allow') {
            $subjectTeachers = SubjectTeacher::where('subject_id', $subject->id)->where('class_section_id', $class_section_id)->with([
                'teacher' => fn($q) => $q->with('user')->withCount([
                    'lessons' => fn($q) => $q->where('class_section_id', $class_section_id)->active(),
                    'questions',
                    'students'
                ])
            ])->get()->pluck('teacher');
            return view('student_dashboard.teachers.index', compact('subjectTeachers', 'subject'));
        }

        $lessons = Lesson::where('subject_id', $subject->id)
            ->where('class_section_id', $class_section_id)
            ->withCount('studentActiveEnrollment')->get();
        return view('student_dashboard.lessons.index', compact('lessons'));
    }


}