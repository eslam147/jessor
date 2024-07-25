<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Models\Settings;
use App\Models\Students;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $class_section_id = Students::where('user_id',Auth::user()->id)->first()->class_section_id;
        $class_section    = ClassSection::findOrFail($class_section_id);
        $class_id         = $class_section->class_id;
        $subjects = ClassSubject::where('class_id', $class_id)->with('subject')->latest()->get()->pluck('subject');
        return view('student_dashboard.subject.index',compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $class_section_id = Students::where('user_id',Auth::user()->id)->first()->class_section_id;
        $subject = Subject::findOrfail($id);
        $show_teachers = Settings::where('type','show_teachers')->first()->message;

        if($show_teachers == 'allow'){
            $subjectTeachers = SubjectTeacher::where('subject_id', $id)->where('class_section_id', $class_section_id)->with('teacher.user')->get()->pluck('teacher.user');
            return view('student_dashboard.teachers.index',compact('subjectTeachers','subject'));
        }else{
            $lessons = Lesson::where('subject_id',$id)->where('class_section_id',$class_section_id)->get();
            return view('student_dashboard.lessons.index',compact('lessons'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
