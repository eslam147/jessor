<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Models\Students;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        dd('teachers');
    }

    public function teacher_lessons($teacher_id, $subject_id)
    {
        $userId = Auth::user()->id;
        $class_section_id = Students::where('user_id', Auth::user()->id)->first()->class_section_id;
        //get the lesson's based on the teacher and subject and section class With Enrollment Count
        //$lessons = Lesson::where('subject_id',$subject_id)->where('class_section_id',$class_section_id)->where('teacher_id',$teacher_id)->get();
        $lessons = Lesson::where('subject_id', $subject_id)
            ->where('class_section_id', $class_section_id)
            ->where('teacher_id', $teacher_id)
            ->withCount([
                'enrollments' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->get();
        return view('student_dashboard.lessons.teacher_lessons', compact('lessons'));
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
        //
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
