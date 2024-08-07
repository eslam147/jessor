<?php

namespace App\Http\Controllers\student;

use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonTopic;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TopicsController extends Controller
{
    public function index()
    {
        dd('test');
    }

    public function show($id)
    {
        $lesson = Lesson::with('file')->active()->findOrFail($id);
        if($lesson->is_paid == 1){
            abort_unless(
                Enrollment::where('user_id', Auth::user()->id)->where('lesson_id', $id)->exists(),
                Response::HTTP_UNAUTHORIZED
            );
            $topics = LessonTopic::active()->where('lesson_id', $lesson->id)->get();
        }else{

            $topics = LessonTopic::active()->where('lesson_id', $lesson->id)->get();
        }

        return view('student_dashboard.topics.index', compact('topics','lesson'));
    }

    public function topic_files($topic_id)
    {
        //$topic = LessonTopic::with('file')->active()->findOrFail($topic_id);
        $files = File::where('modal_id',$topic_id)->where('type',1)->get();
        return view('student_dashboard.files.index', compact('files'));
    }

    public function get_file(Request $request){
        $file = File::find($request->id);
        return view('student_dashboard.files.file',compact('file'));
        //return response()->json(['id' => $id]);
    }

}
