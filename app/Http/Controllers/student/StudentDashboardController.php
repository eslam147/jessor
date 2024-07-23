<?php

namespace App\Http\Controllers\student;


use App\Models\Students;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        //get the subjects of the student
            $class_section_id = Students::where('user_id',Auth::user()->id)->first()->class_section_id;
            $class_section    = ClassSection::findOrFail($class_section_id);
            $class_id         = $class_section->class_id;
            $subjects = ClassSubject::where('class_id', $class_id)->with('subject')->latest()->take(3)->get()->pluck('subject');

        //get the time table of the student

        return view('student_dashboard.dashboard',compact('subjects'));
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
