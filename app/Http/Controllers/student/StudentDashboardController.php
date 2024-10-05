<?php

namespace App\Http\Controllers\student;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Lesson;

use App\Models\LiveLesson;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\Lesson\LiveLessonStatus;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $purchasedLessons = Lesson::withWhereHas('studentActiveEnrollment')->orderByDesc('id')->with('subject')->get();
        $classSectionId = Auth::user()->student()->value('class_section_id') ?? 0;
        
        $liveSessions = LiveLesson::where('class_section_id', $classSectionId)
            ->where('status', LiveLessonStatus::SCHEDULED)
            ->orderByDesc('id')
            ->with('subject', 'teacher.user')
            ->whereHas('participants',function ($q){
                $q->where('participant_type', User::class)->where('participant_id', Auth::user()->id);
            })->get();

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
