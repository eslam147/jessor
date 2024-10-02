<?php

namespace App\Http\Controllers\Api;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Exam;
use App\Models\File;
use App\Models\User;
use App\Models\Event;
use App\Models\Shift;
use Razorpay\Api\Api;
use App\Models\Lesson;
use App\Models\Slider;
use App\Models\Holiday;
use App\Models\Parents;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ChatFile;
use App\Models\FeesPaid;
use App\Models\Settings;
use App\Models\Students;
use Stripe\StripeClient;
use App\Models\ExamClass;
use App\Models\FeesClass;
use App\Models\Timetable;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\ExamResult;
use App\Models\OnlineExam;
use App\Models\ChatMessage;
use App\Models\ClassSchool;
use App\Models\LessonTopic;
use App\Models\ReadMessage;
use App\Models\SessionYear;
use App\Models\Announcement;
use App\Models\ClassSection;
use App\Models\ClassTeacher;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ExamTimetable;
use App\Models\MultipleEvent;
use App\Models\FeesChoiceable;
use App\Models\InstallmentFee;
use App\Models\StudentSubject;
use App\Models\SubjectTeacher;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UserNotification;
use App\Models\PaidInstallmentFee;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Services\Coupon\CouponService;
use App\Models\OnlineExamStudentAnswer;
use App\Models\StudentOnlineExamStatus;
use App\Models\OnlineExamQuestionAnswer;
use App\Models\OnlineExamQuestionChoice;
use App\Models\OnlineExamQuestionOption;
use Illuminate\Support\Facades\Validator;
use App\Services\Auth\RegisterAuthService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\Purchase\PurchaseService;
use App\Http\Resources\TimetableCollection;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\Student\ClassSchoolResource;
use App\Http\Resources\Student\Lesson\LessonResource;
use App\Http\Resources\Student\Teacher\TeacherResource;
use App\Http\Resources\Student\LessonTopic\LessonTopicResource;
use App\Services\Auth\LoginService;

