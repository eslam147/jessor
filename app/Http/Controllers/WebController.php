<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Faq;
use App\Models\Event;
use App\Models\Media;
use App\Models\Slider;
use App\Models\Holiday;
use App\Models\Teacher;
use App\Models\Students;
use App\Models\ContactUs;
use App\Models\MediaFile;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use App\Helpers\DeviceHelper;
use App\Models\SubjectTeacher;
use Illuminate\Support\Carbon;
use App\Models\EducationalProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WebController extends Controller
{
    public function index()
    {
        $eprograms = null;
        $images = null;
        $videos = null;
        $news = null;
        $faqs = null;

        $date = now();
        $settings = cachedSettings()->pluck('message','type');
        $sliders = Slider::whereIn('type', [2, 3])->get();
        $webSettings = cachedWebSettings();
        $about = $webSettings->firstWhere('name', 'about_us');
        $event = $webSettings->firstWhere('name', 'events');
        $program = $webSettings->firstWhere('name', 'programs');
        $photo = $webSettings->firstWhere('name', 'photos');
        $video = $webSettings->firstWhere('name', 'videos');
        $faq = $webSettings->firstWhere('name', 'faqs');
        $app = $webSettings->firstWhere('name', 'app');
        if ($program) {
            $eprograms = EducationalProgram::get();
        }

        if ($event) {
            $events = Event::with('multipleEvent')->where(function ($query) use ($date) {
                $query->where('start_date', '>=', $date)->orWhere('end_date', '>=', $date)
                    ->orWhereDate('start_date', '=', $date)->orWhere('end_date', '=', $date);
            })->get();

            $holiday = Holiday::where('date', '>=', $date)->get();

            $collections = $events->merge($holiday);
            $sortedCollection = $collections->sortby('date')->sortby('start_date');
            $news = $sortedCollection->take(6);

        }

        if ($photo) {
            $images = Media::where('type', 1)->get();
        }

        if ($video) {
            $videos = Media::where('type', 2)->get();
        }

        if ($faq) {
            $faqs = Faq::where('status', 1)->get();
        }


        return view('web.index', compact('settings', 'sliders', 'about', 'event', 'program', 'photo', 'video', 'faq', 'app', 'eprograms', 'news', 'images', 'videos', 'faqs'));
    }

    public function about()
    {
        $teachercount = 0;
        $studentcount = 0;
        $teachers = null;
        $settings = getSettings();
        $about = WebSetting::where('name', 'about_us')->where('status', 1)->first();
        $whoweare = WebSetting::where('name', 'who_we_are')->where('status', 1)->first();
        $teacher = WebSetting::where('name', 'teacher')->where('status', 1)->first();

        if ($teacher) {
            $subjectData = [];
            $teachers = Teacher::with('user:id,first_name,last_name,image')->get();
            $teachercount = Teacher::count();
            $studentcount = Students::count();
        }


        return view('web.about-us', compact('settings', 'about', 'whoweare', 'teacher', 'teachers', 'teachercount', 'studentcount'));
    }

    public function contact_us()
    {
        $settings = getSettings();
        $question = WebSetting::where('name', 'question')->where('status', 1)->first();
        return view('web.contact-us', compact('settings', 'question'));
    }

    public function photo()
    {
        $settings = getSettings();
        $images = null;
        $photo = WebSetting::where('name', 'photos')->where('status', 1)->first();
        if ($photo) {
            $images = Media::where('type', 1)->get();
        }

        return view('web.photos', compact('settings', 'photo', 'images'));
    }

    public function video()
    {
        $settings = getSettings();
        $videos = null;

        $video = WebSetting::where('name', 'videos')->where('status', 1)->first();
        $videos = Media::where('type', 2)->get();

        return view('web.videos', compact('settings', 'video', 'videos'));
    }

    public function photo_details($id)
    {
        $settings = getSettings();

        $images = MediaFile::where('media_id', $id)->get();
        return view('web.photos-details', compact('settings', 'images'));
    }

    public function contact_us_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {

            ContactUs::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'date' => now(),
            ]);

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully'),
            ];
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ];
        }
        return response()->json($response);
    }
}
