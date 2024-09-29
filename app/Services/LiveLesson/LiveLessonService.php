<?php
namespace App\Services\LiveLesson;

use App\Models\LiveLesson;
use Illuminate\Support\Carbon;
use App\Enums\Lesson\LiveLessonStatus;
use App\Factories\VideoConference\VideoConferenceFactory;
use App\Http\Requests\Dashboard\LiveLesson\LiveLessonRequest;

class LiveLessonService
{
  public function __construct(
    private readonly LiveLesson $liveLessonModel
  ) {
  }
  public function create(LiveLessonRequest $liveLessonRequest)
  {
    return $this->liveLessonModel->create([
      'subject_id' => $liveLessonRequest->subject_id,
      'class_section_id' => $liveLessonRequest->class_section_id,
      'teacher_id' => $liveLessonRequest->teacher_id,
      'status' => LiveLessonStatus::SCHEDULED,
      'description' => $liveLessonRequest->description,
      'name' => $liveLessonRequest->name,
      'duration' => $liveLessonRequest->session_duration,
      'session_date' => Carbon::parse($liveLessonRequest->session_date)->toDateTimeString(),
    ]);
  }
  public function update(LiveLessonRequest $liveLessonRequest, LiveLesson $liveLesson)
  {
    return $liveLesson->update([
      'subject_id' => $liveLessonRequest->subject_id,
      'class_section_id' => $liveLessonRequest->class_section_id,
      'status' => LiveLessonStatus::SCHEDULED,
      'description' => $liveLessonRequest->description,
      'name' => $liveLessonRequest->name,
      'duration' => $liveLessonRequest->session_duration,
      'session_date' => Carbon::parse($liveLessonRequest->session_date)->toDateTimeString(),
    ]);
  }
  public function createMeeting(LiveLesson $liveLesson, string $provider)
  {

    $videoService = VideoConferenceFactory::make($provider);

    return $videoService->createMeeting([
      "topicName" => $liveLesson->name,
      "startTime" => $liveLesson->session_date->toDateTimeString(),
      'password' => $liveLesson->password,
    ]);
  }
}