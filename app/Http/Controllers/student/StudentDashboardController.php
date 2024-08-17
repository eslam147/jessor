<?php

namespace App\Http\Controllers\student;

use App\Models\Coupon;
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
        //get the subjects of the student
        $class_section_id = Students::where('user_id', Auth::user()->id)->first()->class_section_id;
        $class_section = ClassSection::findOrFail($class_section_id);
        $class_id = $class_section->class_id;
        $subjects = ClassSubject::where('class_id', $class_id)->with('subject')->latest()->take(3)->get()->pluck('subject');

        //get the time table of the student
        return view('student_dashboard.dashboard');
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
