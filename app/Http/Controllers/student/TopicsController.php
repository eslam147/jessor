<?php

namespace App\Http\Controllers\student;

use App\Models\File;
use App\Models\Lesson;
use App\Models\Settings;
use App\Models\Students;
use App\Models\LessonTopic;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('test');
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
    public function show($id){
        $enrollment_count = Enrollment::where('user_id',Auth::user()->id)->where('lesson_id',$id)->get()->count();
        if($enrollment_count == 0){
            return redirect()->route('subjects.index');
        }
        $topics = LessonTopic::where('lesson_id',$id)->get();
        $lesson_name = Lesson::findOrFail($id)->name;
        return view('student_dashboard.topics.index',compact('topics','lesson_name'));
    }

    public function topic_files($topic_id){
        $videos = File::where('modal_type', 'App\Models\Lesson')->where('modal_id',$topic_id)->get();
        $topic_videos = File::where('modal_type', 'App\Models\LessonTopic')->get();
        return view('student_dashboard.files.index',compact('videos','topic_videos'));
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
