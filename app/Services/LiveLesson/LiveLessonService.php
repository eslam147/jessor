<?php
namespace App\Services\LiveLesson;

use App\Models\LiveLesson;
use Illuminate\Support\Carbon;
use App\Enums\Lesson\LiveLessonStatus;
use App\Enums\PaymentStatus\PaymentStatus;
use App\Http\Requests\Dashboard\LiveLesson\LiveLessonRequest;

class LiveLessonService
{
  public function __construct(
    private readonly LiveLesson $liveLessonModel
  ) {
  }
  public function create(LiveLessonRequest $liveLessonRequest)
  {
    $paymentStatus = $liveLessonRequest->payment_status == 1 ? PaymentStatus::PAID : PaymentStatus::FREE;
    return $this->liveLessonModel->create([
      'subject_id' => $liveLessonRequest->subject_id,
      'class_section_id' => $liveLessonRequest->class_section_id,
      'teacher_id' => $liveLessonRequest->teacher_id,
      'status' => LiveLessonStatus::SCHEDULED,
      'description' => $liveLessonRequest->description,
      'name' => $liveLessonRequest->name,
      'duration' => $liveLessonRequest->session_duration,
      'session_start_at' => Carbon::parse($liveLessonRequest->session_date)->toDateTimeString(),
      'payment_status' => $paymentStatus,
      'price' => $paymentStatus->isPaid() ? $liveLessonRequest->price : null,
    ]);
  }
  public function update(LiveLessonRequest $liveLessonRequest, LiveLesson $liveLesson)
  {
    $paymentStatus = $liveLessonRequest->payment_status == 1 ? PaymentStatus::PAID : PaymentStatus::FREE;
    return $liveLesson->update([
      'subject_id' => $liveLessonRequest->subject_id,
      'class_section_id' => $liveLessonRequest->class_section_id,
      'status' => LiveLessonStatus::SCHEDULED,
      'description' => $liveLessonRequest->description,
      'name' => $liveLessonRequest->name,
      'duration' => $liveLessonRequest->session_duration,
      'session_start_at' => Carbon::parse($liveLessonRequest->session_date)->toDateTimeString(),
      'payment_status' => $paymentStatus,
      'price' => $paymentStatus->isPaid() ? $liveLessonRequest->price : null,
    ]);
  }
  public function createMeeting(LiveLesson $liveLesson, string $provider)
  {
    return $liveLesson->scheduleMeeting($provider)
      ->withTopic($liveLesson->name)
      ->startingAt($liveLesson->session_start_at)
      ->during($liveLesson->duration)
      ->withMetaAttributes([
        'password' => request('password')
      ])->save();
  }
}