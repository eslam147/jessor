<?php

namespace App\Http\Controllers\student;

use Exception;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\Assigment\AssignmentService;

class AssignmentController extends Controller
{
    public function __construct(
        private AssignmentService $assignmentService
    ) {
    }
    public function index(Request $request)
    {
        $student = auth()->user()->student;
        $student_subject = $student->subjects();
        // $class_subject = $student->classSubjects();

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

        // if ($request->assignment_id) {
        //     $data->where('id', $request->assignment_id);
        // }
        // if ($request->subject_id) {
        //     $data->where('subject_id', $request->subject_id);
        // }
        // if (isset($request->is_submitted)) {
        //     if ($request->is_submitted == 1) {
        //         $data->whereHas('submission', function ($q) use ($student) {
        //             $q->where('student_id', $student->id);
        //         });
        //     } else if ($request->is_submitted == 0) {
        //         $data->whereDoesntHave('submission', function ($q) use ($student) {
        //             $q->where('student_id', $student->id);
        //         });
        //     }
        // }
        $assignments = $data->orderByDesc('id')->paginate();

        return view('student_dashboard.assignments.index', compact('assignments'));
    }
    public function show(Assignment $assignment)
    {
        return abort(503, 'Not implemented');
        // dd(
        //     $assignment
        // );
        return view('student.assignments.show', compact('assignment'));
    }
    public function submit(Request $request, Assignment $assignment)
    {
        // dd(
        //     $assignment
        // );
        $validationResponse = $this->assignmentService->validateRequest($request);

        if ($validationResponse) {
            Alert::error('Error', $validationResponse['message']);
            return redirect()->back()->withErrors($validationResponse['message'])->withInput();
        }

        try {
            $student = $request->user()->student;
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $assignment_submission = $this->assignmentService->handleAssignmentSubmission($assignment, $student, $session_year_id);

            if (isset($assignment_submission['error'])) {
                Alert::error('Error', $assignment_submission['message']);

                return redirect()->back();
            }
            $this->assignmentService->notifyTeachers($assignment, $student);
            $assignment_submission->save();
            $this->assignmentService->saveFiles($request, $assignment_submission);
            Alert::success('Assignment Submitted', 'Assignment Submitted Successfully');
            return redirect()->back();
        } catch (Exception $e) {
            report($e);
            // throw $e;
            Alert::error('Error', trans('error_occurred'));
            return redirect()->back();
        }
    }

}
