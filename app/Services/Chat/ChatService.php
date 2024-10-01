<?php
namespace App\Services\Chat;

use Exception;
use App\Models\ClassSubject;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Parents;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ChatFile;
use App\Models\Students;
use App\Dtos\Chat\ChatDto;
use App\Models\ChatMessage;
use App\Models\ReadMessage;
use App\Models\ClassTeacher;
use App\Models\SubjectTeacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    public function getChatUserList()
    {
        return $this->getTeacherChat();
    }
    public function getTeacherChat()
    {
        $user_type = request('isParent', 0);
        // $search = request('search', 0);

        // $session_year = getSettings('session_year');
        // $subject_teacher_section_ids = [];
        // $class_teacher_section_ids = [];

        $teacher = auth()->user()->teacher;

        $class_section_ids = ClassTeacher::with('class_section')->where('class_teacher_id', $teacher->id)->pluck('class_section_id')->toArray();
        $total_items = 0;
        $totalunreadusers = 0;
        // $subject_teachers = SubjectTeacher::with('class_section')->where('teacher_id', $teacher->id)->whereNotIn('class_section_id', $class_section_ids)->groupBy('class_section_id')->get();
        $data = [];

        if ($class_section_ids) {
            $chatMessagesTable = (new ChatMessage)->getTable();
            $students = Students::with('user', 'class_section.class', 'student_subjects.subject')
                ->whereIn('class_section_id', $class_section_ids)
                ->orderByRaw(
                    "(SELECT COUNT(*) 
                      FROM {$chatMessagesTable} AS messages
                      WHERE (messages.modal_id = students.user_id AND messages.sender_id = ?) OR (messages.modal_id = ? AND messages.sender_id = students.user_id)) DESC",
                    [Auth::user()->id, Auth::user()->id]
                )
                ->orderByRaw(
                    "(SELECT MAX(messages.id)
                      FROM {$chatMessagesTable} AS messages
                      WHERE (messages.modal_id = students.user_id AND messages.sender_id = ?) 
                         OR (messages.modal_id = ? AND messages.sender_id = students.user_id)) DESC",
                    [Auth::user()->id, Auth::user()->id]
                )->orderByDesc('id')
                ->paginate();

            $studentIds = $students->pluck('user_id')->toArray();

            $chatMessages = ChatMessage::where(function ($query) use ($studentIds, $teacher) {
                $query->whereIn('modal_id', $studentIds)
                    ->where('sender_id', $teacher->user->id);
            })->orWhere(function ($query) use ($studentIds, $teacher) {
                $query->where('modal_id', $teacher->user->id)
                    ->whereIn('sender_id', $studentIds);
            })->latest('id')
                ->get();

            $lastReadMessages = ReadMessage::where('modal_id', $teacher->user->id)
                ->whereIn('user_id', $students->pluck('user_id'))
                ->get();

            foreach ($students as $student) {
                $unreadCount = 0;
                if ($student->user_id != 0) {
                    $lastMessage = $chatMessages
                        ->filter(
                            function ($item) use ($student) {
                                return $item->modal_id == $student->user?->id || $item->sender_id == $student->user?->id;
                            }
                        )->last();

                    $lastReadMessage = $lastReadMessages
                        ->where('modal_id', $teacher->user->id)
                        ->where('user_id', $student->user_id)
                        ->first();

                    if ($lastReadMessage) {
                        $lastReadMessageId = $lastReadMessage->last_read_message_id;
                        if ($lastReadMessageId) {
                            $unreadCount = $chatMessages->where('sender_id', $student->user_id)
                                ->where('id', '>', $lastReadMessageId)
                                ->count();
                        }
                    }

                    // $student_subject = $student->subjects;

                    // $core_subjects = array_column($student_subject["core_subject"], 'subject_id');

                    // $elective_subjects = $student_subject["elective_subject"] ?? [];
                    // if ($elective_subjects) {
                    //     $elective_subjects = $elective_subjects->pluck('subject_id')->toArray();
                    // }
                    // $subject_id = array_merge($core_subjects, $elective_subjects);

                    // $subjects = Subject::whereIn('id', $subject_id)->select('id', 'name')->get(['id', 'name'])->toArray();
                    $studentUser = optional($student->user);
                    $data[] = [
                        'id' => $student->id,
                        'user_id' => $student->user_id,
                        'first_name' => $studentUser->first_name ?? '',
                        'last_name' => $studentUser->last_name ?? '',
                        'image' => $studentUser->image ?? '',
                        'gender' => $studentUser->gender,
                        'dob' => $studentUser->dob,
                        // 'roll_no' => $student->roll_number,
                        // 'admission_no' => $student->admission_no,
                        // 'subjects' => $subjects,
                        // 'address' => $studentUser->current_address,
                        'last_message' => $lastMessage ?? null,
                        'class_name' => $student->class_section?->class?->name . ' ' . $student->class_section?->section?->name . ' ' . $student->class_section?->class?->medium?->name,
                        // 'isParent' => $user_type,
                        'unread_message' => $unreadCount ?? 0
                    ];
                }
            }

            $total_items = count($data) ?? 0;
            // $undata = ;
            $totalunreadusers = count(array_filter($data, fn($user) => $user['unread_message'] > 0));

            $data = collect($data)->sortByDesc(function ($user) {
                return optional($user['last_message'])?->date ? $user['last_message']->date->timestamp : 0;
            })->sortByDesc('unread_message')
                ->values();

        }
        return [
            'items' => $data,
            'total_items' => $total_items,
            'total_unread_users' => $totalunreadusers,
        ];
    }

    public function sendMessage(ChatDto $chatDto)
    {
        try {

            DB::beginTransaction();
            $message = ChatMessage::create([
                'modal_id' => $chatDto->receiver->id,
                'modal_type' => User::class,
                'sender_id' => $chatDto->sender->id,
                'body' => $chatDto->message,
                'date' => now(),
            ]);

            $count = 0;
            $unreadCount = 0;

            if ($chatDto->files) {
                foreach ($chatDto->files as $uploadedFile) {
                    $file = ChatFile::create([
                        'file_type' => 1,
                        'message_id' => $message->id,
                        'file_name' => $uploadedFile->storeAs('chatfile', $uploadedFile->hashName(), 'public')
                    ]);
                    $count++;
                }
            }

            $readMessage = ReadMessage::where('modal_id', $chatDto->receiver->id)->where('user_id', $chatDto->sender->id)->first();
            if (empty($readMessage)) {
                $readMessage = ReadMessage::create([
                    'modal_id' => $chatDto->receiver->id,
                    'user_id' => $chatDto->sender->id,
                    'modal_type' => User::class
                ]);
            }

            $message = ChatMessage::with('file')->where('id', $message->id)->select('id', 'sender_id', 'body', 'date')->get();

            foreach ($message as $msg) {
                $chatfile = [];
                foreach ($msg->file as $file) {
                    if (! empty($file)) {
                        $chatfile[] = tenant_asset('storage/' . $file->file_name);
                    } else {
                        $chatfile[] = '';
                    }
                }

                $data = [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'body' => $msg->body,
                    'date' => $msg->date,
                    'files' => $chatfile
                ];
            }

            $teacher = Teacher::with('user', 'subjects.subject')->where('user_id', $chatDto->receiver->id)->first();

            $subjectData = [];

            if ($teacher) {
                foreach ($teacher->subjects as $subject) {
                    $subjectData[] = [
                        'id' => $subject->subject->id,
                        'name' => $subject->subject->name,
                    ];
                }
                $this->sendPushNotification(
                    teacher: $teacher,
                    receiverId: $chatDto->receiver->id,
                    filesCount: $count,
                    unreadCount: $unreadCount,
                    data: $data
                );
            }


            // $lastReadMessage = ReadMessage::where('modal_id', $chatDto->receiver->id)->where('user_id', $teacher->user_id)->first();

            // if ($lastReadMessage) {

            //     $lastReadMessageId = $lastReadMessage->last_read_message_id;
            //     if (! empty($lastReadMessageId)) {
            //         $unreadCount = ChatMessage::where('modal_id', $chatDto->receiver->id)->where('sender_id', $teacher->user_id)->where('id', '>', $lastReadMessageId)->count();
            //     } else {
            //         $unreadCount = ChatMessage::where('modal_id', $chatDto->receiver->id)->where('sender_id', $teacher->user_id)->count();
            //     }

            // }


            DB::commit();

            return [
                'error' => false,
                'message' => trans('message_sent_successfully'),
                'data' => $data,
                'code' => 200,
            ];
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'code' => 500,
            ];
        }

    }

    private function sendPushNotification($teacher, $receiverId, $filesCount, $unreadCount, $data)
    {
        $userinfo = (object) [
            'id' => $teacher->id,
            'user_id' => $teacher->user->id,
            'first_name' => $teacher->user->first_name,
            'last_name' => $teacher->user->last_name,
            'email' => $teacher->user->email,
            'qualification' => $teacher->qualification,
            'image' => $teacher->user->image,
            // 'subjects' => $subjectData,
            'last_message' => $data ?? null,
            'unread_message' => $unreadCount ?? 0
        ];

        $title = $teacher->user->full_name;
        $body = $request->message ?? "{$filesCount} Files Received";
        $type = "chat";
        $image = null;
        $user[] = $receiverId;

        return send_notification($user, $title, $body, $type, $image, $userinfo);
    }

    public function getUserChatMessage(User $user)
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 15);

        $searchIds = [$user->id, Auth::user()->id];

        $messages = ChatMessage::with('file:message_id,file_name')
            ->whereIn('modal_id', $searchIds)
            ->whereIn('sender_id', $searchIds)
            ->select('id', 'modal_id', 'sender_id', 'body', 'date');

        $total_items = $messages->count();

        $messages = $messages
            ->orderByDesc('date')
            ->limit($limit)->offset(($page - 1) * $limit)
            ->get()
            ->toArray();

        foreach ($messages as &$message) {
            $message['files'] = collect($message['file'])->map(function ($file) {
                return tenant_asset($file['file_name']);
            })->toArray();

            unset($message['file']);
        }
        $messages = array_reverse($messages);
        return [
            'error' => false,

            'message' => 'Data Fetched Successfully',
            'data' => [
                'items' => $messages ?? [],
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'image' => $user->image
                ],
                'total_items' => $total_items,
            ],
            'code' => 100,
        ];

    }

    public function readAllMessages(User $user)
    {
        $auth = Auth::id();
        $userId = $user->id;
        $message_id = null;
        $lastMessage = ChatMessage::select('id')->where('sender_id', $userId)
            ->latest('id')
            ->where('modal_id', $auth)->value('id');
        if ($lastMessage) {
            $message_id = $lastMessage;
        }


        // Update Read Message id
        $readMessage = ReadMessage::where('modal_id', $auth)
            ->where('user_id', $userId)
            ->first();

        if ($readMessage && $message_id) {
            $readMessage->update([
                'last_read_message_id' => $message_id
            ]);
        }

        return true;
    }
}
