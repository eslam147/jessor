<?php

namespace App\Http\Controllers;

use App\Helpers\DeviceHelper;
use App\Http\Requests\CommentsRequest;
use App\Models\ClassSection;
use App\Models\Comment;
use App\Models\ContactUs;
use App\Models\EducationalProgram;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Holiday;
use App\Models\Lesson;
use App\Models\Media;
use App\Models\MediaFile;
use App\Models\Slider;
use App\Models\Students;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WebSetting;
use App\Rules\FilterWordsRule;
use App\Rules\ValidMessageContent;
use App\Services\Media\TenantMediaService;
use App\Traits\TenantImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;
use Throwable;

class WebController extends Controller
{
    use TenantImageTrait;
    public function index()
    {
        $eprograms = null;
        $images = null;
        $videos = null;
        $news = null;
        $faqs = null;

        $date = now();
        $settings = cachedSettings()->pluck('message', 'type');
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

    public function send_follow(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $teacher = Teacher::find($request->id);
        $user->toggleFollow($teacher);
    }

    public function send_like(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $subject = Subject::find($request->id);
        if($user->hasDisliked($subject))
        {
            $user->undislike($subject);
        }
        $user->toggleLike($subject);

    }

    public function send_dislike(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $subject = Subject::find($request->id);
        if($user->hasLiked($subject))
        {
            $user->unlike($subject);
        }
        $user->toggleDislike($subject);
    }
    public function instructors()
    {
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $class_id = (auth()->check() && auth()->user()->student) ? ClassSection::whereId($class_section_id)->valueOrFail('class_id') : null;
        $teachers = Teacher::with('user:id,first_name,last_name,image')->withCount(['students', 'subjects' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->whereHas('class_section', function ($q) use ($class_section_id) {
                    $q->whereId($class_section_id);
                });
            }
        }, 'lessons_teacher' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
            }
        }, 'questions' => function ($query) use ($class_id) {
            if ($class_id) {
                $query->whereHas('class_subject', function ($query) use ($class_id) {
                    $query->where('class_id', $class_id);
                });
            }
        }]);
        $teachers = (auth()->check() && auth()->user()->student) ? $teachers->whereHas('students', function ($q) use ($class_section_id) {
            $q->where(DB::raw('students.class_section_id'), $class_section_id);
        })->paginate(9) : $teachers->paginate(9);
        $classes = ['bg-success-light', 'bg-info-light', 'bg-primary-light', 'bg-warning-light', 'bg-danger-light', 'bg-light-light', 'bg-dark-light', 'bg-success-dark'];
        $headers = ['bg-info', 'bg-primary', 'bg-warning', 'bg-danger', 'bg-dark', 'bg-success'];
        $settings = getSettings();
        return view('web.teachers', compact('teachers', 'settings', 'classes', 'headers'));
    }

    public function get_teachers(Request $request)
    {
        $page = $request->page;
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $class_id = (auth()->check() && auth()->user()->student) ? ClassSection::whereId($class_section_id)->valueOrFail('class_id') : null;
        $teachers = Teacher::with('user:id,first_name,last_name,image')->withCount(['students', 'subjects' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->whereHas('class_section', function ($q) use ($class_section_id) {
                    $q->whereId($class_section_id);
                });
            }
        }, 'lessons_teacher' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
            }
        }, 'questions' => function ($query) use ($class_id) {
            if ($class_id) {
                $query->whereHas('class_subject', function ($query) use ($class_id) {
                    $query->where('class_id', $class_id);
                });
            }
        }]);
        $teachers = (auth()->check() && auth()->user()->student) ? $teachers->whereHas('students', function ($q) use ($class_section_id) {
            $q->where(DB::raw('students.class_section_id'), $class_section_id);
        })->paginate(9) : $teachers->paginate(9);
        $classes = ['bg-success-light', 'bg-info-light', 'bg-primary-light', 'bg-warning-light', 'bg-danger-light', 'bg-light-light', 'bg-dark-light', 'bg-success-dark'];
        $headers = ['bg-info', 'bg-primary', 'bg-warning', 'bg-danger', 'bg-dark', 'bg-success'];
        return view('web.get_teachers', compact('teachers', 'classes', 'headers'))->render();
    }

    public function instructor($id)
    {
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $class_id = (auth()->check() && auth()->user()->student) ? ClassSection::whereId($class_section_id)->valueOrFail('class_id') : null;
        $teacher = Teacher::where('id', $id)->with(['user:id,first_name,last_name,image,email,mobile', 'students', 'subjects' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->whereHas('class_section', function ($q) use ($class_section_id) {
                    $q->whereId($class_section_id);
                })->with('subject', 'class_section');
            }
            $query->with('subject', 'class_section');
        }])->withCount(['students', 'subjects' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->whereHas('class_section', function ($q) use ($class_section_id) {
                    $q->whereId($class_section_id);
                });
            }
        }, 'lessons_teacher' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
            }
        }, 'questions' => function ($query) use ($class_id) {
            if ($class_id) {
                $query->whereHas('class_subject', function ($query) use ($class_id) {
                    $query->where('class_id', $class_id);
                });
            }
        }]);
        $teacher = (auth()->check() && auth()->user()->student) ? $teacher->whereHas('students', function ($q) use ($class_section_id) {
            if (!empty($class_section_id)) {
                $q->where(DB::raw('lessons.class_section_id'), $class_section_id);
            }
        })->first() : $teacher->first();
        $user = auth()->check() ? User::find(auth()->user()->id) : null;
        $follow = auth()->check() ? $user->isFollowing(Teacher::find($id)) : false;
        $subjects = $teacher->subjects()->when($class_section_id, function ($query) use ($class_section_id) {
            $query->whereHas('class_section', function ($q) use ($class_section_id) {
                $q->whereId($class_section_id);
            });
        })->with(['subject' => function ($query) use ($class_section_id) {
            $query->withCount(['lessons' => function ($query) use ($class_section_id) {
                if (!empty($class_section_id)) {
                    $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
                }
            }]);
        }, 'class_section']);
        $school_classes = array_column(array_column($subjects->get()->toArray(), 'class_section'), 'class');
        $subjectsId = array_unique(array_column($subjects->get()->toArray(), 'subject_id'));
        $all_subjects = Subject::whereIn('id', $subjectsId)->get();
        $subjects = $subjects->paginate(9);
        $get_files = scandir(public_path('student\images\avatar'));
        $files = [];
        if (count($get_files) > 0) {
            foreach ($get_files as $file) {
                if ($file !== '.' && $file !== '..' && is_file('student/images/avatar/' . $file)) {
                    $files[] = $file;
                }
            }
        }
        $comments = $teacher->comments()->orderBy('id','desc')->paginate(10);
        $classes = ['bg-success-light', 'bg-info-light', 'bg-primary-light', 'bg-warning-light', 'bg-danger-light', 'bg-light-light', 'bg-dark-light', 'bg-success-dark'];
        $headers = ['bg-info', 'bg-primary', 'bg-warning', 'bg-danger', 'bg-dark', 'bg-success'];
        $settings = getSettings();
        return view('web.teacher', compact('follow','teacher','comments', 'all_subjects', 'school_classes', 'headers', 'classes', 'subjects', 'files', 'settings'));
    }

    public function store_comment(CommentsRequest $request)
    {
        $teacher = Teacher::find(trim($request->id));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newImage = TenantMediaService::uploadImage($image,"comment/image");
            $teacher->comment($request->msg, [
                'type' => 'comment',
                'image' => $newImage,
                'parent_id' => null,
                'file_type' => 'image'
            ]);
        } else {
            $teacher->comment($request->msg);
        }
    }

    public function replay_comment(CommentsRequest $request)
    {
        $comment = Comment::where('id',trim($request->id))->first();
        $user = User::find($comment->user_id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newImage = TenantMediaService::uploadImage($image,"comment/image");
            $comment->commentAsUser($user,$request->msg, 
            [
                'image' => $newImage,
                'file_type' => 'image'
            ]);
        } else {
            $comment->commentAsUser($user,$request->msg);
        }
    }

    public function get_replaies_comment(Request $request)
    {
        $replies = Comment::find($request->id);;
        $replies = $replies->comments()->paginate(10);
        $comment = Comment::where('id',trim($request->id))->with('commentator')->first();
        return view('web.comments.get_replaies_comment',compact('replies','comment'));
    }
    public function get_auth()
    {
        return response()->json(auth()->user());
    }
    public function get_comments(Request $request)
    {
        $page = $request->page ?? 1;
        $teacher = Teacher::find(trim($request->id));
        $comments = $teacher->comments()->orderBy('id','desc')->paginate(10);
        return view('web.comments.comments',compact('comments'));
    }
    public function get_subjects(Request $request)
    {
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $id = $request->teacher;
        $class_id = $request->class_id;
        $subject_id = $request->subject;
        $teacher = Teacher::where('id', $id)->with(['user:id,first_name,last_name,image,email,mobile', 'students', 'subjects' => function ($query) use ($class_section_id) {
            $query->whereHas('class_section')->with('subject');
        }])->withCount(['students', 'subjects' => function ($query) use ($class_section_id) {
            $query->whereHas('class_section');
        }, 'lessons_teacher' => function ($query) use ($class_section_id) {
            if ($class_section_id) {
                $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
            }
        }]);
        if (auth()->check() && auth()->user()->student) {
            $teacher = $teacher->whereHas('students', function ($q) use ($class_section_id) {
                $q->where('students.class_section_id', $class_section_id);
            })->first();
        } else {
            $teacher = $teacher->first();
        }

        $subjects = $teacher->subjects();
        if (!empty($subject_id)) {
            $subjects->where('subject_id', $subject_id)->whereHas('class_section')->with(['subject' => function ($query) use ($class_section_id) {
                $query->withCount(['lessons' => function ($query) use ($class_section_id) {
                    if (!empty($class_section_id)) {
                        $query->where('class_section_id', $class_section_id);
                    }
                }]);
            }, 'class_section' => function ($query) use ($class_id) {
                if (!empty($class_id)) {
                    $query->where('class_id', $class_id);
                }
            }]);
        }
        $subjects->whereHas('class_section', function ($query) use ($class_section_id, $class_id) {
            if (!empty($class_id)) {
                $query->where('class_id', $class_id);
            }
            if (!empty($class_section_id)) {
                $query->whereId($class_section_id);
            }
        })->with(['subject' => function ($query) use ($class_section_id) {
            $query->withCount(['lessons' => function ($query) use ($class_section_id) {
                if (!empty($class_section_id)) {
                    $query->where('class_section_id', $class_section_id);
                }
            }]);
        }, 'class_section' => function ($query) use ($class_id) {
            if (!empty($class_id)) {
                $query->where('class_id', $class_id);
            }
        }]);
        $subjects = $subjects->paginate(9);
        return view('web.get_subjects', compact('subjects'))->render();
    }

    public function subject($id)
    {
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $class_id = (auth()->check() && auth()->user()->student) ? ClassSection::whereId($class_section_id)->valueOrFail('class_id') : null;
        $teacher = SubjectTeacher::where('subject_id', $id)->with(['teacher' => function ($query) use ($class_section_id, $class_id) {
            $query->with(['user:id,first_name,last_name,image,email,mobile'])->withCount(['students', 'subjects' => function ($query) use ($class_section_id) {
                if ($class_section_id) {
                    $query->whereHas('class_section', function ($q) use ($class_section_id) {
                        $q->whereId($class_section_id);
                    });
                }
            }, 'lessons_teacher' => function ($query) use ($class_section_id) {
                if ($class_section_id) {
                    $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
                }
            }, 'questions' => function ($query) use ($class_id) {
                if ($class_id) {
                    $query->whereHas('class_subject', function ($query) use ($class_id) {
                        $query->where('class_id', $class_id);
                    });
                }
            }]);
        }])->first()->teacher;
        $user = auth()->check() ? User::find(auth()->user()->id) : null;
        $follow = auth()->check() ? $user->isFollowing(Teacher::find($teacher->id)) : 0;
        $lessons = !empty($class_section_id) ? Lesson::where('subject_id', $id)->where('class_section_id', $class_section_id)->withCount('topic')->paginate(9) : Lesson::where('subject_id', $id)->withCount('topic')->paginate(9);
        $subject = Subject::where('id', $id)->first();
        $classes = ['bg-success-light', 'bg-info-light', 'bg-primary-light', 'bg-warning-light', 'bg-danger-light', 'bg-light-light', 'bg-dark-light', 'bg-success-dark'];
        $class_section = ClassSection::with(['class', 'section'])->withOutTrashedRelations('class', 'section')->get();
        $headers = ['bg-info', 'bg-primary', 'bg-warning', 'bg-danger', 'bg-dark', 'bg-success'];
        $settings = getSettings();
        return view('web.subject', compact('follow','teacher', 'class_section', 'subject', 'headers', 'classes', 'lessons', 'settings', 'id'));
    }
    public function get_lessons(Request $request)
    {
        $id = $request->subject_id;
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $page = $request->page;
        $lessons = !empty($class_section_id) ? Lesson::where('subject_id', $id)->where('class_section_id', $class_section_id)->withCount('topic')->paginate(9) : Lesson::where('subject_id', $id)->withCount('topic')->paginate(9);
        return view('web.get_lessons', compact('lessons'))->render();
    }

    public function lesson(Lesson $id)
    {
        $lesson = $id;
        $class_section_id = (auth()->check() && auth()->user()->student) ? Students::where('user_id', Auth::user()->id)->value('class_section_id') : null;
        $class_id = (auth()->check() && auth()->user()->student) ? ClassSection::whereId($class_section_id)->valueOrFail('class_id') : null;
        $lesson->load([
            'studentActiveEnrollment',
            'topic' => function ($q) {
                $q->with(['file' => function ($q) {
                    $q->with([
                        'exam', 'assignment' => function ($q) {
                        $q->with('submission');
                    }]);
                }]);
            },
            'teacher' => function ($q) use ($class_section_id,$class_id) {
                $q->with('user')->withCount([
                    'students',
                    'subjects' => function ($query) use ($class_section_id) {
                        if ($class_section_id) {
                            $query->whereHas('class_section', function ($q) use ($class_section_id) {
                                $q->whereId($class_section_id);
                            });
                        }
                    }, 'lessons_teacher' => function ($query) use ($class_section_id) {
                        if ($class_section_id) {
                            $query->where(DB::raw('lessons.class_section_id'), $class_section_id);
                        }
                    }, 'questions' => function ($query) use ($class_id) {
                        if ($class_id) {
                            $query->whereHas('class_subject', function ($query) use ($class_id) {
                                $query->where('class_id', $class_id);
                            });
                        }
                    }
                ]);
            },
        ]);
        $user = auth()->check() ? User::find(auth()->user()->id) : null;
        $follow = auth()->check() ? $user->isFollowing(Teacher::find($lesson->teacher->id)) : 0;
        $topics = $lesson->topic;
        $ids = [];
        $ids = $lesson->topic->pluck('id')->toArray();
        $arr = [];
        foreach($topics as $topic)
        {
            $files = $topic->file;
            if(count($files) > 0)
            {
                foreach($files as $file)
                {
                    if(!empty($file->online_exam_id))
                    {
                        if($file->exam->highest_degree < $file->exam->pass_mark)
                        {
                            $arr[] = $file->modal_id;
                        }
                    }
                    if(!empty($file->assignment_id))
                    {
                        if(empty($file->assignment->submission) || !empty($file->assignment->submission) && $file->assignment->submission->status != 1)
                        {
                            $arr[] = $file->modal_id;
                        }
                    }
                }
            }
        }
        $result = [];
        if(!empty($arr))
        {
            $arr = array_unique($arr);
            $first = $arr[0];
            $result = array_filter($ids, function($item) use ($first) {
                return $item > $first;
            });
        }
        $settings = getSettings();
        $classes = ['bg-success-light', 'bg-info-light', 'bg-primary-light', 'bg-warning-light', 'bg-danger-light', 'bg-light-light', 'bg-dark-light', 'bg-success-dark'];
        $headers = ['bg-info', 'bg-primary', 'bg-warning', 'bg-danger', 'bg-dark', 'bg-success'];
        return view('web.lessons',compact('follow','lesson','settings','result', 'classes', 'headers'));
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
