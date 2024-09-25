<?php

namespace App\Http\Controllers\student;

use App\Models\LiveLesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LiveLessonController extends Controller
{
    public function index()
    {
        $classSectionId = Auth::user()->student->class_section_id;
        $liveSessions = LiveLesson::where('class_section_id', $classSectionId)
            ->orderByDesc('id')
            ->with('subject', 'teacher.user')
            ->get()
            ->groupBy(function ($item) {
                return $item->session_date->format('Y-m-d');
            });

        return view('student_dashboard.live_lessons.index', compact('liveSessions'));
    }
}
