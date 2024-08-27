<?php

namespace App\Http\Controllers\student;

use App\Models\Coupon;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Students;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $subjects = [];
        $purchasedLessons = Lesson::withWhereHas('studentActiveEnrollment')->latest()->take(10);
        //get the subjects of the student
        // $studentClassSection = Auth::user()->student->class_section_id;
        // $class_section = ClassSection::find($studentClassSection);
        // if ($class_section) {
        //     $class_id = $class_section->class_id;
        //     $subjects = Subject::whereHas('class', function ($q) use ($class_id) {
        //         $q->where('class_id', $class_id);
        //     })->latest()->take(3)->get();
        // }

        //get the time table of the student
        return view('student_dashboard.dashboard', compact('purchasedLessons'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
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
