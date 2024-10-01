<?php

namespace App\Http\Controllers\student;

use App\Enums\Lesson\LiveLessonStatus;
use App\Models\Coupon;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LiveLesson;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $purchasedLessons = Lesson::withWhereHas('studentActiveEnrollment')->orderByDesc('id')->with('subject')->get();
        $liveSessions = LiveLesson::where('class_section_id', Auth::user()->student->class_section_id)
            ->where('status', LiveLessonStatus::SCHEDULED)
            ->orderByDesc('id')
            ->with('subject', 'teacher.user')
            ->get();
        //get the time table of the student
        return view('student_dashboard.dashboard', compact('purchasedLessons', 'liveSessions'));
    }

    public function couponHistory()
    {
        $user = Auth::user();
        $usagesCoupons = Coupon::withWhereHas('usages', function ($query) use ($user) {
            $query->where('used_by_user_type', get_class($user))->where('used_by_user_id', $user->id);
        })->get();

        return view('student_dashboard.coupons.history', compact('usagesCoupons'));
    }
}
