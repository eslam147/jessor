<?php

namespace App\Http\Controllers\student;

use App\Models\LiveLesson;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LiveLessonController extends Controller
{
    public function index()
    {
        $classSectionId = Auth::user()->student->class_section_id;
        $liveSessions = LiveLesson::where('class_section_id', $classSectionId)
            ->orderByDesc('id')
            ->with('subject', 'teacher.user', 'meeting')
            ->withCount('participants')
            ->get()
            ->groupBy(fn($item) => $item->session_date->format('Y-m-d'));

        return view('student_dashboard.live_lessons.index', compact('liveSessions'));
    }
    // public function enroll(Request $request, LiveLesson $liveLesson)
    // {
    //     // $request->validate([
    //     //     'payment_method' => 'required'
    //     // ]);
    //     // // $liveLesson->enroll(Auth::user(),$request->payment_method);
    //     // return redirect()->back()->with('success', 'Enrolled successfully');
    // }
}
