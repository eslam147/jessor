<?php

namespace App\Http\Resources\Student\Teacher;

use App\Http\Resources\Student\Lesson\LessonResource;
use App\Http\Resources\Student\Subject\SubjectResource;
use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    private $subjectLessons = null;
    public function withLessons($lessons)
    {
        $this->subjectLessons = $lessons;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'gender' => $this->user->gender,
            'image' => $this->when($this->user->image, asset($this->user->image)),
            'dob' => $this->user->dob,
            // ------------------------------------------
            'lessons_count' => $this->lessons_count,
            'topics_count' => $this->lesson_topics_count,
            // ------------------------------------------
        ];
    }
}