class StudentApiController extends Controller
{
    public function __construct(
        protected LoginService $loginService,
        protected RegisterAuthService $registerAuthService,
        private CouponService $couponService,
        private PurchaseService $purchaseService
    ) {
    }
    public function getClassSections()
    {
        $classSections = ClassSchool::get();

        return response()->json([
            'error' => false,
            'message' => 'Retrived Successfully!',
            'data' => ClassSchoolResource::collection($classSections),
            'code' => 100,
        ], Response::HTTP_OK);

    }
    public function register(RegisterRequest $request)
    {
        try {
            #TODO Otp

            $fatherId = null;
            $motherId = null;
            $guardianId = null;

            DB::beginTransaction();
            if (! empty($request->parent)) {
                $parents = $this->registerAuthService->storeParents($request);
                $fatherId = $parents['father']->id;
                $motherId = $parents['mother']->id;
            }

            if (! empty($request->guardian)) {
                $guardian = $this->registerAuthService->storeGuardian($request);
                $guardianId = $guardian->id;
            }

            $student = $this->registerAuthService->storeStudent(
                request: $request,
                fatherId: $fatherId,
                motherId: $motherId,
                guardianId: $guardianId
            );
            DB::commit();

            return response()->json([
                'error' => false,
                'message' => 'Student Created Successfully!',
                'data' => [
                    'user' => $student,
                ],
                'code' => 100,
            ], Response::HTTP_CREATED);
            // ------------------------------------------------------------- \\

        } catch (Exception $e) {
            DB::rollback();
            report($e);

            $execptionResponse = [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => []
            ];

            return response()->json($execptionResponse, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // ------------------------------------------------------------- \\

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gr_number' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }



        if (Auth::attempt(['email' => $request->gr_number, 'password' => $request->password])) {

            $session_year_id = settingByType('session_year');
            $compulsory_fees_mode = settingByType('compulsory_fee_payment_mode') ?? 0;

            $session_year = SessionYear::where('id', $session_year_id)->first();
            $isInstallment = $session_year->include_fee_installments;

            $due_date = $session_year->fee_due_date;
            $free_app_use_date = $session_year->free_app_use_date;

            $current_date = now()->toDateString();
            //        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            //Here Email Field is referenced as a GR Number for Student
            $auth = Auth::user();
            if (! $auth->hasRole('Student')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid Login Credentials',
                    'code' => 101

                ], 200);
            }
            // $this->loginService->handleDeviceLimit($auth);

            $token = $auth->createToken($auth->first_name)->plainTextToken;
            $user = $auth->load(['student.class_section', 'student.category']);

            if ($request->fcm_id) {
                $auth->fcm_id = $request->fcm_id;
                $auth->save();
            }

            if ($request->device_type) {
                $auth->device_type = $request->device_type;
                $auth->save();
            }
            $classSection = optional($user->student->class_section);
            $classSectionName = $classSection->class?->name . " " . $classSection->section?->name;

            // Set Class Section name
            $streamName = $classSection->class->streams->name ?? null;
            if (! is_null($streamName)) {
                $user->class_section_name = "{$classSectionName} {$streamName}";
            } else {
                $user->class_section_name = $classSectionName;
            }

            //Set Medium name
            $user->medium_name = $classSection?->class?->medium->name;


            //Set Shift name
            $user->shift_id = $classSection->class->shifts->id ?? '';
            $user->shift = Shift::find($user->shift_id);
            // if ($user->shift) {
            //     $user->shift->id;
            //     $user->shift->title;
            //     $user->shift->start_time;
            // }


            // $user->dynamic_field = $dynamicFields;
            unset($user->student->class_section);

            //Set Category name
            $user->category_name = $user->student->category->name;
            unset($user->student->category);
            $class_id = $user->student->class_section?->class_id;

            if ($compulsory_fees_mode == 1) {
                if (isset($free_app_use_date)) {
                    if ($current_date >= $free_app_use_date) {
                        $user->is_fee_payment_due = 1;
                    } else {
                        $user->is_fee_payment_due = 0;
                    }
                } else {
                    $fees_paid = FeesPaid::where('student_id', $user->student->id)->where('session_year_id', $session_year_id)->first();

                    if ($isInstallment == 0) {
                        // Non-installment case
                        if (isset($fees_paid) && $fees_paid->is_fully_paid == 0) {
                            $user->is_fee_payment_due = ($current_date >= $due_date) ? 1 : 0;
                        } else {
                            $user->is_fee_payment_due = 0;
                        }
                    } else {

                        if (isset($fees_paid) && $fees_paid->is_fully_paid == 1) {
                            $user->is_fee_payment_due = 0;
                        } else {
                            // Installment case
                            $installment_db = InstallmentFee::where('session_year_id', $session_year_id);
                            if ($installment_db->count()) {
                                $installment_db_data = $installment_db->get();
                                foreach ($installment_db_data as $data) {
                                    $paid_installment_data = PaidInstallmentFee::where(['student_id' => $user->student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'installment_fee_id' => $data['id'], 'status' => 1])->first();
                                    $installment_data[] = array(
                                        'id' => $data->id,
                                        'name' => $data->name,
                                        'due_date' => date('Y-m-d', strtotime($data->due_date)),
                                        'due_charges' => $data->due_charges,
                                        'is_paid' => $paid_installment_data->status ?? 0,
                                    );
                                }
                            }
                            // Find the first unpaid installment and set its due date
                            foreach ($installment_data as $data) {
                                if ($data['is_paid'] == 0) {
                                    $due_date = $data['due_date'];
                                    break; // Stop after the first unpaid installment
                                }
                            }
                            $user->is_fee_payment_due = ($current_date >= $due_date) ? 1 : 0;
                        }

                    }
                }


            } else {
                $user->is_fee_payment_due = 0;
            }



            $dynamicFields = null;
            $dynamicField = $user->student->dynamic_fields;
            $user = flattenMyModel($user);
            if (! empty($dynamicField)) {
                $data = json_decode($dynamicField, true);
                if (is_array($data)) {
                    foreach ($data as $item) {
                        if ($item != null) {
                            foreach ($item as $key => $value) {
                                $dynamicFields[$key] = $value;
                            }
                        }
                    }
                } else {
                    $dynamicFields = $data;
                }
            } else {
                $dynamicFields = null;
            }


            $data = array_merge($user, ['dynamic_fields' => $dynamicFields]);

            $response = array(
                'error' => false,
                'message' => 'User logged-in!',
                'token' => $token,
                'data' => $data,
                'code' => 100,
            );
            return response()->json($response, 200);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid Login Credentials',
                'code' => 101
            ], 200);
        }
    }

    public function dashboard(Request $request)
    {
        try {
            $student = $request->user()->student;
            $date = now();
            $day = $date->format('l');
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $sliders = Slider::where('type', 1)->orWhere('type', 3)->get()->toArray();
            $subjects = $student->subjects();

            if ($subjects['elective_subject'] == []) {
                $subjects['elective_subject'] = null;
            }

            $announcements = Announcement::where('table_type', "")->where('session_year_id', $session_year_id)->latest()->limit(3)->get();



            $student_subject = $student->subjects();
            $class_subject = $student->classSubjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }


            $subject_id = array_merge($core_subjects, $elective_subjects);

            $assignments = Assignment::where('class_section_id', $student->class_section_id)
                ->whereIn('subject_id', $subject_id)
                ->whereDoesntHave('submission', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })
                ->where('due_date', '>', $date)
                ->with('subject')
                ->orderBy('due_date', 'asc')
                ->limit(2)
                ->get();

            $timetables = Timetable::where('class_section_id', $student->class_section_id)->where('day_name', $day)
                ->whereHas('subject_teacher', function ($q) use ($subject_id) {
                    $q->whereIn('subject_id', $subject_id);
                })->orderBy('start_time', 'asc')->get();


            $class_id = $student->class_section?->class_id;

            $exam_data_db = Exam::with([
                'timetable' => function ($q) use ($request, $class_id, $subject_id) {
                    $q->where('class_id', $class_id)->whereIn('subject_id', $subject_id)->with(['subject'])->orderby('date');
                }
            ])->limit(10)->get();

            foreach ($exam_data_db as $data) {
                $exam_timetable = [];
                $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where(['exam_id' => $data->id, 'class_id' => $class_id])->first();
                $starting_date = $starting_date_db['min(date)'];
                $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where(['exam_id' => $data->id, 'class_id' => $class_id])->first();
                $ending_date = $ending_date_db['max(date)'];
                $currentTime = Carbon::now();
                $current_date = date($currentTime->toDateString());
                if ($current_date >= $starting_date && $current_date <= $ending_date) {
                    $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } elseif ($current_date < $starting_date) {
                    $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } else {
                    $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
                }

                foreach ($data->timetable as $item) {
                    if ($date <= $item->end_time) {
                        $exam_timetable[] = array(
                            'id' => $item->id,
                            'total_marks' => $item->total_marks,
                            'passing_marks' => $item->passing_marks,
                            'date' => $item->date,
                            'starting_time' => $item->start_time,
                            'ending_time' => $item->end_time,
                            'subject' => array(
                                'id' => $item->subject->id,
                                'name' => $item->subject->name,
                                'bg_color' => $item->subject->bg_color,
                                'image' => $item->subject->image,
                                'type' => $item->subject->type,
                            )
                        );
                    }

                }
                if ($exam_status != 2) {
                    $exam_data[] = array(
                        'id' => $data->id,
                        'name' => $data->name,
                        'description' => $data->description,
                        'publish' => $data->publish,
                        'session_year' => $data->session_year->name,
                        'exam_starting_date' => $starting_date,
                        'exam_ending_date' => $ending_date,
                        'exam_status' => $exam_status,
                        'exam_timetable' => $exam_timetable
                    );
                }
            }

            $events = Event::where(function ($query) use ($date) {
                $query->where('start_date', '>=', $date)->orWhere('end_date', '>=', $date)
                    ->orWhereDate('start_date', '=', $date)->orWhere('end_date', '=', $date); // Adding this condition to include events with start_date equal to $date
            })
                ->orderBy('start_date')
                ->limit(3)
                ->get();

            foreach ($events as $row) {
                if ($row->type == 'multiple') {
                    $hasdaySchedule = MultipleEvent::where('event_id', $row->id)->first();
                    if ($hasdaySchedule) {
                        $event[] = [
                            'id' => $row->id,
                            'has_day_schedule' => 1,
                            'title' => $row->title,
                            'type' => $row->type,
                            'start_date' => $row->start_date,
                            'end_date' => $row->end_date,
                            'start_time' => $row->start_time,
                            'end_time' => $row->end_time,
                            'image' => $row->image,
                            'description' => $row->description,
                        ];
                    } else {
                        $event[] = [
                            'id' => $row->id,
                            'has_day_schedule' => 0,
                            'title' => $row->title,
                            'type' => $row->type,
                            'start_date' => $row->start_date,
                            'end_date' => $row->end_date,
                            'start_time' => $row->start_time,
                            'end_time' => $row->end_time,
                            'image' => $row->image,
                            'description' => $row->description,
                        ];
                    }

                } else {
                    $event[] = [
                        'id' => $row->id,
                        'has_day_schedule' => 0,
                        'title' => $row->title,
                        'type' => $row->type,
                        'start_date' => $row->start_date,
                        'end_date' => $row->end_date,
                        'start_time' => $row->start_time,
                        'end_time' => $row->end_time,
                        'image' => $row->image,
                        'description' => $row->description,
                    ];
                }

            }

            $data = array(
                'sliders' => $sliders,
                'subjects' => $subjects,
                'announcements' => $announcements,
                'assignments' => $assignments,
                'timetables' => new TimetableCollection($timetables),
                'exams' => isset($exam_data) ? $exam_data : [],
                'events' => isset($event) ? $event : []
            );
            $response = array(
                'error' => false,
                'message' => 'Data Fetched Successfully.',
                'data' => (object) $data,
                'code' => 200,
            );
            return response()->json($response, 200);

        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gr_no' => 'required',
            'dob' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $get_id = Students::select('user_id')->where('admission_no', $request->gr_no)->pluck('user_id')->first();
            if (isset($get_id) && ! empty($get_id)) {

                $user = User::where('id', $get_id)->whereDate('dob', '=', date('Y-m-d', strtotime($request->dob)))->first();
                if ($user) {
                    $user->reset_request = 1;
                    $user->save();
                    $response = array(
                        'error' => false,
                        'message' => "Request Send Successfully",
                        'code' => 200,
                    );
                } else {
                    $response = [
                        'error' => true,
                        'message' => "Invalid user Details",
                        'code' => 107,
                    ];
                }
            } else {
                $response = array(
                    'error' => true,
                    'message' => "Invalid user Details",
                    'code' => 107,
                );
            }
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function subjects(Request $request)
    {
        try {
            $user = $request->user();
            $subjects = $user->student->subjects();

            if ($subjects['elective_subject'] == []) {
                $subjects['elective_subject'] = null;
            }

            $response = array(
                'error' => false,
                'message' => 'Student Subject Fetched Successfully.',
                'data' => $subjects,
                'code' => 200,
            );
            return response()->json($response, 200);
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function classSubjects(Request $request)
    {
        try {
            $user = $request->user();
            $subjects = $user->student->classSubjects();
            $response = array(
                'error' => false,
                'message' => 'Class Subject Fetched Successfully.',
                //                'data' => new ClassSubjectCollection($subjects),
                'data' => $subjects,
                'code' => 200
            );
            return response()->json($response, 200);
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103
            );
            return response()->json($response, 200);
        }
    }

    public function selectSubjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_group.*.id' => 'required',
            'subject_group.*.subject_id' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $student = $request->user()->student;
            $class_section = $student->class_section;
            $student_subject = [];
            $session_year_id = Settings::select('message')->where('type', 'session_year')->pluck('message')->first();
            foreach ($request->subject_group as $key => $subject_group) {
                $subject_group_id = $subject_group['id'];
                foreach ($subject_group['subject_id'] as $subject_id) {

                    $if_subject_already_selected = StudentSubject::where([
                        'student_id' => $student->id,
                        'subject_id' => $subject_id,
                        'class_section_id' => $class_section->id,
                        'session_year_id' => intval($session_year_id)
                    ])->first();
                    if (! $if_subject_already_selected) {
                        $student_subject[] = array(
                            'student_id' => $student->id,
                            'subject_id' => $subject_id,
                            'class_section_id' => $class_section->id,
                            'session_year_id' => intval($session_year_id)
                        );
                    }
                }
            }
            StudentSubject::insert($student_subject);

            $response = array(
                'error' => false,
                'message' => "Subject Selected Successfully",
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getParentDetails(Request $request)
    {
        try {
            $student = $request->user()->student->load(['father', 'mother', 'guardian']);
            $parentDynamicFields = [];
            if ($student->father != null) {
                // father data
                $data = json_decode($student->father->dynamic_fields, true);
                $father = $student->father->toArray();

                if (is_array($data)) {
                    foreach ($data as $item) {
                        if ($item != null) {
                            foreach ($item as $key => $value) {
                                $parentDynamicFields[$key] = $value;
                            }
                        }
                    }
                } else {
                    $parentDynamicFields = $data;
                }
                $dynamic_fields = (array) $parentDynamicFields;
                $father = array_merge($father, ['dynamic_fields' => ! empty($dynamic_fields) ? $dynamic_fields : null]);
            }

            if ($student->mother != null) {
                //mother data
                $data = json_decode($student->mother->dynamic_fields, true);
                $mother = $student->mother->toArray();

                if (is_array($data)) {
                    foreach ($data as $item) {
                        if ($item != null) {
                            foreach ($item as $key => $value) {
                                $parentDynamicFields[$key] = $value;
                            }
                        }
                    }
                } else {
                    $parentDynamicFields = $data;
                }
                $dynamic_fields = (array) $parentDynamicFields;
                $mother = array_merge($mother, ['dynamic_fields' => ! empty($dynamic_fields) ? $dynamic_fields : null]);

            }

            if ($student->guardian != null) {
                //guardian data
                $data = json_decode($student->guardian->dynamic_fields, true);
                $guardian = $student->guardian->toArray();

                if (is_array($data)) {
                    foreach ($data as $item) {
                        if ($item != null) {
                            foreach ($item as $key => $value) {
                                $parentDynamicFields[$key] = $value;
                            }
                        }
                    }
                } else {
                    $parentDynamicFields = $data;
                }

                $dynamic_fields = (array) $parentDynamicFields;
                $guardian = array_merge($guardian, ['dynamic_fields' => ! empty($dynamic_fields) ? $dynamic_fields : null]);

            }

            if ($student->father != null && $student->mother != null && $student->guardian != null) {
                $data = array(
                    'father' => (! empty($student->father)) ? $father : (object) [],
                    'mother' => (! empty($student->mother)) ? $mother : (object) [],
                    'guardian' => (! empty($student->guardian)) ? $guardian : (object) []
                );
            } elseif ($student->father != null && $student->mother != null) {
                $data = array(
                    'father' => (! empty($student->father)) ? $father : (object) [],
                    'mother' => (! empty($student->mother)) ? $mother : (object) [],
                );
            } else {
                $data = array(
                    'guardian' => (! empty($student->guardian)) ? $guardian : (object) []
                );
            }

            $response = array(
                'error' => false,
                'message' => "Parent Details Fetched Successfully",
                'data' => $data,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getTimetable(Request $request)
    {
        try {

            $student = $request->user()->student;
            $student_subject = $student->subjects();
            // $class_subject = $student->classSubjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }


            $subject_id = array_merge($core_subjects, $elective_subjects);

            $timetables = Timetable::where('class_section_id', $student->class_section_id)->with([
                'subject_teacher' => function ($q) use ($subject_id) {
                    $q->whereIn('subject_id', $subject_id);
                }
            ])->orderBy('day', 'asc')->orderBy('start_time', 'asc')->get();

            $new_timetable = [];
            foreach ($timetables as $timetable) {
                if ($timetable->subject_teacher != null) {
                    $new_timetable[] = $timetable;
                }
            }

            $response = array(
                'error' => false,
                'message' => "Timetable Fetched Successfully",
                'data' => new TimetableCollection($new_timetable),
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    /**
     * @param
     * subject_id : 2
     * lesson_id : 1 //OPTIONAL
     */
    public function getLessons(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'nullable|numeric',
            'teacher_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $user = $request->user();
            $user->load(['student']);

            $studentInfo = $user->student;

            $data = Lesson::where('teacher_id', $request->teacher_id)
                ->active()
                ->where('subject_id', $request->subject_id)
                ->relatedToCurrentStudentClass($studentInfo)
                ->with('topic.file', 'file', 'subject', 'class');

            $data = $data->addSelect([
                'is_enrolled' => Enrollment::whereColumn('lesson_id', 'lessons.id')
                    ->select('id')
                    ->activeEnrollments($user->id),
            ])->get();

            $response = [
                'error' => false,
                'message' => "Lessons Fetched Successfully",
                'data' => LessonResource::collection($data),
                'code' => 200,
            ];

        } catch (Exception $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }
    /**
     * @param
     * subject_id : 2
     * lesson_id : 1 //OPTIONAL
     */
    public function getEnrollmentLessons(Request $request)
    {
        try {
            $data = Lesson::whereHas('enrollments', function ($q) {
                return $q->where('user_id', auth('api')->id());
            })->with('topic', 'file');

            $data = $data->active()->get();

            $response = [
                'error' => false,
                'message' => "Lessons Fetched Successfully",
                'data' => $data,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    /**
     * return all teachers belong to the Subject
     */
    public function getTeachers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);

        }

        try {
            $student = $request->user()->student;
            //----------------------------------------- \\
            $data = Teacher::whereHas('subjects', function ($q) use ($request, $student) {
                return $q->where('subject_id', $request->subject_id)->where('class_section_id', $student->class_section_id);
            })->with('user')
                ->withCount([
                    'lessons' => fn($q) => $q->active()->where('subject_id', $request->subject_id)->relatedToCurrentStudentClass($student),
                    'lessonTopics' => fn($q) => $q->whereHas('lesson', fn($q) => $q->active()->where('subject_id', $request->subject_id)->relatedToCurrentStudentClass($student)),
                ])->get();
            //----------------------------------------- \\

            $response = [
                'error' => false,
                'message' => "Teacher Fetched Successfully",
                'data' => TeacherResource::collection($data),
                'code' => 200,
            ];

            //----------------------------------------- \\
        } catch (Exception $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    /**
     * @param
     * lesson_id : 1
     * topic_id : 1    //OPTIONAL
     */
    public function getLessonTopics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|numeric',
            'topic_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }

        try {
            //$student = $request->user()->student;
            $data = LessonTopic::where('lesson_id', $request->lesson_id)->with('file');
            if ($request->topic_id) {
                $data->where('id', $request->topic_id);
            }
            $data = $data->get();

            $response = [
                'error' => false,
                'message' => "Topics Fetched Successfully",
                'data' => LessonTopicResource::collection($data),
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    /**
     * @param
     * assignment_id : 1    //OPTIONAL
     * subject_id : 1       //OPTIONAL
     */
    public function getAssignments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'nullable|numeric',
            'subject_id' => 'nullable|numeric',
            'is_submitted' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = [
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ];
            return response()->json($response);
        }

        try {
            $student = $request->user()->student;
            $student_subject = $student->subjects();
            $class_subject = $student->classSubjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }


            $subject_id = array_merge($core_subjects, $elective_subjects);

            $data = Assignment::where('class_section_id', $student->class_section_id)->whereIn('subject_id', $subject_id)->with([
                'file',
                'subject',
                'submission' => function ($q) use ($student) {
                    $q->where('student_id', $student->id)->with('file');
                }
            ]);

            if ($request->assignment_id) {
                $data->where('id', $request->assignment_id);
            }
            if ($request->subject_id) {
                $data->where('subject_id', $request->subject_id);
            }
            if (isset($request->is_submitted)) {
                if ($request->is_submitted == 1) {
                    $data->whereHas('submission', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                } else if ($request->is_submitted == 0) {
                    $data->whereDoesntHave('submission', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                }
            }
            $data = $data->orderBy('id', 'desc')->paginate();

            $response = [
                'error' => false,
                'message' => "Assignments Fetched Successfully",
                'data' => $data,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                // 'message' => trans('error_occurred'),
                'message' => trans($e->getMessage()),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    /**
     * @param
     * assignment_id : 1    //OPTIONAL
     * subject_id : 1       //OPTIONAL
     */
    public function submitAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|numeric',
            'subject_id' => 'nullable|numeric',
            'files' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }

        try {
            $student = $request->user()->student;
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $assignment = Assignment::where('id', $request->assignment_id)->where('class_section_id', $student->class_section_id)->firstOrFail();
            $assignment_submission = AssignmentSubmission::where('assignment_id', $request->assignment_id)->where('student_id', $student->id)->first();
            if (empty($assignment_submission)) {
                $assignment_submission = new AssignmentSubmission();
                $assignment_submission->assignment_id = $request->assignment_id;
                $assignment_submission->student_id = $student->id;
                $assignment_submission->session_year_id = $session_year_id;
            } else if ($assignment_submission->status == 2 && $assignment->resubmission) {
                // if assignment submission is rejected and
                // Assignment has resubmission allowed then change the status to resubmitted
                $assignment_submission->status = 3;
                if ($assignment_submission->file) {
                    foreach ($assignment_submission->file as $file) {
                        if (Storage::disk('public')->exists($file->file_url)) {
                            Storage::disk('public')->delete($file->file_url);
                        }
                    }
                }
                $assignment_submission->file()->delete();
            } else {
                $response = [
                    'error' => true,
                    'message' => "You already have submitted your assignment.",
                    'code' => 104
                ];
                return response()->json($response);
            }

            $subject_teacher_id = SubjectTeacher::where('class_section_id', $student->class_section_id)->where('subject_id', $assignment->subject_id)->pluck('teacher_id');
            $user = Teacher::whereIn('id', $subject_teacher_id)->pluck('user_id');
            $title = 'New submission';
            $body = $student->user->full_name . ' submitted their assignment.';
            $type = 'assignment_submission';
            $image = null;
            $userinfo = null;

            $notification = Notification::create([
                'send_to' => 2,
                'title' => $title,
                'message' => $body,
                'type' => $type,
                'date' => now(),
                'is_custom' => 0,
            ]);

            foreach ($user as $data) {
                $user_notification = new UserNotification();
                $user_notification->notification_id = $notification->id;
                $user_notification->user_id = $data;
                $user_notification->save();
            }
            $assignment_submission->save();
            send_notification($user, $title, $body, $type, $image, $userinfo);

            foreach ($request->file('files') as $key => $image) {
                $file = new File();
                $file->file_name = $image->getClientOriginalName();
                $file->modal()->associate($assignment_submission);
                $file->type = 1;
                $file->file_url = $image->store('assignment', 'public');
                $file->save();
            }

            $submitted_assignment = AssignmentSubmission::where('id', $assignment_submission->id)->with('file')->get();
            $response = [
                'error' => false,
                'message' => "Assignments Submitted Successfully",
                'data' => $submitted_assignment,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    /**
     * @param
     * assignment_id : 1    //OPTIONAL
     * subject_id : 1       //OPTIONAL
     */
    public function deleteAssignmentSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_submission_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }

        try {
            $student = $request->user()->student;
            $assignment_submission = AssignmentSubmission::where('id', $request->assignment_submission_id)->where('student_id', $student->id)->with('file')->first();

            if (! empty($assignment_submission) && $assignment_submission->status == 0) {
                foreach ($assignment_submission->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }
                $assignment_submission->file()->delete();
                $assignment_submission->delete();
                $response = array(
                    'error' => false,
                    'message' => "Assignments Deleted Successfully",
                    'code' => 200,
                );
            } else {
                $response = array(
                    'error' => true,
                    'message' => "You can not delete assignment",
                    'code' => 110,
                );
            }
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    /**
     * @param
     * month : 4 //OPTIONAL
     * year : 2022 //OPTIONAL
     */
    public function getAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|numeric',
            'year' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;
            $session_year_id = settingByType('session_year');

            $attendance = Attendance::where('student_id', $student->id)->where('session_year_id', $session_year_id);
            $holidays = new Holiday;
            $session_year_data = SessionYear::find($session_year_id);
            if (isset($request->month)) {
                $attendance = $attendance->whereMonth('date', $request->month);
                $holidays = $holidays->whereMonth('date', $request->month);
            }

            if (isset($request->year)) {
                $attendance = $attendance->whereYear('date', $request->year);
                $holidays = $holidays->whereYear('date', $request->year);
            }
            $attendance = $attendance->get();
            $holidays = $holidays->get();


            $response = [
                'error' => false,
                'message' => "Attendance Details Fetched Successfully",
                'data' => ['attendance' => $attendance, 'holidays' => $holidays, 'session_year' => $session_year_data],
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    public function getAnnouncements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:subject,noticeboard,class',
            'subject_id' => 'required_if:type,subject|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $student = $request->user()->student;
            $class_id = $student->class_section?->class->id;
            $session_year_id = getSettings('session_year')['session_year'];
            $table = null;
            if (isset($request->type) && $request->type == "subject") {
                $table = SubjectTeacher::where('class_section_id', $student->class_section_id)->where('subject_id', $request->subject_id)->get()->pluck('id');
                if (empty($table)) {
                    return response()->json([
                        'error' => true,
                        'message' => "Invalid Subject ID",
                        'code' => 106,
                    ]);
                }
            }

            $data = Announcement::with('file')->where('session_year_id', $session_year_id)->latest();

            if (isset($request->type) && $request->type == "noticeboard") {
                $data = $data->where('table_type', "");
            }

            if (isset($request->type) && $request->type == "class") {
                $data = $data->where('table_type', ClassSchool::class)->where('table_id', $class_id);
            }

            if (isset($request->type) && $request->type == "subject") {
                $data = $data->where('table_type', SubjectTeacher::class)->whereIn('table_id', $table);
            }

            $data = $data->orderByDesc('id')->paginate();
            $response = [
                'error' => false,
                'message' => "Announcement Details Fetched Successfully",
                'data' => $data,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    public function getExamList(Request $request)
    {
        try {

            $student_id = Auth::user()->student->id;
            $student = Students::with('class_section')->where('id', $student_id)->first();
            $student_subject = $student->subjects();
            $class_id = $student->class_section?->class_id;

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id') ?? [];
            // dd($core_subjects);
            $elective_subjects = $student_subject["elective_subject"] ?? [];
            // dd($elective_subjects);
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }

            $subject_id = array_merge($core_subjects, $elective_subjects);

            $exam_data_db = ExamClass::with('exam.session_year:id,name', 'exam.timetable')
                ->where('class_id', $class_id)
                ->whereHas('exam.timetable', function ($query) use ($subject_id) {
                    $query->whereIn('subject_id', $subject_id);
                })->get();

            foreach ($exam_data_db as $data) {
                // date status
                $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where(['exam_id' => $data->exam->id, 'class_id' => $class_id])->first();
                $starting_date = $starting_date_db['min(date)'];
                $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where(['exam_id' => $data->exam->id, 'class_id' => $class_id])->first();
                $ending_date = $ending_date_db['max(date)'];
                $currentTime = Carbon::now();
                $current_date = date($currentTime->toDateString());
                if ($current_date >= $starting_date && $current_date <= $ending_date) {
                    $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } elseif ($current_date < $starting_date) {
                    $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } else {
                    $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
                }

                if (isset($request->status)) {
                    if ($request->status == 0) {
                        $exam_data[] = array(
                            'id' => $data->exam->id,
                            'name' => $data->exam->name,
                            'description' => $data->exam->description,
                            'publish' => $data->exam->publish,
                            'session_year' => $data->exam->session_year->name,
                            'exam_starting_date' => $starting_date,
                            'exam_ending_date' => $ending_date,
                            'exam_status' => $exam_status,
                        );
                    } else if ($request->status == 1) {
                        if ($exam_status == 0) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->exam->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                            );
                        }
                    } else if ($request->status == 2) {
                        if ($exam_status == 1) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->exam->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                            );
                        }
                    } else {
                        if ($exam_status == 2) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->exam->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                            );
                        }
                    }
                } else {
                    $exam_data[] = array(
                        'id' => $data->exam->id,
                        'name' => $data->exam->name,
                        'description' => $data->exam->description,
                        'publish' => $data->exam->publish,
                        'session_year' => $data->exam->session_year->name,
                        'exam_starting_date' => $starting_date,
                        'exam_ending_date' => $ending_date,
                        'exam_status' => $exam_status,
                    );
                }
            }

            $response = [
                'error' => false,
                'data' => isset($exam_data) ? $exam_data : [],
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    public function getExamDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|nullable',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student_id = Auth::user()->student->id;
            $student = Students::with('class_section')->where('id', $student_id)->first();
            $student_subject = $student->subjects();
            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] == null ? [] : $student_subject["elective_subject"]->pluck('subject_id')->toArray();

            $subject_id = array_merge($core_subjects, $elective_subjects);

            $class_id = $student->class_section->class_id;
            $exam_data_db = Exam::with([
                'timetable' => function ($q) use ($request, $class_id, $subject_id) {
                    $q->where(['exam_id' => $request->exam_id, 'class_id' => $class_id])->whereIn('subject_id', $subject_id)->with(['subject'])->orderby('date');
                }
            ])->where('id', $request->exam_id)->first();


            if (! $exam_data_db) {
                $response = array(
                    'error' => false,
                    'data' => [],
                    'code' => 200,
                );
                return response()->json($response);
            }


            foreach ($exam_data_db->timetable as $data) {
                $exam_data[] = array(
                    'id' => $data->id,
                    'total_marks' => $data->total_marks,
                    'passing_marks' => $data->passing_marks,
                    'date' => $data->date,
                    'starting_time' => $data->start_time,
                    'ending_time' => $data->end_time,
                    'subject' => array(
                        'id' => $data->subject->id,
                        'name' => $data->subject->name,
                        'bg_color' => $data->subject->bg_color,
                        'image' => $data->subject->image,
                        'type' => $data->subject->type,
                    )
                );
            }
            $response = array(
                'error' => false,
                'data' => isset($exam_data) ? $exam_data : [],
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getExamMarks(Request $request)
    {
        try {
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $student = $request->user()->student;
            $class_data = Students::where('id', $student->id)->with('class_section.class.medium', 'class_section.section')->get()->first();

            $exam_result_db = ExamResult::with([
                'student' => function ($q) {
                    $q->select('id', 'user_id', 'roll_number')->with('user:id,first_name,last_name');
                }
            ])->with('exam', 'session_year')->with([
                        'exam.marks' => function ($q) use ($student) {
                            $q->where('student_id', $student->id);
                        }
                    ])->where('student_id', $student->id)->get();



            if (sizeof($exam_result_db)) {
                foreach ($exam_result_db as $exam_result_data) {
                    $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where(['exam_id' => $exam_result_data->exam_id, 'class_id' => $class_data->class_section->class_id, 'session_year_id' => $session_year_id])->first();
                    $starting_date = $starting_date_db['min(date)'];

                    $exam_result = array(
                        'result_id' => $exam_result_data->id,
                        'exam_id' => $exam_result_data->exam_id,
                        'exam_name' => $exam_result_data->exam->name,
                        'class_name' => $class_data->class_section->class->name . '-' . $class_data->class_section->section->name . ' ' . $class_data->class_section->class->medium->name,
                        'student_name' => $exam_result_data->student->user->first_name . ' ' . $exam_result_data->student->user->last_name,
                        'exam_date' => $starting_date,
                        'total_marks' => $exam_result_data->total_marks,
                        'obtained_marks' => $exam_result_data->obtained_marks,
                        'percentage' => $exam_result_data->percentage,
                        'grade' => $exam_result_data->grade,
                        'session_year' => $exam_result_data->session_year->name,
                    );
                    $exam_marks = array();
                    foreach ($exam_result_data->exam->marks as $marks) {
                        $exam_marks[] = array(
                            'marks_id' => $marks->id,
                            'subject_name' => $marks->subject->name,
                            'subject_type' => $marks->subject->type,
                            'total_marks' => $marks->timetable->total_marks,
                            'passing_marks' => $marks->timetable->passing_marks,
                            'obtained_marks' => $marks->obtained_marks,
                            'teacher_review' => $marks->teacher_review,
                            'grade' => $marks->grade,
                        );
                    }
                    $data[] = array(
                        'result' => $exam_result,
                        'exam_marks' => $exam_marks,
                    );
                }

                $response = array(
                    'error' => false,
                    'message' => "Exam Result Fetched Successfully",
                    'data' => $data,
                    'code' => 200,
                );
            } else {
                $response = array(
                    'error' => false,
                    'message' => "Exam Result Fetched Successfully",
                    'data' => [],
                    'code' => 200,
                );
            }
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $date = now()->setTimezone('UTC');
            $student = $request->user()->student;

            $student_subject = $student->subjects();
            $class_subject = $student->classSubjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }

            $subject_id = array_merge($core_subjects, $elective_subjects);

            $class_section_id = $student->class_section->id;
            $class_id = $student->class_section->class_id;
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            //get current
            $time_data = now()->toArray();
            $current_date_time = $time_data['formatted'];

            // checks the subject id param is passed or not .
            // query meets the condition for both class section and class
            if (isset($request->subject_id) && ! empty($request->subject_id)) {
                $exam_data_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->has('question_choice')->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $session_year_id, $current_date_time, $student, $request) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->orderby('start_date')->paginate(15)->toArray();
            } else {
                $exam_data_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->whereIn('subject_id', $subject_id)->has('question_choice')->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $subject_id, $session_year_id, $current_date_time, $student) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'session_year_id' => $session_year_id, ['end_date', '>=', $current_date_time]])->whereIn('subject_id', $subject_id)->with('subject')->whereDoesntHave('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->orderby('start_date')->paginate(15)->toArray();
            }

            if (isset($exam_data_db) && ! empty($exam_data_db)) {

                $exam_data = array();
                $exam_list = array();
                // making the array of exam data
                foreach ($exam_data_db['data'] as $data) {

                    // total marks of exams
                    $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                    $total_marks = $total_marks['sum(marks)'];

                    if ($data['model_type'] == 'App\Models\ClassSection') {
                        $class_section_data = ClassSection::where('id', $data['model_id'])->with('class.medium', 'section')->first();
                        $class_name = $class_section_data->class->name . ' - ' . $class_section_data->section->name . ' ' . $class_section_data->class->medium->name;
                    } else {
                        $class_data = ClassSchool::where('id', $data['model_id'])->with('medium')->first();
                        $class_name = $class_data->name . ' ' . $class_data->medium->name;
                    }

                    if ($total_marks == null) {
                        $exam_list = [];
                    } else {
                        $exam_list[] = array(
                            'exam_id' => $data['id'],
                            'class' => array(
                                'id' => $data['model_id'],
                                'name' => $class_name
                            ),
                            'subject' => array(
                                'id' => $data['subject_id'],
                                'name' => $data['subject']['name'] . ' - ' . $data['subject']['type']
                            ),
                            'title' => $data['title'],
                            'exam_key' => $data['exam_key'],
                            'duration' => $data['duration'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'total_marks' => $total_marks,
                        );
                    }

                }

                //adding the exam data with pagination data
                $exam_data = array(
                    'current_page' => $exam_data_db['current_page'],
                    'data' => $exam_list,
                    'current_date' => $date,
                    'from' => $exam_data_db['from'],
                    'last_page' => $exam_data_db['last_page'],
                    'per_page' => $exam_data_db['per_page'],
                    'to' => $exam_data_db['to'],
                    'total' => $exam_data_db['total'],
                );
            } else {
                //if no data found
                $exam_data = null;
            }

            $response = array(
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $exam_data,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamQuestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required',
            'exam_key' => 'required',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;

            // checks Exam key
            $check_key = OnlineExam::where(['id' => $request->exam_id, 'exam_key' => $request->exam_key])->count();
            if ($check_key == 0) {
                $response = array(
                    'error' => true,
                    'message' => trans('invalid_exam_key'),
                    'code' => 103
                );
                return response()->json($response);
            }

            // checks student exam status
            $check_student_status = StudentOnlineExamStatus::where(['online_exam_id' => $request->exam_id, 'student_id' => $student->id])->count();
            if ($check_student_status != 0) {
                $response = array(
                    'error' => true,
                    'message' => trans('student_already_attempted_exam'),
                    'code' => 105
                );
                return response()->json($response);
            }

            //checks the exam started or not
            $time_data = now()->toArray();
            $current_date_time = $time_data['formatted'];
            $check_start_date = OnlineExam::where('id', $request->exam_id)->where('start_date', '>', $current_date_time)->count();
            if ($check_start_date != 0) {
                return response()->json([
                    'error' => true,
                    'message' => trans('exam_not_started_yet'),
                    'code' => 106,
                ]);
            }

            // add the exam status
            StudentOnlineExamStatus::create([
                'online_exam_id' => $request->exam_id,
                'student_id' => $student->id,
                'status' => 1,
            ]);


            // get total questions
            $total_questions = OnlineExamQuestionChoice::where('online_exam_id', $request->exam_id)->count();

            // get the questions data
            $get_exam_questions_db = OnlineExamQuestionChoice::where('online_exam_id', $request->exam_id)->with('questions')->get();
            $questions_data = array();
            $total_marks = 0;
            foreach ($get_exam_questions_db as $exam_questions) {
                $total_marks += $exam_questions->marks;

                // make options array
                $options_data = array();
                foreach ($exam_questions->questions->options as $question_options) {
                    $options_data[] = array(
                        'id' => $question_options->id,
                        'option' => htmlspecialchars_decode($question_options->option)
                    );
                }

                // make answers array
                $answers_data = array();
                foreach ($exam_questions->questions->answers as $question_answers) {
                    $answers_data[] = array(
                        'id' => $question_answers->id,
                        'option_id' => $question_answers->answer,
                        'answer' => htmlspecialchars_decode($question_answers->options->option)
                    );
                }

                // make question array
                $questions_data[] = array(
                    'id' => $exam_questions->id,
                    'question' => htmlspecialchars_decode($exam_questions->questions->question),
                    'question_type' => $exam_questions->questions->question_type,
                    'options' => $options_data,
                    'answers' => $answers_data,
                    'marks' => $exam_questions->marks,
                    'image' => $exam_questions->questions->image_url,
                    'note' => $exam_questions->questions->note,
                );
            }
            $response = array(
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $questions_data ?? null,
                'total_questions' => $total_questions,
                'total_marks' => $total_marks,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function submitOnlineExamAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'online_exam_id' => 'required|numeric',
            'answers_data' => 'required|array',
            'answers_data.*.question_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $student = $request->user()->student;
            DB::beginTransaction();
            // checks the online exam exists
            $check_online_exam_id = OnlineExam::where('id', $request->online_exam_id)->count();
            if ($check_online_exam_id) {

                $answers_exists = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->count();
                if ($answers_exists) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Answers already submitted',
                        'code' => 103,
                    ]);
                }

                foreach ($request->answers_data as $answer_data) {

                    // checks the question exists with provided exam id
                    $check_question_exists = OnlineExamQuestionChoice::where(['id' => $answer_data['question_id']])->count();
                    if ($check_question_exists) {

                        // get the question id from question choiced
                        $question_id = OnlineExamQuestionChoice::where(['id' => $answer_data['question_id'], 'online_exam_id' => $request->online_exam_id])->pluck('question_id')->first();

                        // checks the option exists with provided question
                        $check_option_exists = OnlineExamQuestionOption::where(['id' => $answer_data['option_id'], 'question_id' => $question_id])->count();

                        //get the current date
                        $currentTime = now();
                        $current_date = $currentTime->toDateString();

                        if ($check_option_exists) {
                            foreach ($answer_data['option_id'] as $options) {
                                // add the data of answers
                                OnlineExamStudentAnswer::create([
                                    'student_id' => $student->id,
                                    'online_exam_id' => $request->online_exam_id,
                                    'question_id' => $answer_data['question_id'],
                                    'option_id' => $options,
                                    'submitted_date' => $current_date,
                                ]);
                            }

                            $studentExamStatus = StudentOnlineExamStatus::where([
                                'student_id' => $student->id,
                                'online_exam_id' => $request->online_exam_id
                            ])->first();

                            if (! empty($studentExamStatus) && $studentExamStatus) {
                                $studentExamStatus->update([
                                    'status' => 2
                                ]);
                            } else {
                                return response()->json([
                                    'error' => true,
                                    'message' => trans('invalid_online_exam_attempt_not_found'),
                                    'code' => 103
                                ]);
                            }
                        }
                    } else {
                        return response()->json([
                            'error' => true,
                            'message' => trans('invalid_question_id'),
                            'code' => 103
                        ]);
                    }
                }
                DB::commit();

                return response()->json([
                    'error' => false,
                    'message' => trans('data_store_successfully'),
                    'code' => 200,
                ]);
            } else {
                info("{GetLine No. }", [trans('invalid_online_exam_id')]);

                return response()->json([
                    'error' => true,
                    'message' => trans('invalid_online_exam_id'),
                    'code' => 103
                ]);
            }
        } catch (Exception $e) {
            report($e);
            DB::rollBack();

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $student = $request->user()->student;
            $class_section_id = $student->class_section_id;
            $class_id = $student->class_section->class_id;


            $session_year_id = settingByType('session_year');

            //get current
            $current_date_time = now()->toArray()['formatted'];

            $exam_query = OnlineExam::where([
                'subject_id' => $request->subject_id,
                'session_year_id' => $session_year_id,
                ['start_date', '<=', $current_date_time]
            ])->where(function ($q) use ($class_section_id, $class_id) {
                return $q->where(
                    fn($q) => $q->where([
                        'model_type' => ClassSection::class,
                        'model_id' => $class_section_id
                    ])
                )->orWhere(
                        fn($q) => $q->where([
                            'model_type' => ClassSchool::class,
                            'model_id' => $class_id
                        ])
                    );
            });
            $exam_exists = $exam_query->count();
            $exam_query_without_session_year = OnlineExam::where([
                'model_type' => 'App\Models\ClassSection',
                'model_id' => $class_section_id,
                'subject_id' => $request->subject_id,
                ['start_date', '<=', $current_date_time]
            ])->orWhere(function ($query) use ($class_id, $session_year_id, $request, $current_date_time) {
                $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, ['start_date', '<=', $current_date_time]]);
            });

            // checks the exams exists
            if (isset($exam_exists) && ! empty($exam_exists)) {
                //total online exams id and counts
                $total_exam_ids = $exam_query->pluck('id');
                //online exam ids attempted
                $attempted_online_exam_ids = StudentOnlineExamStatus::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->pluck('online_exam_id');

                //get the submitted answers (i.e. option id)
                $online_exams_attempted_answers = OnlineExamStudentAnswer::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->pluck('option_id');

                //get the submitted choiced question id
                $online_exams_submitted_question_ids = OnlineExamStudentAnswer::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->pluck('question_id');

                //get the questions id
                $get_question_ids = OnlineExamQuestionChoice::whereIn('id', $online_exams_submitted_question_ids)->pluck('question_id');

                //removes the question id of the question if one of the answer of particular question is wrong
                foreach ($get_question_ids as $question_id) {
                    $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $online_exams_attempted_answers)->count();
                    if ($check_questions_answers_exists) {
                        unset($get_question_ids[array_search($question_id, $get_question_ids->toArray())]);
                    }
                }
                //get the correct answers question id
                $correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $get_question_ids)->whereIn('answer', $online_exams_attempted_answers)->pluck('question_id');


                //total exams
                $total_exams = $exam_query_without_session_year->count();

                //total exam attempted
                $total_attempted_exams = StudentOnlineExamStatus::where('student_id', $student->id)->whereIn('online_exam_id', $total_exam_ids)->count();

                // total missed exams
                $total_missed_exams = $exam_query_without_session_year->whereNotIn('id', $attempted_online_exam_ids)->count();

                // get the correct choiced question id and marks
                $total_obtained_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->whereIn('online_exam_id', $total_exam_ids)->whereIn('question_id', $correct_answers_question_id)->first();
                $total_obtained_marks = $total_obtained_marks['sum(marks)'];

                //overall total marks
                $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->whereIn('online_exam_id', $total_exam_ids)->first();
                $total_marks = $total_marks['sum(marks)'];

                if ($total_obtained_marks) {
                    $percentage = number_format(($total_obtained_marks * 100) / $total_marks, 2);
                }


                // particular online exam data
                $online_exams_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['start_date', '<=', $current_date_time]])->orWhere(function ($query) use ($class_id, $session_year_id, $request, $current_date_time) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id, ['start_date', '<=', $current_date_time]]);
                })->with([
                            'student_attempt' => function ($q) use ($student) {
                                $q->where('student_id', $student->id);
                            }
                        ])->has('question_choice')->paginate(10)->toArray();


                $exam_list = [];
                $total_obtained_marks_exam = '';
                foreach ($online_exams_db['data'] as $data) {
                    $exam_submitted_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('question_id');
                    $get_exam_question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_submitted_question_ids)->pluck('question_id');


                    $exam_attempted_answers = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('option_id');


                    //removes the question id of the question if one of the answer of particular question is wrong
                    foreach ($get_exam_question_ids as $question_id) {
                        $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $exam_attempted_answers)->count();
                        if ($check_questions_answers_exists) {
                            unset($get_exam_question_ids[array_search($question_id, $get_exam_question_ids->toArray())]);
                        }
                    }

                    $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $get_exam_question_ids)->whereIn('answer', $exam_attempted_answers)->pluck('question_id');

                    $total_obtained_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->whereIn('question_id', $exam_correct_answers_question_id)->first();
                    $total_obtained_marks_exam = $total_obtained_marks_exam['sum(marks)'];
                    $total_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                    $total_marks_exam = $total_marks_exam['sum(marks)'];

                    $exam_list[] = array(
                        'online_exam_id' => $data['id'],
                        'title' => $data['title'],
                        'obtained_marks' => $total_obtained_marks_exam ?? "0",
                        'total_marks' => $total_marks_exam ?? "0",
                    );

                }


                // array of final data
                $online_exam_report_data = [
                    'total_exams' => $total_exams,
                    'attempted' => $total_attempted_exams,
                    'missed_exams' => $total_missed_exams,
                    'total_marks' => $total_marks ?? "0",
                    'total_obtained_marks' => $total_obtained_marks ?? "0",
                    'percentage' => $percentage ?? "0",
                    'exam_list' => [
                        'current_page' => $online_exams_db['current_page'],
                        'data' => $exam_list,
                        'from' => $online_exams_db['from'],
                        'last_page' => $online_exams_db['last_page'],
                        'per_page' => $online_exams_db['per_page'],
                        'to' => $online_exams_db['to'],
                        'total' => $online_exams_db['total'],
                    ]
                ];
            }
            $response = [
                'error' => false,
                'data' => $online_exam_report_data ?? [],
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getAssignmentReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = $request->user()->student;

            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // get the assignments ids
            $assingment_ids = Assignment::where(['class_section_id' => $student->class_section_id, 'session_year_id' => $session_year_id, 'subject_id' => $request->subject_id])->pluck('id');

            //total assignments of class
            $total_assignments = Assignment::where(['class_section_id' => $student->class_section_id, 'session_year_id' => $session_year_id, 'subject_id' => $request->subject_id])->count();

            //total assignment submiited
            $total_submitted_assignments = AssignmentSubmission::where('student_id', $student->id)->whereIn('assignment_id', $assingment_ids)->count();

            // submitted assingment id
            $submitted_assignment_ids = AssignmentSubmission::where('student_id', $student->id)->whereIn('assignment_id', $assingment_ids)->pluck('assignment_id');

            //total assignment unsubmitted
            $total_assingment_unsubmitted = Assignment::where(['class_section_id' => $student->class_section_id, 'subject_id' => $request->subject_id])->whereNotIn('id', $submitted_assignment_ids)->count();

            //total points of assignment submitted
            $total_assignment_submitted_points = Assignment::select(DB::raw("sum(points)"))->where('class_section_id', $student->class_section_id)->whereIn('id', $submitted_assignment_ids)->whereNot('points', null)->first();
            $total_assignment_submitted_points = $total_assignment_submitted_points['sum(points)'];

            // total obtained assignment points
            $assingment_id_with_points = Assignment::where(['class_section_id' => $student->class_section_id, 'subject_id' => $request->subject_id])->whereIn('id', $submitted_assignment_ids)->whereNot('points', null)->pluck('id');
            $total_points_obtained = AssignmentSubmission::select(DB::raw("sum(points)"))->whereIn('assignment_id', $assingment_id_with_points)->where('student_id', $student->id)->first();
            $total_points_obtained = $total_points_obtained['sum(points)'];

            if ($total_points_obtained) {
                //percentage
                $percentage = number_format(($total_points_obtained * 100) / $total_assignment_submitted_points, 2);
            }

            $submitted_assignment_data_db = Assignment::with('submission')->where(['class_section_id' => $student->class_section_id, 'subject_id' => $request->subject_id])->whereIn('id', $submitted_assignment_ids)->whereNot('points', null);
            $submitted_assignment_data_with_points = $submitted_assignment_data_db->paginate(10)->toArray();

            $submitted_assingment_data = array();
            foreach ($submitted_assignment_data_with_points['data'] as $submitted_data) {
                $submitted_assingment_data[] = array(
                    'assignment_id' => $submitted_data['id'],
                    'assignment_name' => $submitted_data['name'],
                    'obtained_points' => $submitted_data['submission']['points'],
                    'total_points' => $submitted_data['points']
                );
            }
            $assingment_report = array(
                'assignments' => $total_assignments,
                'submitted_assignments' => $total_submitted_assignments,
                'unsubmitted_assignments' => $total_assingment_unsubmitted,
                'total_points' => $total_assignment_submitted_points ?? "0",
                'total_obtained_points' => $total_points_obtained ?? "0",
                'percentage' => $percentage ?? "0",
                'submitted_assignment_with_points_data' => array(
                    'current_page' => $submitted_assignment_data_with_points['current_page'],
                    'data' => $submitted_assingment_data,
                    'from' => $submitted_assignment_data_with_points['from'],
                    'last_page' => $submitted_assignment_data_with_points['last_page'],
                    'per_page' => $submitted_assignment_data_with_points['per_page'],
                    'to' => $submitted_assignment_data_with_points['to'],
                    'total' => $submitted_assignment_data_with_points['total'],
                )
            );
            $response = array(
                'error' => false,
                'data' => $assingment_report,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response, 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function getOnlineExamResultList(Request $request)
    {
        try {
            $student = $request->user()->student;
            $class_section_id = $student->class_section_id;
            $class_id = $student->class_section->class_id;

            // current session year id
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // get the class subject id on the basis of subject id passed
            // query meets the condition for both class section and class
            if (isset($request->subject_id) && ! empty($request->subject_id)) {
                $online_exam_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'subject_id' => $request->subject_id, 'session_year_id' => $session_year_id])->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $session_year_id, $request, $student) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'session_year_id' => $session_year_id, 'subject_id' => $request->subject_id])->with('subject')->whereHas('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->with('subject')->paginate(10)->toArray();
            } else {
                $online_exam_db = OnlineExam::where(['model_type' => 'App\Models\ClassSection', 'model_id' => $class_section_id, 'session_year_id' => $session_year_id])->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })->orWhere(function ($query) use ($class_id, $session_year_id, $student) {
                    $query->where(['model_type' => 'App\Models\ClassSchool', 'model_id' => $class_id, 'session_year_id' => $session_year_id])->with('subject')->whereHas('student_attempt', function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    });
                })->with('subject')->paginate(10)->toArray();
            }
            $exam_list_data = array();
            foreach ($online_exam_db['data'] as $data) {
                //get the choice question id
                $exam_submitted_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('question_id');
                $exam_submitted_date = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('submitted_date')->first();

                $question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_submitted_question_ids)->pluck('question_id');


                $exam_attempted_answers = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $data['id']])->pluck('option_id');

                //removes the question id of the question if one of the answer of particular question is wrong
                foreach ($question_ids as $question_id) {
                    $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $exam_attempted_answers)->count();
                    if ($check_questions_answers_exists) {
                        unset($question_ids[array_search($question_id, $question_ids->toArray())]);
                    }
                }

                $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->pluck('question_id');

                // get the data of only attempted data
                $total_obtained_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->whereIn('question_id', $exam_correct_answers_question_id)->first();
                $total_obtained_marks_exam = $total_obtained_marks_exam['sum(marks)'];
                $total_marks_exam = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $data['id'])->first();
                $total_marks_exam = $total_marks_exam['sum(marks)'];

                $exam_list_data[] = array(
                    'online_exam_id' => $data['id'],
                    'subject' => array(
                        'id' => $data['subject_id'],
                        'name' => $data['subject']['name'] . ' - ' . $data['subject']['type'],
                    ),
                    'title' => $data['title'],
                    'obtained_marks' => $total_obtained_marks_exam ?? "0",
                    'total_marks' => $total_marks_exam ?? "0",
                    'exam_submitted_date' => $exam_submitted_date ?? date('Y-m-d', strtotime($data['end_date']))
                );
            }
            $exam_list = array(
                'current_page' => $online_exam_db['current_page'],
                'data' => $exam_list_data ?? '',
                'from' => $online_exam_db['from'],
                'last_page' => $online_exam_db['last_page'],
                'per_page' => $online_exam_db['per_page'],
                'to' => $online_exam_db['to'],
                'total' => $online_exam_db['total'],
            );
            $response = array(
                'error' => false,
                'data' => $exam_list ?? '',
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getOnlineExamResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'online_exam_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            $student = $request->user()->student;

            //get the total questions count
            $total_questions = OnlineExamQuestionChoice::where('online_exam_id', $request->online_exam_id)->count();

            //get the exam's choiced question id
            $exam_choiced_question_ids = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->pluck('question_id');

            //get the questions id
            $question_ids = OnlineExamQuestionChoice::whereIn('id', $exam_choiced_question_ids)->pluck('question_id');

            //get the options submitted by student
            $exam_attempts = OnlineExamStudentAnswer::where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->with('question')->get();
            $exam_attempted_answers = $exam_attempts->pluck('option_id');


            //removes the question id of the question if one of the answer of particular question is wrong
            foreach ($question_ids as $question_id) {
                $check_questions_answers_exists = OnlineExamQuestionAnswer::where('question_id', $question_id)->whereNotIn('answer', $exam_attempted_answers)->count();
                if ($check_questions_answers_exists) {
                    unset($question_ids[array_search($question_id, $question_ids->toArray())]);
                }
            }

            // get the correct answers counter
            $exam_correct_answers = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->groupby('question_id')->pluck('question_id')->count();

            // question id of correct answers
            $exam_correct_answers_question_id = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->pluck('question_id');

            //data of correct answers
            $exam_correct_answers_data = OnlineExamQuestionAnswer::whereIn('question_id', $question_ids)->whereIn('answer', $exam_attempted_answers)->groupby('question_id')->get();

            // array of correct answer with choiced exam id and marks
            $correct_answers_data = [];
            foreach ($exam_correct_answers_data as $correct_data) {
                $choice_questions = OnlineExamQuestionChoice::where(['online_exam_id' => $request->online_exam_id, 'question_id' => $correct_data->question_id])->first();

                $questionChoiceAnswers = $choice_questions->questions->answers->pluck('options.id')->toArray();

                $correct_answers_data[] = $this->answerFormat(
                    isCorrect: true,
                    choiceQuestions: $choice_questions,
                    exam_attempts: $exam_attempts,
                    correct_data: $correct_data,
                    questionChoiceAnswers: $questionChoiceAnswers
                );

            }

            // get questions ids
            $all_questions_ids = OnlineExamQuestionChoice::whereNotIn('question_id', $question_ids)->where('online_exam_id', $request->online_exam_id)->pluck('question_id');

            // get the incorrect answers && unattempted counter
            $exam_in_correct_answers = OnlineExamQuestionAnswer::whereIn('question_id', $all_questions_ids)->whereNotIn('answer', $exam_attempted_answers)->groupby('question_id')->pluck('question_id')->count();

            // data of in correct && unattempted answers
            $exam_in_correct_answers_data = OnlineExamQuestionAnswer::whereIn('question_id', $all_questions_ids)->whereNotIn('answer', $exam_attempted_answers)->groupby('question_id')->get();

            // array of in correct answer && unattempted with choiced exam id and marks
            $in_correct_answers_data = array();
            foreach ($exam_in_correct_answers_data as $in_correct_data) {
                $choice_questions = OnlineExamQuestionChoice::where(['online_exam_id' => $request->online_exam_id, 'question_id' => $in_correct_data->question_id])->first();
                if (isset($choice_questions) && ! empty($choice_questions)) {
                    $choice_questions = OnlineExamQuestionChoice::where([
                        'online_exam_id' => $request->online_exam_id,
                        'question_id' => $in_correct_data->question_id
                    ])->first();

                    // $questionChoiceAnswers = $;
                    $in_correct_answers_data[] = $this->answerFormat(
                        isCorrect: false,
                        choiceQuestions: $choice_questions,
                        exam_attempts: $exam_attempts,
                        correct_data: $choice_questions,
                        questionChoiceAnswers: $choice_questions->questions->answers->pluck('options.id')->toArray()
                    );

                }
            }

            // total obtained and total marks
            $total_obtained_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $request->online_exam_id)->whereIn('question_id', $exam_correct_answers_question_id)->first();
            $total_obtained_marks = $total_obtained_marks['sum(marks)'];
            $total_marks = OnlineExamQuestionChoice::select(DB::raw("sum(marks)"))->where('online_exam_id', $request->online_exam_id)->first();
            $total_marks = $total_marks['sum(marks)'];

            // final array data
            $exam_result = [
                'total_questions' => $total_questions,
                'correct_answers' => array(
                    'total_questions' => $exam_correct_answers,
                    'question_data' => $correct_answers_data ?? ''
                ),
                'in_correct_answers' => array(
                    'total_questions' => $exam_in_correct_answers,
                    'question_data' => $in_correct_answers_data ?? ''
                ),
                'total_obtained_marks' => $total_obtained_marks ?? '0',
                'total_marks' => $total_marks
            ];
            $response = [
                'error' => false,
                'data' => $exam_result ?? '',
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getProfileDetails()
    {
        try {
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $compulsory_fees_mode = getSettings('compulsory_fee_payment_mode');
            $compulsory_fees_mode = $compulsory_fees_mode['compulsory_fee_payment_mode'] ?? 0;

            $session_year = SessionYear::where('id', $session_year_id)->first();
            $isInstallment = $session_year->include_fee_installments;

            $due_date = $session_year->fee_due_date;
            $free_app_use_date = $session_year->free_app_use_date;

            $current_date = now()->toDateString();

            $user = Auth::user()->load(['student.class_section', 'student.category']);
            //Set Class Section name
            $classSection = optional($user->student->class_section);
            $classSectionName = "{$classSection->class?->name} {$classSection->section?->name}";

            // Set Class Section name
            $streamName = $classSection->class->streams->name ?? null;
            if ($streamName !== null) {
                $user->class_section_name = $classSectionName . " " . $streamName;
            } else {
                $user->class_section_name = $classSectionName;
            }

            //Set Medium name
            $user->medium_name = $classSection->class->medium?->name ?? '';

            //Set School Shift name
            $user->shift_id = $classSection->class->shifts->id ?? '';
            $user->shift = Shift::find($user->shift_id);
            if ($user->shift) {
                $user->shift->id;
                $user->shift->title;
                $user->shift->start_time;

            }
            unset($user->student->class_section);

            //Set Category
            $user->category_name = $user->student->category->name;
            unset($user->student->category);

            $class_id = $classSection->class_id;

            if ($compulsory_fees_mode == 1) {
                if (isset($free_app_use_date)) {
                    if ($current_date >= $free_app_use_date) {
                        $user->is_fee_payment_due = 1;
                    } else {
                        $user->is_fee_payment_due = 0;
                    }
                } else {
                    $fees_paid = FeesPaid::where('student_id', $user->student->id)->where('session_year_id', $session_year_id)->first();

                    if ($isInstallment == 0) {
                        // Non-installment case
                        if (isset($fees_paid) && $fees_paid->is_fully_paid == 0) {
                            $user->is_fee_payment_due = ($current_date >= $due_date) ? 1 : 0;
                        } else {
                            $user->is_fee_payment_due = 0;
                        }
                    } else {

                        if (isset($fees_paid) && $fees_paid->is_fully_paid == 1) {
                            $user->is_fee_payment_due = 0;
                        } else {
                            // Installment case
                            $installment_db = InstallmentFee::where('session_year_id', $session_year_id);
                            if ($installment_db->count()) {
                                $installment_db_data = $installment_db->get();
                                foreach ($installment_db_data as $data) {
                                    $paid_installment_data = PaidInstallmentFee::where(['student_id' => $user->student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'installment_fee_id' => $data['id'], 'status' => 1])->first();
                                    $installment_data[] = array(
                                        'id' => $data->id,
                                        'name' => $data->name,
                                        'due_date' => date('Y-m-d', strtotime($data->due_date)),
                                        'due_charges' => $data->due_charges,
                                        'is_paid' => $paid_installment_data->status ?? 0,
                                    );
                                }
                            }
                            // Find the first unpaid installment and set its due date
                            foreach ($installment_data as $data) {
                                if ($data['is_paid'] == 0) {
                                    $due_date = $data['due_date'];
                                    break; // Stop after the first unpaid installment
                                }
                            }
                            $user->is_fee_payment_due = ($current_date >= $due_date) ? 1 : 0;
                        }

                    }
                }


            } else {
                $user->is_fee_payment_due = 0;
            }

            $dynamicFields = null;
            $dynamicField = $user->student->dynamic_fields;
            $user = flattenMyModel($user);

            $data = json_decode($dynamicField, true);
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (! empty($item)) {
                        foreach ($item as $key => $value) {
                            $dynamicFields[$key] = $value;
                        }
                    }
                }
            } else {
                $dynamicFields = $data;
            }

            $data = array_merge($user, ['dynamic_fields' => $dynamicFields ?? null]);

            $response = [
                'error' => false,
                'message' => 'Data Fetched Successfully',
                'data' => $data,
                'code' => 100,
            ];

        } catch (Exception $e) {
            report($e);
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getNotifications(Request $request)
    {
        try {
            $user = $request->user()->id;
            $notification_id = UserNotification::where('user_id', $user)->pluck('notification_id');
            $notification = Notification::whereIn('id', $notification_id)->latest()->paginate();
            $response = array(
                'error' => false,
                'data' => $notification ?? '',
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);

    }

    public function getChatUserList(Request $request)
    {
        try {
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;
            $search = $request->search;

            $user = Auth::user();

            $class_section_id = $user->student->class_section?->id;

            $student_subject = $user->student->subjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }
            $subject_id = array_merge($core_subjects, $elective_subjects);


            $class_teachers_id = ClassTeacher::where('class_section_id', $class_section_id)->pluck('class_teacher_id')->toArray();
            $subject_teacher_id = SubjectTeacher::where('class_section_id', $class_section_id)->whereIn('subject_id', $subject_id)->pluck('teacher_id')->toArray();


            $teacher_ids = array_merge($class_teachers_id, $subject_teacher_id);

            $teachers = Teacher::whereIn('id', $teacher_ids)
                ->when(
                    fn($q) => $q->where(function ($query) use ($search) {
                        $query->whereHas('user', function ($subquery) use ($search) {
                            $subquery->whereRaw("concat(first_name,' ',last_name) LIKE '%{$search}%'");
                        });
                    })
                )
                ->with('user:id,first_name,last_name,image,mobile,email', 'subjects.subject')
                ->offset($offset)->limit($limit)
                ->get();



            $data = [];

            foreach ($teachers as $teacher) {

                $unreadCount = 0;
                $subjectData = [];

                foreach ($teacher->subjects as $subject) {
                    $subjectData[] = [
                        'id' => $subject->subject->id ?? '',
                        'name' => $subject->subject->name ?? '',
                    ];
                }

                $lastMessage = ChatMessage::with('file')->where(function ($query) use ($user, $teacher) {
                    $query->where('modal_id', $teacher->user->id)
                        ->where('sender_id', $user->id);
                })
                    ->orWhere(function ($query) use ($user, $teacher) {
                        $query->where('sender_id', $teacher->user->id)
                            ->where('modal_id', $user->id);
                    })
                    ->select('id', 'body', 'date')
                    ->latest()
                    ->first();


                $lastReadMessage = ReadMessage::where('modal_id', $user->id)->where('user_id', $teacher->user->id)->first();

                if ($lastReadMessage) {

                    $lastReadMessageId = $lastReadMessage->last_read_message_id;
                    if (! empty($lastReadMessageId)) {
                        $unreadCount = ChatMessage::where('sender_id', $teacher->user->id)->where('modal_id', $user->id)->where('id', '>', $lastReadMessageId)->count();
                    } else {
                        $unreadCount = ChatMessage::where('sender_id', $teacher->user->id)->where('modal_id', $user->id)->count();
                    }

                }
                $data[] = [
                    'id' => $teacher->id,
                    'user_id' => $teacher->user->id,
                    'first_name' => $teacher->user->first_name,
                    'last_name' => $teacher->user->last_name,
                    'email' => $teacher->user->email,
                    'qualification' => $teacher->qualification,
                    'image' => $teacher->user->image,
                    'mobile_no' => $teacher->user->mobile,
                    'subjects' => $subjectData,
                    'last_message' => $lastMessage ?? null,
                    'unread_message' => $unreadCount ?? 0
                ];

            }
            $total_items = count($data);

            $unreadusers = array_filter($data, function ($teacher) {
                return $teacher['unread_message'] > 0;
            });

            $totalunreadusers = count($unreadusers);

            $data = collect($data)->sortByDesc(function ($user) {
                return boolval(optional($user['last_message'])->date);
            })->values();

            $response = [
                'error' => false,
                'message' => 'Data Fetched Successfully',
                'data' => [
                    'items' => $data,
                    'total_items' => $total_items,
                    'total_unread_users' => $totalunreadusers,
                ],
                'code' => 100,
            ];

            return response()->json($response);

        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|numeric',
            'message' => 'required_without:file',
            'file.*' => 'nullable'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $sender_id = $request->user()->id;
            $receiver_id = $request->receiver_id;

            $message = new ChatMessage();
            $message->modal_id = $receiver_id;
            $message->modal_type = User::class;
            $message->sender_id = $sender_id;
            $message->body = $request->message ?? '';
            $message->date = now();
            $message->save();
            $count = 0;
            $unreadCount = 0;

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedFile) {

                    $originalName = $uploadedFile->hashName();
                    $filePath = $uploadedFile->storeAs('chatfile', $originalName, 'public');

                    $file = new ChatFile();
                    $file->file_type = 1;
                    $file->file_name = $filePath;
                    $file->message_id = $message->id;
                    $file->save();
                    $count++;
                }
            }


            $readMessage = ReadMessage::where('modal_id', $receiver_id)->where('user_id', $sender_id)->first();

            if (empty($readMessage)) {
                $readMessage = new ReadMessage();
                $readMessage->modal_id = $receiver_id;
                $readMessage->modal_type = 'App/Models/User';
                $readMessage->user_id = $sender_id;
                $readMessage->save();
            }

            $message = ChatMessage::with('file')->where('id', $message->id)->select('id', 'sender_id', 'body', 'date')->get();

            foreach ($message as $message) {
                $chatfile = [];
                foreach ($message->file as $file) {
                    if (! empty($file)) {
                        $chatfile[] = tenant_asset($file->file_name);
                    } else {
                        $chatfile[] = '';
                    }

                }

                $data = array(
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'body' => $message->body,
                    'date' => $message->date,
                    'files' => $chatfile
                );
            }

            $student = Students::with('user')->where('user_id', $sender_id)->first();


            $lastReadMessage = ReadMessage::where('modal_id', $receiver_id)->where('user_id', $student->user_id)->first();

            if ($lastReadMessage) {

                $lastReadMessageId = $lastReadMessage->last_read_message_id;

                if (! empty($lastReadMessageId) || ($lastReadMessageId != null)) {
                    $unreadCount = ChatMessage::where('modal_id', $receiver_id)->where('sender_id', $student->user_id)->where('id', '>', $lastReadMessageId)->count();
                } else {
                    $unreadCount = ChatMessage::where('modal_id', $receiver_id)->where('sender_id', $student->user_id)->count();
                }
            }



            $student_subject = $student->subjects();

            $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

            $elective_subjects = $student_subject["elective_subject"] ?? [];
            if ($elective_subjects) {
                $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
            }
            $subject_id = array_merge($core_subjects, $elective_subjects);


            $subjects = Subject::whereIn('id', $subject_id)->get();
            $subjectArray = [];
            foreach ($subjects as $subject) {
                $subjectArray[] = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];
            }


            $userinfo = [
                'id' => $student->id,
                'user_id' => $student->user_id, // Assuming this is the correct property name
                'first_name' => $student->user->first_name,
                'last_name' => $student->user->last_name,
                'image' => $student->user->image,
                'roll_no' => $student->roll_number,
                'admission_no' => $student->admission_no,
                'gender' => $student->user->gender,
                'dob' => $student->user->dob,
                'subjects' => $subjectArray,
                'address' => $student->user->current_address,
                'last_message' => $data ?? null,
                'class_name' => $student->class_section->class->name . ' ' . $student->class_section->section->name . ' ' . $student->class_section->class->medium->name,
                'isParent' => 0,
                'unread_message' => $unreadCount ?? 0
            ];

            $title = $student->user->first_name . ' ' . $student->user->last_name;
            $body = $request->message ?? $count . " Files Received";
            $type = "chat";
            $image = null;
            $user[] = $receiver_id;
            $userinfo = (object) $userinfo;

            send_notification($user, $title, $body, $type, $image, $userinfo);

            $response = array(
                'error' => false,
                'message' => trans('message_sent_successfully'),
                'data' => $data,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);

    }

    public function getUserChatMessage(Request $request)
    {
        try {
            $offset = $request->offset;
            $limit = $request->limit;

            $messages = ChatMessage::with([
                'file' => function ($query) {
                    $query->select('message_id', 'file_name');
                }
            ])
                ->where(function ($query) use ($request) {
                    $query->where('modal_id', $request->user_id)
                        ->orWhere('modal_id', Auth::id());
                })
                ->where(function ($query) use ($request) {
                    $query->where('sender_id', $request->user_id)
                        ->orWhere('sender_id', Auth::id());
                })
                ->select('id', 'sender_id', 'body', 'date')
                ->latest('date');

            $total_items = $messages->count();

            $messages = $messages->offset($offset)->limit($limit)->get()->toArray();

            foreach ($messages as &$message) {
                if (isset($message['file'])) {
                    $message['files'] = collect($message['file'])->map(function ($file) {
                        return tenant_asset($file['file_name']);
                    })->toArray();

                    unset($message['file']);
                } else {
                    $message['files'] = []; // or handle the case where 'file' is not set
                }
            }

            $response = array(
                'error' => false,
                'message' => 'Data Fetched Successfully',
                'data' => [
                    'items' => $messages ?? [],
                    'total_items' => $total_items,
                ],
                'code' => 100,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);

    }

    public function readAllMessages(Request $request)
    {
        try {
            $user = Auth::id();
            $teacher = $request->user_id;

            $lastMessage = ChatMessage::where('sender_id', $teacher)->where('modal_id', $user)->latest()->first();
            if ($lastMessage) {
                $message_id = $lastMessage->id;
            }


            // Update Read Message id
            $readMessage = ReadMessage::where('modal_id', $user)->where('user_id', $teacher)->first();

            if ($readMessage) {
                $readMessage->last_read_message_id = $message_id;
                $readMessage->save();
            }

            $response = [
                'error' => false,
                'message' => 'Message Read',
                'code' => 200,
            ];
            return response()->json($response, 200);
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
            return response()->json($response, 200);
        }
    }

    //Get Fees Details
    public function getFeesDetails(Request $request)
    {
        try {
            $student = $request->user()->student;

            $current_date = Carbon::now('UTC');

            $class_id = $student->class_section->class_id;

            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            //Total optional fees amount
            $optional_fees_amount = FeesClass::select(DB::raw("SUM(amount) as optional_fees_amount"))->where(['class_id' => $class_id, 'choiceable' => 1])->first();
            $optional_fees_amount = $optional_fees_amount['optional_fees_amount'];

            //Total optional fees amount
            $compulsory_fees_amount = FeesClass::select(DB::raw("SUM(amount) as compulsory_fees_amount"))->where(['class_id' => $class_id, 'choiceable' => 0])->first();
            $compulsory_fees_amount = $compulsory_fees_amount['compulsory_fees_amount'];

            // Fees Class Data
            $fees_class = FeesClass::where('class_id', $class_id)->with('fees_type')->get();

            //arrays for data
            $compulsory_fees_data = [];
            $optional_fees_data = [];
            $installment_data = [];

            $payment_transaction_db = PaymentTransaction::where(['student_id' => $student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->latest()->first();

            if ($payment_transaction_db) {
                if ($payment_transaction_db->date) {
                    $datetime = Carbon::parse($payment_transaction_db->date);
                    $time = $datetime->format('H:i:s');
                    $addHour = $datetime->copy()->addHour();
                    if (now()->gt($addHour)) {
                        $payment_transaction_db->payment_status = 0;
                        $payment_transaction_db->save();
                    }
                }
                $payment_transaction_status = $payment_transaction_db->payment_status;
            }
            // Get the Compulsory Fees Data and Optional Fees Data
            foreach ($fees_class as $data) {
                if ($data->choiceable == 1) {
                    $paid_optional_data = FeesChoiceable::where(['student_id' => $student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'fees_type_id' => $data['fees_type_id'], 'status' => 1])->first();
                    $optional_fees_data[] = [
                        'id' => $data->fees_type_id,
                        'name' => $data->fees_type->name,
                        'amount' => $data->amount,
                        'is_paid' => $paid_optional_data->status ?? 0,
                        'paid_date' => $paid_optional_data->date ?? null
                    ];
                } else {
                    $is_fully_paid_data = FeesPaid::where(['student_id' => $student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'is_fully_paid' => 1]);
                    $compulsory_fees_data[] = [
                        'id' => $data->fees_type_id,
                        'name' => $data->fees_type->name,
                        'amount' => $data->amount,
                        'is_paid' => ! empty($is_fully_paid_data->count()) ? 1 : 0,
                        'paid_on' => ! empty($is_fully_paid_data->count()) ? ((isset($is_fully_paid_data->first()->date) && ! empty($is_fully_paid_data->first()->date)) ? date('Y-m-d', strtotime($is_fully_paid_data->first()->date)) : null) : "",
                    ];
                }
            }

            // Checking for Due Charges Paid with Fully Compulsory Amount and Add it to Compulsory Fees Data Array
            if (isset($compulsory_fees_data) && ! empty($compulsory_fees_data)) {
                $paid_charges_due = FeesChoiceable::where(['student_id' => $student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'is_due_charges' => 1]);
                if ($paid_charges_due->count()) {
                    array_push(
                        $compulsory_fees_data,
                        [
                            'id' => "",
                            'name' => 'Due Charges',
                            'amount' => $paid_charges_due->first()->total_amount,
                            'is_paid' => 1
                        ]
                    );
                }
            }

            // DB::enableQueryLog();
            //check the installments data for current session year
            $installment_db = InstallmentFee::where('session_year_id', $session_year_id);
            if ($installment_db->count()) {
                $installment_db_data = $installment_db->get();
                foreach ($installment_db_data as $data) {
                    $paid_installment_data = PaidInstallmentFee::where(['student_id' => $student->id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'installment_fee_id' => $data['id'], 'status' => 1])->first();
                    $installment_data[] = [
                        'id' => $data->id,
                        'name' => $data->name,
                        'due_date' => date('Y-m-d', strtotime($data->due_date)),
                        'due_charges' => $data->due_charges,
                        'is_paid' => $paid_installment_data->status ?? 0,
                        'paid_date' => $paid_installment_data->date ?? null,
                        "paid_due_charges" => ! empty($paid_installment_data) ? number_format($paid_installment_data->due_charges, 2) : ""
                    ];
                }
            }
            //Due Date And Due Charges From Session Year For Fully pay Compulsory Amount (Without Installments)
            $session_year_data = SessionYear::where('id', $session_year_id)->first();
            $due_date = date('Y-m-d', strtotime($session_year_data->fee_due_date));
            $due_charges = $session_year_data->fee_due_charges;

            $response = [
                'error' => false,
                'compulsory_fees_data' => $compulsory_fees_data ?? [""],
                'optional_fees_data' => $optional_fees_data ?? [""],
                'installment_data' => $installment_data ?? (object) null,
                'compulsory_fees_total' => $compulsory_fees_amount ?? 0,
                'optional_fees_total' => $optional_fees_amount ?? 0,
                'compulsory_due_date' => $due_date,
                'compulsory_due_charges' => $due_charges,
                'current_date' => $current_date,
                'is_fee_pending' => (isset($payment_transaction_status) && $payment_transaction_status == 2) ? 1 : 0,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    //Store Fees Transaction
    public function storeFeesTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'type_of_fee' => 'required|in:0,1,2',
            'is_fully_paid' => 'required|in:0,1'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {

            $student = $request->user()->student;

            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            //variables for storing the data
            $payment_gateway_details = array();

            $class_id = $student->class_section->class_id;

            //variables for storing the data
            $optional_fees_store = array();
            $installment_fees_store = array();
            $paid_installment_id = [];
            $optional_fees_id = [];


            $razorpay_status = getSettings('razorpay_status');
            $razorpay_status = $razorpay_status['razorpay_status'];

            // get currency code from settings
            $setting_currency_code = getSettings('currency_code');
            $currency_code = $setting_currency_code['currency_code'];

            if ($razorpay_status) {
                // get api key from settings
                $razorpay_setting_api_key = getSettings('razorpay_api_key');
                $razorpay_api_key = $razorpay_setting_api_key['razorpay_api_key'];

                // get secret key from settings
                $razorpay_setting_secret_key = getSettings('razorpay_secret_key');
                $razorpay_secret_key = $razorpay_setting_secret_key['razorpay_secret_key'];

                $currency_code = strtoupper($currency_code);

                // add the data to transaction table local
                $payment_transaction_db = PaymentTransaction::create([
                    'student_id' => $student->id,
                    'parent_id' => $student->father_id ?? $student->guardian_id,
                    'class_id' => $class_id,
                    'mode' => 2,
                    'type_of_fee' => $request->type_of_fee,
                    'payment_gateway' => 1,
                    'payment_status' => 2,
                    'total_amount' => $request->amount,
                    'date' => date('Y-m-d H:i:s'),
                    'session_year_id' => $session_year_id,
                ]);

                // If Optional Fees Passed then insert data
                if (isset($request->optional_fees_data) && ! empty($request->optional_fees_data)) {
                    foreach ($request->optional_fees_data as $data) {
                        $optional_fees_id[] = FeesChoiceable::insertGetId([
                            'student_id' => $student->id,
                            'class_id' => $class_id,
                            'fees_type_id' => $data['id'],
                            'is_due_charges' => 0,
                            'total_amount' => $data['amount'],
                            'session_year_id' => $session_year_id,
                            'date' => date('Y-m-d'),
                            'payment_transaction_id' => $payment_transaction_db->id,
                            'status' => 0
                        ]);
                    }
                }

                // If Installment Fees Passed then insert data
                if (isset($request->installment_data) && ! empty($request->installment_data)) {
                    foreach ($request->installment_data as $data) {
                        $paid_installment_id[] = PaidInstallmentFee::insertGetId([
                            'class_id' => $class_id,
                            'student_id' => $student->id,
                            'parent_id' => $student->father_id ?? $student->guardian_id,
                            'installment_fee_id' => $data['id'],
                            'session_year_id' => $session_year_id,
                            'amount' => $data['amount'],
                            'due_charges' => $data['due_charges'] ?? null,
                            'date' => date('Y-m-d'),
                            'payment_transaction_id' => $payment_transaction_db->id,
                            'status' => 0
                        ]);
                    }
                }

                $api = new Api($razorpay_api_key, $razorpay_secret_key);
                $order = $api->order->create([
                    'amount' => $request->amount * 100,
                    'currency' => $currency_code,
                    'notes' => [
                        'student_id' => $student->id,
                        'parent_id' => $student->father_id ?? $student->guardian_id,
                        'class_id' => $class_id,
                        'session_year_id' => $session_year_id,
                        'payment_transaction_id' => $payment_transaction_db->id,
                        'is_fully_paid' => $request->is_fully_paid,
                        'type_of_fee' => $request->type_of_fee,
                        'is_due_charges' => (isset($request->due_charges) && $request->due_charges >= 0) ? 1 : 0,
                        'due_charges' => $request->due_charges,
                        'optional_fees_paid' => json_encode($optional_fees_id) ?? "",
                        'installment_fees_paid' => json_encode($paid_installment_id) ?? "",
                    ]
                ]);

                // update the order id in trasaction table local
                $payemnt_transaction_update = PaymentTransaction::find($payment_transaction_db->id);
                $payemnt_transaction_update->order_id = $order->id;
                $payemnt_transaction_update->save();

                $payment_gateway_details = [
                    'order_id' => $order->id,
                    'amount' => $order->amount,
                    'payment_transaction_id' => $payment_transaction_db->id,
                ];
            }

            $stripe_status = getSettings('stripe_status')['stripe_status'];

            if ($stripe_status) {
                $stripe_secret_key = getSettings('stripe_secret_key')['stripe_secret_key'];
                $currency_code = strtolower($currency_code);

                // add the data to transaction table local
                $payment_transaction_db = PaymentTransaction::create([
                    'student_id' => $student->id,
                    'parent_id' => $student->father_id ?? $student->guardian_id,
                    'class_id' => $class_id,
                    'mode' => 2,
                    'type_of_fee' => $request->type_of_fee,
                    'payment_gateway' => 2,
                    'payment_status' => 2,
                    'total_amount' => $request->amount,
                    'date' => date('Y-m-d H:i:s'),
                    'session_year_id' => $session_year_id,
                ]);

                // If Optional Fees Passed then insert data
                if (isset($request->optional_fees_data) && ! empty($request->optional_fees_data)) {
                    foreach ($request->optional_fees_data as $data) {
                        $optional_fees_id[] = FeesChoiceable::insertGetId([
                            'student_id' => $student->id,
                            'class_id' => $class_id,
                            'fees_type_id' => $data['id'],
                            'is_due_charges' => 0,
                            'total_amount' => $data['amount'],
                            'session_year_id' => $session_year_id,
                            'date' => date('Y-m-d'),
                            'payment_transaction_id' => $payment_transaction_db->id,
                            'status' => 0
                        ]);
                    }
                }

                // If Installment Fees Passed then insert data
                if (isset($request->installment_data) && ! empty($request->installment_data)) {
                    foreach ($request->installment_data as $data) {
                        $paid_installment_id[] = PaidInstallmentFee::insertGetId([
                            'class_id' => $class_id,
                            'student_id' => $student->id,
                            'parent_id' => $student->father_id ?? $student->guardian_id,
                            'installment_fee_id' => $data['id'],
                            'session_year_id' => $session_year_id,
                            'amount' => $data['amount'],
                            'due_charges' => $data['due_charges'] ?? null,
                            'date' => date('Y-m-d'),
                            'payment_transaction_id' => $payment_transaction_db->id,
                            'status' => 0
                        ]);
                    }
                }


                $stripe = new StripeClient($stripe_secret_key);
                $stripe_data = $stripe->paymentIntents->create([
                    'amount' => $request->amount * 100,
                    'currency' => $currency_code,
                    'metadata' => [
                        'student_id' => $student->id,
                        'parent_id' => $student->father_id ?? $student->guardian_id,
                        'class_id' => $class_id,
                        'session_year_id' => $session_year_id,
                        'payment_transaction_id' => $payment_transaction_db->id,
                        'is_fully_paid' => $request->is_fully_paid,
                        'type_of_fee' => $request->type_of_fee,
                        'is_due_charges' => (isset($request->due_charges) && $request->due_charges >= 0) ? 1 : 0,
                        'due_charges' => $request->due_charges,
                        'optional_fees_paid' => json_encode($optional_fees_id) ?? "",
                        'installment_fees_paid' => json_encode($paid_installment_id) ?? "",
                    ],
                ]);

                // update the order id in trasaction table local
                $payemnt_transaction_update = PaymentTransaction::find($payment_transaction_db->id);
                $payemnt_transaction_update->order_id = $stripe_data->id;
                $payemnt_transaction_update->save();

                $payment_gateway_details = array(
                    'payment_intent_id' => $stripe_data->id,
                    'amount' => $stripe_data->amount,
                    'client_secret' => $stripe_data->client_secret,
                    'payment_transaction_id' => $payment_transaction_db->id,
                );
            }

            $paystack_status = getSettings('paystack_status');
            $paystack_status = $paystack_status['paystack_status'];

            if ($paystack_status) {
                $paystack_setting_secret_key = getSettings('paystack_secret_key');
                $paystack_secret_key = $paystack_setting_secret_key['paystack_secret_key'];

                $currency_code = strtoupper($currency_code);

                // add the data to transaction table local
                $payment_transaction_db = PaymentTransaction::create([
                    'student_id' => $student->id,
                    'parent_id' => $student->father_id ?? $student->guardian_id,
                    'class_id' => $class_id,
                    'mode' => 2,
                    'type_of_fee' => $request->type_of_fee,
                    'payment_gateway' => 3,
                    'payment_status' => 2,
                    'total_amount' => $request->amount,
                    'date' => date('Y-m-d H:i:s'),
                    'session_year_id' => $session_year_id,
                ]);

                // If Optional Fees Passed then insert data
                if (isset($request->optional_fees_data) && ! empty($request->optional_fees_data)) {
                    foreach ($request->optional_fees_data as $data) {
                        $optional_fees_id[] = FeesChoiceable::insertGetId([
                            'student_id' => $student->id,
                            'class_id' => $class_id,
                            'fees_type_id' => $data['id'],
                            'is_due_charges' => 0,
                            'total_amount' => $data['amount'],
                            'session_year_id' => $session_year_id,
                            'date' => date('Y-m-d'),
                            'payment_transaction_id' => $payment_transaction_db->id,
                            'status' => 0
                        ]);
                    }
                }

                // If Installment Fees Passed then insert data
                if (isset($request->installment_data) && ! empty($request->installment_data)) {
                    foreach ($request->installment_data as $data) {
                        $paid_installment_id[] = PaidInstallmentFee::insertGetId([
                            'class_id' => $class_id,
                            'student_id' => $student->id,
                            'parent_id' => $student->father_id ?? $student->guardian_id,
                            'installment_fee_id' => $data['id'],
                            'session_year_id' => $session_year_id,
                            'amount' => $data['amount'],
                            'due_charges' => $data['due_charges'] ?? null,
                            'date' => date('Y-m-d'),
                            'payment_transaction_id' => $payment_transaction_db->id,
                            'status' => 0
                        ]);
                    }
                }

                $payment_gateway_details = array(
                    'payment_transaction_id' => $payment_transaction_db->id,
                    'is_fully_paid' => $request->is_fully_paid,
                    'amount' => $request->amount * 100,
                    'currency' => $currency_code,
                    'email' => $email,
                    'optional_fees_id' => $optional_fees_id ?? [],
                    'paid_installment_id' => $paid_installment_id ?? []
                );
            }
            //validating the enable of gateways ..
            if ($razorpay_status == 0 && $stripe_status == 0 && $paystack_status == 0) {
                return response()->json([
                    'error' => true,
                    'message' => 'Please enable the payment gateway in panel',
                    'code' => 404,
                ]);
            }
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully'),
                'payment_gateway_details' => $payment_gateway_details,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    // add the transaction data in transaction table
    public function storeFees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'payment_id' => 'nullable',
            'payment_signature' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ]);
        }
        try {
            // Updating the Payment Transaction
            $transaction_db = PaymentTransaction::findOrFail($request->transaction_id);
            if ($request->payment_id) {
                $transaction_db->payment_id = $request->payment_id;
            }
            if ($request->payment_signature) {
                $transaction_db->payment_signature = $request->payment_signature;
            }
            $transaction_db->save();
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully'),
                'code' => 200
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103
            ];
        }
        return response()->json($response);
    }

    //get the fees paid list
    public function feesPaidList(Request $request)
    {
        try {
            $student = $request->user()->student;

            $fees_paid = FeesPaid::where(['student_id' => $student->id])->with('session_year:id,name', 'class.medium')->get();

            $response = [
                'error' => false,
                'data' => $fees_paid,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);

            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    //Generate The Reciept
    public function feesPaidReceiptPDF(Request $request)
    {
        try {
            $student = $request->user()->student;

            $logo = loadTenantMainAsset('logo2');
            // $logo = public_path("/storage/{$logo}");
            $school_name = settingByType('school_name');
            $school_address = getSettings('school_address');
            $school_address = $school_address['school_address'];

            $currency_symbol = getSettings('currency_symbol');
            if (isset($currency_symbol) && sizeof($currency_symbol)) {
                $currency_symbol = $currency_symbol['currency_symbol'];
            } else {
                $currency_symbol = null;
            }

            //Getting the Fees Paid Data
            $fees_paid = FeesPaid::where('student_id', $student->id)->with('student.user:id,first_name,last_name', 'class', 'session_year')->first();

            // Check That Fees Paid Data Exists Or Not
            if (! $fees_paid) {
                return response()->json([
                    'error' => true,
                    'message' => "No Fees Paid Found",
                ]);
            }

            // Variables
            $student_id = $fees_paid->student_id;
            $class_id = $fees_paid->class_id;
            $session_year_id = $fees_paid->session_year_id;


            // Paid Installment Data
            $paid_installment = PaidInstallmentFee::where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'status' => 1])->with('installment_fee')->get();

            //Fees Choiceable Data
            $optional_fees_type_id = FeesClass::where(['class_id' => $class_id, 'choiceable' => 1])->pluck('fees_type_id');
            $fees_choiceable = FeesChoiceable::whereIn('fees_type_id', $optional_fees_type_id)->where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id, 'status' => 1])->with('fees_type')->orderby('id', 'asc')->get();

            //Fees Class Data
            $fees_class = FeesClass::where(['class_id' => $class_id, 'choiceable' => 0])->with('fees_type')->get();

            //Session Year Data
            $session_year = SessionYear::where('id', $session_year_id)->first();

            //Load the HTML
            $pdf = Pdf::loadView('fees.fees_receipt', compact('logo', 'school_name', 'fees_paid', 'paid_installment', 'fees_choiceable', 'currency_symbol', 'school_address', 'fees_class', 'session_year'));

            //Get The Output Of PDF
            $output = $pdf->output();

            $response = [
                'error' => false,
                'pdf' => base64_encode($output),
            ];
        } catch (Throwable $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
            );
        }
        return response()->json($response);
    }

    public function getFeesPaymentTransactions(Request $request)
    {
        try {

            $student = $request->user()->student;
            // $parent_id = Auth::user()->parent->id;
            // $child_id = Students::where('father_id',$parent_id)->orWhere('mother_id',$parent_id)->orWhere('guardian_id',$parent_id)->pluck('id');
            $fees_payment_transactions = PaymentTransaction::where('student_id', $student->id)->with('session_year')->with([
                'student' => function ($q) {
                    $q->select('id', 'user_id')->with('user:id,first_name,last_name');
                }
            ])->orderBy('id', 'desc')->paginate(15);

            foreach ($fees_payment_transactions as $transaction) {
                $date = $transaction->date;
                if ($date) {
                    $datetime = Carbon::parse($date);
                    $time = $datetime->format('H:i:s');
                    $addHour = $datetime->copy()->addHour();
                    if (Carbon::now()->gt($addHour)) {
                        $transaction->payment_status = 0;
                        $transaction->save();
                    }
                }
            }
            $fees_payment_transactions = $fees_payment_transactions->toArray();
            $response = array(
                'error' => false,
                'data' => array(
                    'current_page' => $fees_payment_transactions['current_page'],
                    'transaction-data' => $fees_payment_transactions['data'],
                    'from' => $fees_payment_transactions['from'],
                    'last_page' => $fees_payment_transactions['last_page'],
                    'per_page' => $fees_payment_transactions['per_page'],
                    'to' => $fees_payment_transactions['to'],
                    'total' => $fees_payment_transactions['total'],
                ),
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    // Make Transaction Fail API
    public function failPaymentTransactionStatus(Request $request)
    {
        try {
            $update_status = PaymentTransaction::findOrFail($request->payment_transaction_id);
            $total_amount = $update_status->total_amount;
            $update_status->payment_status = 0;
            $update_status->save();

            $student = $request->user()->student;

            $user[] = $student->user_id;
            $body = 'Amount :- ' . $total_amount;
            $type = 'online';
            $image = null;
            $userinfo = null;

            $notification = new Notification();
            $notification->send_to = 2;
            $notification->title = 'Payment Failed';
            $notification->message = $body;
            $notification->type = $type;
            $notification->date = Carbon::now();
            $notification->is_custom = 0;
            $notification->save();
            foreach ($user as $data) {
                $user_notification = new UserNotification();
                $user_notification->notification_id = $notification->id;
                $user_notification->user_id = $data;
                $user_notification->save();
            }

            send_notification($user, 'Payment Failed', $body, $type, $image, $userinfo);
            $response = array(
                'error' => false,
                'message' => 'Data Updated Successfully',
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $settings = getSettings();

            if (isset($settings['razorpay_status']) && $settings['razorpay_status']) {
                $secretkey = $settings['razorpay_secret_key'] ?? "";
            }

            if (isset($settings['stripe_status']) && $settings['stripe_status']) {
                $secretkey = $settings['stripe_secret_key'] ?? "";
            }

            $url = 'https://api.stripe.com/v1/payment_intents/' . $request->payment_intent_id;

            $payment_status = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretkey,
            ])->get($url);

            $data = $payment_status['status'];

            $response = array(
                'error' => false,
                'data' => $data,
                'code' => 200,
            );
        } catch (Exception $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function sendFeeNotification(Request $request)
    {
        try {
            $student = $request->user()->student;

            $parent = [
                $student->father_id ?? '',
                $student->mother_id ?? '',
                $student->guardian_id ?? '',
            ];

            $user = Parents::whereIn('id', $parent)->pluck('user_id');

            $title = 'Fees are dues';
            $body = $student->user->first_name . ' ' . $student->user->last_name . ' ' . 'wants to keep learning! Please pay the overdue fees as soon as possible.';
            $type = "fees-due";
            $image = null;
            $userinfo = (string) $student->id;

            $notification = new Notification();
            $notification->send_to = 2;
            $notification->title = $title;
            $notification->message = $body;
            $notification->type = $type;
            $notification->date = Carbon::now();
            $notification->is_custom = 0;
            $notification->save();

            send_notification($user, $title, $body, $type, $image, $userinfo);

            $response = array(
                'error' => false,
                'message' => 'Notification Send Successfully',
                'code' => 200,
            );

        } catch (Throwable $e) {
            report($e);

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }
    public function enrollFreeLesson(
        Request $request
    ) {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
        ]);
        if ($validator->fails()) {
            $response = [
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ];
            return response()->json($response);
        }
        try {
            $user = $request->user();
            $lesson = Lesson::find($request->lesson_id);

            if ($this->purchaseService->isLessonAlreadyEnrolled($lesson, $user->id)) {
                return response()->json([
                    'error' => true,
                    'message' => trans('lesson_already_enrolled'),
                    'code' => 104,
                ]);
            }
            if (! $lesson->isFree()) {
                return response()->json([
                    'error' => true,
                    'message' => trans('lesson_is_not_free'),
                ]);
            }
            $this->purchaseService->enrollLesson($lesson, $user->id);

            $response = [
                'error' => false,
                'message' => trans('successfully_lesson_enrolled'),
                'code' => 200,
            ];

        } catch (Exception $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);

    }
    public function redeemCouponForLesson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
            'coupon_code' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ];
            return response()->json($response);
        }
        try {

            $user = $request->user();
            $lesson = Lesson::find($request->lesson_id);

            if ($this->purchaseService->isLessonAlreadyEnrolled($lesson, $user->id)) {
                return response()->json([
                    'error' => true,
                    'message' => trans('lesson_already_enrolled'),
                    'code' => 104,
                ]);
            }
            $coupon = $this->couponService->redeemCoupon($user, $request->coupon_code, $lesson);
            if (! $coupon['status']) {
                $response = [
                    'error' => true,
                    'message' => $coupon['message'],
                    'code' => 107,
                ];
            } else {
                $this->purchaseService->enrollLesson($lesson, $user->id);
                $response = [
                    'error' => false,
                    'message' => $coupon['message'],
                    'code' => 200,
                ];
            }

        } catch (Exception $e) {
            report($e);
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }
    private function answerFormat($choiceQuestions, $exam_attempts, $correct_data, $questionChoiceAnswers, $isCorrect = false)
    {
        return [
            'is_correct' => $isCorrect,

            'student_answer' => $exam_attempts->firstWhere('question.id', $correct_data->question_id)?->only(
                'option_id',
                'question_id',
                'id',
                'submitted_date'
            ),
            'question' => $choiceQuestions->questions->only(
                'id',
                'question_type',
                'question',
                'image_url',
                'note',
                'explain_answer',
                'options'
            ),
            'correct_answers' => $questionChoiceAnswers,
            // ------------------------------------------------ \\
            'question_id' => $choiceQuestions->id,
            'marks' => $choiceQuestions->marks
        ];
    }
}
