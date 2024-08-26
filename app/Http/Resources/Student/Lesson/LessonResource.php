<?php

namespace App\Http\Resources\Student\Lesson;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Student\File\FileResource;
use App\Http\Resources\Student\ClassSchoolResource;
use App\Http\Resources\Student\Subject\SubjectResource;
use App\Http\Resources\Student\LessonTopic\LessonTopicResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'class' => new ClassSchoolResource($this->whenLoaded('class')),
            'subject' => (new SubjectResource($this->subject))->withoutLessons(),
            
            'files' => $this->when($this->is_enrolled, function () {
                return FileResource::collection($this->file);
            }, null),

            'is_enrolled' => ($this->is_enrolled ? true : false),
            'is_paid' => (!$this->is_paid ? true : false),

            'topics' => LessonTopicResource::collection($this->topic)
        ];
    }
}
