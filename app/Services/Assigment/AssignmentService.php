<?php

namespace App\Services\Assigment;

use App\Models\File;
use App\Models\Teacher;
use App\Models\Assignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Students;

class AssignmentService
{
    public function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
        ]);

        if ($validator->fails()) {
            return [
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            ];
        }

        return null;
    }
    public function getStudentAssignmentSubmission(Assignment $assignment, Students $student)
    {
        $assignment->load([
            'submission' => fn($q) => $q->where('student_id', $student->id),
        ]);
        return $assignment->submission;
    }

    public function getAssignment(Request $request)
    {
        $student = $request->user()->student;
        return Assignment::where('id', $request->assignment_id)
            ->where('class_section_id', $student->class_section_id)
            ->firstOrFail();
    }

    public function handleAssignmentSubmission($assignment, $student, $session_year_id)
    {
        $assignment_submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if (empty($assignment_submission)) {
            $assignment_submission = new AssignmentSubmission();
            $assignment_submission->assignment_id = $assignment->id;
            $assignment_submission->student_id = $student->id;
            $assignment_submission->session_year_id = $session_year_id;
        } elseif ($assignment_submission->status == 2 && $assignment->resubmission) {
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
            return [
                'error' => true,
                'message' => "You already have submitted your assignment.",
                'code' => 104
            ];
        }

        return $assignment_submission;
    }

    public function notifyTeachers($assignment, $student)
    {
        $subject_teacher_ids = SubjectTeacher::where('class_section_id', $student->class_section_id)
            ->where('subject_id', $assignment->subject_id)
            ->pluck('teacher_id');

        $user_ids = Teacher::whereIn('id', $subject_teacher_ids)->pluck('user_id');

        $notification = Notification::create([
            'send_to' => 2,
            'title' => 'New submission',
            'message' => $student->user->full_name . ' submitted their assignment.',
            'type' => 'assignment_submission',
            'date' => Carbon::now(),
            'is_custom' => 0,
        ]);


        foreach ($user_ids as $user_id) {
            UserNotification::create([
                'notification_id' => $notification->id,
                'user_id' => $user_id,
            ]);
        }

        send_notification($user_ids, $notification->title, $notification->message, $notification->type, null, null);
    }

    public function saveFiles(Request $request, $assignment_submission)
    {
        foreach ($request->file('files') as $image) {
            $file = new File();
            $file->file_name = $image->hashName();
            $file->modal()->associate($assignment_submission);
            $file->type = File::FILE_UPLOAD_TYPE;
            $file->file_url = $image->store('assignment', 'public');
            $file->save();
        }
    }
}
